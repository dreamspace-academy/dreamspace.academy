# www.dreamspace.academy

The website of DreamSpace Academy.

Pages are written as `.php` files so the header, menu bar and footer can be
shared through `include()`. A build step resolves those includes into static
HTML, and that is what gets deployed. PHP does not run in production.

## STRUCTURE

| Path | What it is |
|---|---|
| `pages/*.php` | Source. 40 pages, each including `0-0-header.php`, plus the fragments they pull in. |
| `media/` `style/` `script/` | Images, CSS and JavaScript, served as-is. |
| `files/` | PDFs and standalone apps. |
| `scripts/` | Build and verification. |
| `static/` | Copied verbatim to the deployment root. |
| `dist/` | Generated output. Never edit it, never commit it. |

## DEVELOPMENT

Requires [Node.js](https://nodejs.org/) 22 or newer, Git, and a code editor.

```
npm install
npm run dev
```

`npm run dev` builds the site and serves it at http://localhost:8080, watching
`pages/` and rebuilding on save. Hard refresh to see a change.

```
npm run build    build into dist/
npm run check    verify the build
```

`npm run check` renders every page through a local PHP server and confirms the
static output matches it byte for byte, then checks every internal link and
image reference. Run it before opening a pull request.

Live Server cannot preview this project. It serves files but does not execute
PHP, so a `.php` file opened through it shows the raw include statements. Use
`npm run dev`, or point Live Server at `dist/` after `npm run build`.

### Adding a page

A page must include the header, or the build treats it as a fragment and will
not emit it:

```php
<?php include('0-0-header.php'); ?>
...
<?php include('9-0-footer.php'); ?>
```

The build asserts the page count, so adding a page also means updating
`EXPECTED_PAGES` in `scripts/build.mjs`. That is deliberate: it stops a
header-less page slipping through unnoticed.

Link between pages with the bare filename, `<a href="2-0-about.php">`. The
build rewrites these to `.html`.

## BRANCHES

| Branch | Purpose | Deploys to |
|---|---|---|
| `master` | Production | https://dreamspace.academy |
| `dev` | New features and fixes | https://dev.dreamspace.academy |

Both build and deploy automatically on push, and a push to `dev` also gets its
own preview URL for that commit.

Work on `dev`, then open a pull request into `master`.
