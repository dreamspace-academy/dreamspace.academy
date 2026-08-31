#!/usr/bin/env node
/**
 * Flattens the PHP include tree into static HTML for Cloudflare Pages.
 *
 * pages/*.php remain the source of truth; dist/ is generated and gitignored.
 * No dependencies - node:fs, node:path and node:http only.
 *
 *   node scripts/build.mjs           build into dist/
 *   node scripts/build.mjs --serve   build, serve dist/ on :8080, rebuild
 *                                    HTML when pages/ changes
 */

import { createServer } from 'node:http';
import { existsSync, readFileSync, readdirSync, statSync, watch } from 'node:fs';
import { cp, mkdir, rm, writeFile } from 'node:fs/promises';
import { basename, dirname, extname, join, relative, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';

const ROOT = resolve(dirname(fileURLToPath(import.meta.url)), '..');
const DIST = join(ROOT, 'dist');

// Overridable so the parity test can pin it against a PHP server.
export const BUILD_YEAR = process.env.BUILD_YEAR ?? String(new Date().getFullYear());

/* ---------------------------------------------------------------- config */

// A file is a page rather than a fragment if it pulls in the shared header.
// Classifying by "included by nothing" instead would wrongly emit six
// orphaned fragments as standalone, headerless pages.
const HEADER_MARKER = "include('0-0-header.php')";
const EXPECTED_PAGES = 40;

const ASSET_DIRS = ['media', 'script', 'style', 'files'];
const EXTRA_ENTRIES = [
  'files/dreamfungi/index.php',
  'files/story/index.php',
  'files/support/index.php',
];

// PHP executes an include even inside an HTML comment, so the content still
// ships. Exactly one such include exists; it is expanded faithfully and any
// new one fails the build.
const ALLOWED_COMMENTED_INCLUDE = {
  file: 'pages/1-0-index.php',
  target: '1-9-timeline.php',
};

/* --------------------------------------------------------------- regexes */

// Three include syntaxes are in use: no semicolon (67), semicolon (55), and
// semicolon with a doubled space (4). The `;?` and trailing `\s*` cover all
// three; requiring the semicolon would silently miss over half the site.
// PHP swallows the single newline directly after a closing `?>`. The
// trailing `\r?\n?` reproduces that, without which every include leaves a
// stray blank line and the output no longer matches PHP byte for byte.
const INCLUDE_RE = /<\?php\s+include\(\s*(['"])(.+?)\1\s*\)\s*;?\s*\?>\r?\n?/g;
const YEAR_RE = /<\?php\s+echo\s+date\('Y'\);?\s*\?>\r?\n?/g;
const PHP_TAG_RE = /<\?php/;

const ATTR_RE = /\b(href|src)="([^"]*)"/g;
// Leave alone anything with a scheme, protocol-relative, a bare fragment, or a
// root-absolute path. Without this the build corrupts external URLs such as
// facebook.com/profile.php?id=... and shanjeevan.oceanbio.me/pages/1-0-index.php
const SCHEME_RE = /^(?:[a-z][a-z0-9+.-]*:|\/\/|#|\/)/i;
// Only bare same-directory filenames, which is the shape of all 197 internal
// links. The character class excludes "/" deliberately.
const LOCAL_PHP_RE = /^([A-Za-z0-9._-]+)\.php((?:#|\?).*)?$/;

/* ------------------------------------------------------------- flattener */

function assertCommentedIncludes(rel, src) {
  for (const comment of src.matchAll(/<!--[\s\S]*?-->/g)) {
    for (const inc of comment[0].matchAll(INCLUDE_RE)) {
      const allowed =
        rel === ALLOWED_COMMENTED_INCLUDE.file &&
        inc[2] === ALLOWED_COMMENTED_INCLUDE.target;
      if (!allowed) {
        throw new Error(
          `commented-out include '${inc[2]}' in ${rel}: PHP runs these, so the ` +
            `content still ships. Remove the include itself, not just the markup.`,
        );
      }
    }
  }
}

function expand(abs, stack = []) {
  if (stack.includes(abs)) {
    const trail = [...stack, abs].map((p) => relative(ROOT, p)).join(' -> ');
    throw new Error(`include cycle: ${trail}`);
  }
  if (stack.length > 10) {
    throw new Error(`include nesting deeper than 10 at ${relative(ROOT, abs)}`);
  }

  const rel = relative(ROOT, abs);
  const src = readFileSync(abs, 'utf8');
  assertCommentedIncludes(rel, src);

  // Resolve against the including file's own directory. That single choice is
  // what makes files/story/index.php -> ../../pages/... work with no special case.
  return src.replace(INCLUDE_RE, (_match, _quote, target) => {
    const child = resolve(dirname(abs), target);
    if (!existsSync(child)) {
      throw new Error(`missing include '${target}' referenced by ${rel}`);
    }
    return expand(child, [...stack, abs]);
  });
}

export function rewriteLinks(html) {
  return html.replace(ATTR_RE, (whole, attr, value) => {
    if (SCHEME_RE.test(value)) return whole;
    const m = LOCAL_PHP_RE.exec(value);
    return m ? `${attr}="${m[1]}.html${m[2] ?? ''}"` : whole;
  });
}

export function render(abs) {
  const html = rewriteLinks(expand(abs)).replace(YEAR_RE, BUILD_YEAR);
  if (PHP_TAG_RE.test(html)) {
    throw new Error(`PHP survived the flattener in ${relative(ROOT, abs)}`);
  }
  return html;
}

/* ------------------------------------------------------------- discovery */

export function discover() {
  const dir = join(ROOT, 'pages');
  const php = readdirSync(dir).filter((f) => f.endsWith('.php')).sort();
  const pages = php.filter((f) =>
    readFileSync(join(dir, f), 'utf8').includes(HEADER_MARKER),
  );
  if (pages.length !== EXPECTED_PAGES) {
    throw new Error(
      `expected ${EXPECTED_PAGES} pages, found ${pages.length}. Every page must ` +
        `include 0-0-header.php. Update EXPECTED_PAGES if this is intended.`,
    );
  }
  return { php, pages };
}

/* ------------------------------------------------------------- redirects */

function redirectsFor(pages) {
  // padEnd only aligns; the explicit spaces guarantee the fields stay
  // separated when a path is longer than its column.
  const row = (from, to, code) => `${from.padEnd(50)} ${to.padEnd(38)} ${code}`;
  const out = [
    '# Generated by scripts/build.mjs. Do not edit by hand.',
    '',
    '# The PHP site served a 302 from / to the homepage. Keep it a 302 so the',
    '# homepage can move to / later without fighting hard-cached 301s.',
    row('/', '/pages/1-0-index', '302'),
    row('/index.php', '/pages/1-0-index', '301'),
    '',
    '# Indexed .php URLs, one line per page.',
  ];
  for (const f of pages) {
    out.push(row(`/pages/${f}`, `/pages/${f.slice(0, -4)}`, '301'));
  }
  out.push('', '# PHP directory indexes under /files/.');
  for (const e of EXTRA_ENTRIES) {
    const d = `/${dirname(e)}/`;
    out.push(row(`${d}index.php`, d, '301'));
  }
  return out.join('\n') + '\n';
}

/* ----------------------------------------------------------------- build */

function keepAsset(src) {
  const b = basename(src);
  return b !== '.DS_Store' && b !== 'Thumbs.db' && !b.endsWith('.php');
}

async function copyAssets() {
  for (const d of ASSET_DIRS) {
    await cp(join(ROOT, d), join(DIST, d), { recursive: true, filter: keepAsset });
  }
  // static/ holds _headers, 404.html and anything else served verbatim at the root.
  const staticDir = join(ROOT, 'static');
  if (existsSync(staticDir)) await cp(staticDir, DIST, { recursive: true });
}

async function writeHtml(pages) {
  await mkdir(join(DIST, 'pages'), { recursive: true });
  for (const f of pages) {
    const out = join(DIST, 'pages', `${f.slice(0, -4)}.html`);
    await writeFile(out, render(join(ROOT, 'pages', f)));
  }
  for (const e of EXTRA_ENTRIES) {
    const abs = join(ROOT, e);
    if (!existsSync(abs)) throw new Error(`missing entry point ${e}`);
    const out = join(DIST, dirname(e), 'index.html');
    await mkdir(dirname(out), { recursive: true });
    await writeFile(out, render(abs));
  }
  return pages.length + EXTRA_ENTRIES.length;
}

function audit() {
  const MAX_FILE = 25 * 1024 * 1024;
  const MAX_FILES = 20000;
  let files = 0;
  let html = 0;
  let biggest = { size: 0, path: '' };

  const walk = (dir) => {
    for (const e of readdirSync(dir, { withFileTypes: true })) {
      const p = join(dir, e.name);
      if (e.isDirectory()) {
        walk(p);
        continue;
      }
      files += 1;
      if (p.endsWith('.html')) html += 1;
      const { size } = statSync(p);
      if (size > biggest.size) biggest = { size, path: relative(DIST, p) };
    }
  };
  walk(DIST);

  if (biggest.size > MAX_FILE) {
    throw new Error(`${biggest.path} is over the 25 MiB Cloudflare per-file limit`);
  }
  if (files > MAX_FILES) {
    throw new Error(`${files} files is over the Cloudflare 20,000 per-deployment limit`);
  }
  return { files, html, biggest };
}

export async function build({ htmlOnly = false } = {}) {
  const started = Date.now();
  const { php, pages } = discover();

  if (!htmlOnly) {
    await rm(DIST, { recursive: true, force: true });
    await copyAssets();
  }
  const written = await writeHtml(pages);
  await writeFile(join(DIST, '_redirects'), redirectsFor(pages));

  const { files, html, biggest } = audit();
  const mb = (biggest.size / 1048576).toFixed(2);
  console.log(
    `  ${pages.length} pages + ${EXTRA_ENTRIES.length} entry points -> ${written} html\n` +
      `  ${php.length - pages.length} fragments inlined, none emitted\n` +
      `  dist/: ${files} files, ${html} html, largest ${mb} MB (${biggest.path})\n` +
      `  done in ${Date.now() - started} ms`,
  );
}

/* ------------------------------------------------------------ dev server */

const MIME = {
  '.html': 'text/html; charset=utf-8', '.css': 'text/css',
  '.js': 'text/javascript', '.json': 'application/json',
  '.svg': 'image/svg+xml', '.png': 'image/png', '.jpg': 'image/jpeg',
  '.jpeg': 'image/jpeg', '.jfif': 'image/jpeg', '.gif': 'image/gif',
  '.webp': 'image/webp', '.avif': 'image/avif', '.ico': 'image/x-icon',
  '.pdf': 'application/pdf', '.xml': 'application/xml',
  '.woff': 'font/woff', '.woff2': 'font/woff2', '.ttf': 'font/ttf',
  '.eot': 'application/vnd.ms-fontobject', '.map': 'application/json',
  '.txt': 'text/plain; charset=utf-8', '.swf': 'application/x-shockwave-flash',
};

function serve(port = 8080) {
  createServer((req, res) => {
    let p = decodeURIComponent(new URL(req.url, 'http://localhost').pathname);
    if (p === '/') p = '/pages/1-0-index.html'; // mirrors the _redirects root rule

    for (const candidate of [p, `${p}.html`, join(p, 'index.html')]) {
      const abs = join(DIST, candidate);
      if (abs.startsWith(DIST) && existsSync(abs) && statSync(abs).isFile()) {
        res.writeHead(200, {
          'content-type': MIME[extname(abs)] ?? 'application/octet-stream',
        });
        res.end(readFileSync(abs));
        return;
      }
    }
    const notFound = join(DIST, '404.html');
    res.writeHead(404, { 'content-type': 'text/html; charset=utf-8' });
    res.end(existsSync(notFound) ? readFileSync(notFound) : 'Not found');
  }).listen(port, () => {
    console.log(`  serving dist/ on http://localhost:${port}`);
  });
}

/* ------------------------------------------------------------------ main */

const isMain =
  process.argv[1] && resolve(process.argv[1]) === fileURLToPath(import.meta.url);

if (isMain) {
  try {
    await build();
  } catch (err) {
    console.error(`build failed: ${err.message}`);
    process.exit(1);
  }

  if (process.argv.includes('--serve')) {
    serve();
    let timer;
    watch(join(ROOT, 'pages'), () => {
      clearTimeout(timer);
      timer = setTimeout(() => {
        build({ htmlOnly: true }).catch((e) => console.error(`  ${e.message}`));
      }, 50);
    });
    console.log('  watching pages/ - hard refresh after a save');
  }
}
