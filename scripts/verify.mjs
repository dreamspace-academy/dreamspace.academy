#!/usr/bin/env node
/**
 * Verifies the static build.
 *
 *   node scripts/verify.mjs             parity (when php is present) + links
 *   node scripts/verify.mjs --parity    parity only
 *   node scripts/verify.mjs --links     links and assets only
 *
 * Parity renders every entry point through a throwaway PHP server and diffs it
 * against dist/. It imports the build's own rewriter rather than
 * reimplementing it, so what is actually under test is the include expansion -
 * the part that could silently lose content.
 */

import { spawn, spawnSync } from 'node:child_process';
import { existsSync, readFileSync, readdirSync } from 'node:fs';
import { dirname, join, relative, resolve, sep } from 'node:path';
import { fileURLToPath } from 'node:url';

import { discover, rewriteLinks } from './build.mjs';

const ROOT = resolve(dirname(fileURLToPath(import.meta.url)), '..');
const DIST = join(ROOT, 'dist');
const PORT = 8199;

const EXTRA_ENTRIES = ['dreamfungi', 'story', 'support'];

// Every one of these already returns 404 on the live PHP site, verified by
// request. They are content bugs with their own fixes, so they are reported
// but must not fail the build - otherwise every deploy is blocked on unrelated
// editorial work. Remove an entry as its fix lands.
const KNOWN_BROKEN = new Set([
  // page never existed
  'pages/3-1-0-maker-education.html',
  // numbered past the images that exist (-1, and -0/-1 respectively)
  'media/gallery/lab-art-dreamspace-7.jpg',
  'media/gallery/lab-business-dreamspace-4.jpg',
  // real file is Govarthenan-Rajadurai.jpg
  'media/people/govarthenan-rajadurai-dreamspace.jpg',
  // real files are Rathees-Koneswaran.jpg and rathees-koneswaran-dreamspace.jpg
  'media/people/Rathees.jpg',
  // schemeless href in the unlinked map app; link text reads guna.com
  'files/map/kuna.com',
]);

/* ---------------------------------------------------------------- parity */

const sleep = (ms) => new Promise((r) => setTimeout(r, ms));

async function waitForServer(url, tries = 50) {
  for (let i = 0; i < tries; i += 1) {
    try {
      await fetch(url);
      return true;
    } catch {
      await sleep(100);
    }
  }
  return false;
}

async function parity() {
  const php = spawn('php', ['-S', `127.0.0.1:${PORT}`, '-t', ROOT], { stdio: 'ignore' });
  const fails = [];
  try {
    if (!(await waitForServer(`http://127.0.0.1:${PORT}/pages/1-0-index.php`))) {
      throw new Error('the PHP server never came up');
    }

    const { pages } = discover();
    const compare = async (url, built, label) => {
      const live = rewriteLinks(await (await fetch(url)).text());
      const have = readFileSync(built, 'utf8');
      if (live !== have) {
        fails.push({ label, live: live.length, have: have.length });
      }
    };

    for (const f of pages) {
      await compare(
        `http://127.0.0.1:${PORT}/pages/${f}`,
        join(DIST, 'pages', `${f.slice(0, -4)}.html`),
        `pages/${f}`,
      );
    }
    for (const d of EXTRA_ENTRIES) {
      await compare(
        `http://127.0.0.1:${PORT}/files/${d}/`,
        join(DIST, 'files', d, 'index.html'),
        `files/${d}/index.php`,
      );
    }
    return { total: pages.length + EXTRA_ENTRIES.length, fails };
  } finally {
    php.kill();
  }
}

/* ----------------------------------------------------------------- links */

// readdir is cached because the walk asks about the same directories
// repeatedly - roughly a thousand lookups across a hundred directories.
const dirCache = new Map();

function entriesOf(dir) {
  let set = dirCache.get(dir);
  if (!set) {
    try {
      set = new Set(readdirSync(dir));
    } catch {
      set = new Set();
    }
    dirCache.set(dir, set);
  }
  return set;
}

// existsSync is useless here: macOS is case-insensitive and will happily
// confirm a file that Cloudflare's case-sensitive storage would 404. Comparing
// each path segment against its parent listing is what actually catches it.
function existsExactCase(abs) {
  const rel = relative(DIST, abs);
  if (rel === '') return true;
  if (rel.startsWith('..')) return false;

  let cur = DIST;
  for (const segment of rel.split(sep)) {
    if (!entriesOf(cur).has(segment)) return false;
    cur = join(cur, segment);
  }
  return true;
}

function htmlFiles(dir, out = []) {
  for (const e of readdirSync(dir, { withFileTypes: true })) {
    const p = join(dir, e.name);
    if (e.isDirectory()) htmlFiles(p, out);
    else if (p.endsWith('.html')) out.push(p);
  }
  return out;
}

const EXTERNAL = /^(?:[a-z][a-z0-9+.-]*:|\/\/|#|$)/i;
const ATTR = /\b(?:href|src)="([^"]*)"/g;

function checkLinks() {
  const errors = [];
  const warnings = [];
  let checked = 0;

  for (const file of htmlFiles(DIST)) {
    // Commented-out markup is not live, so it should not be checked.
    const src = readFileSync(file, 'utf8').replace(/<!--[\s\S]*?-->/g, '');
    const from = relative(DIST, file);

    for (const [, raw] of src.matchAll(ATTR)) {
      if (EXTERNAL.test(raw)) continue;

      const [path] = raw.split(/[#?]/);
      if (!path) continue;

      let decoded = path;
      try {
        decoded = decodeURIComponent(path);
      } catch {
        // A malformed escape is left as-is; the existence check will report it.
      }

      // Relative URLs resolve against the directory the document is served
      // from, which for every page here is its own directory in dist/.
      const abs = decoded.startsWith('/')
        ? join(DIST, decoded)
        : resolve(dirname(file), decoded);

      checked += 1;
      if (existsExactCase(abs)) continue;

      const target = relative(DIST, abs);
      const entry = `${from}  ->  ${raw}`;
      if (KNOWN_BROKEN.has(target)) warnings.push(entry);
      else errors.push(entry);
    }
  }
  return { checked, errors, warnings };
}

/* ------------------------------------------------------------------ main */

const args = process.argv.slice(2);
const wantParity = args.length === 0 || args.includes('--parity');
const wantLinks = args.length === 0 || args.includes('--links');

if (!existsSync(DIST)) {
  console.error('dist/ not found - run `npm run build` first');
  process.exit(1);
}

let failed = false;

if (wantParity) {
  const havePhp = spawnSync('php', ['-v'], { stdio: 'ignore' }).status === 0;
  if (!havePhp) {
    console.log('parity: skipped, php is not installed');
  } else {
    const { total, fails } = await parity();
    if (fails.length === 0) {
      console.log(`parity: ${total}/${total} byte-identical to the PHP rendering`);
    } else {
      failed = true;
      console.log(`parity: ${total - fails.length}/${total} identical, ${fails.length} DIFFER`);
      for (const f of fails) {
        console.log(`  ${f.label}  php ${f.live}B vs dist ${f.have}B`);
      }
      console.log('  if only the footer year differs, rebuild - dist/ is stale');
    }
  }
}

if (wantLinks) {
  const { checked, errors, warnings } = checkLinks();
  console.log(`links: ${checked} internal references checked`);

  if (warnings.length) {
    console.log(`  ${warnings.length} pre-existing, already broken on the live site:`);
    for (const w of warnings) console.log(`    ${w}`);
  }
  if (errors.length) {
    failed = true;
    console.log(`  ${errors.length} BROKEN:`);
    for (const e of errors) console.log(`    ${e}`);
  } else {
    console.log('  no new breakage');
  }
}

process.exit(failed ? 1 : 0);
