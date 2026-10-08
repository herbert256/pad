# PAD + islands

## Introduction

One runtime mounts components of any framework - React, Vue, Svelte, Preact, Solid - on the islands a PAD page writes; Vite builds them and {vite} links the build.

## Overview

| Page | Shows |
|------|-------|
| `examples/frameworks` | One counter in five frameworks on one page, each with its start value from PAD and its weight read from the manifest |
| `examples/loading` | `data-load` - at once, when the browser is idle, when the island scrolls into view - with a log of what came when |
| `examples/shared` | A React shelf, a Vue summary and a Svelte badge sharing one store, the cart kept by PAD in the session |
| `examples/typed` | A TypeScript island whose props are typed by `pad types` from the page's own data - `npm run types`, `npm run check` |
| `examples/build` | What `{vite 'src/main.js'}` wrote for the request, the manifest it read, and the dev server that takes over |

## The runtime

`_frontend/src/pad-islands.js` (ESM, in the build):

```js
islands({
  VueCounter: vue(() => import('./islands/Counter.vue')),      // the component and its framework,
  Shelf:      react(() => import('./islands/Shelf.react.jsx')) // both fetched when an island is due
});
```

- `islands(registry)` mounts a component on every `[data-island]` of the page, with the JSON of
  `data-props` as its props and what PAD rendered inside as `serverHtml`; `data-load` is
  `eager` (default), `idle` or `visible`.
- `react()`, `vue()`, `svelte()`, `preact()`, `solid()` pair a component loader with its
  framework's adapter (`_frontend/src/adapters/`) - each a module of its own, so a page without a
  Vue island loads no Vue.
- `store(name, initial)` is state every framework can subscribe to; `get(page, params)` and
  `post(page, body)` talk to PAD (the JSON of `$padExpose`, the CSRF token in a header);
  `mounted()` lists the islands that mounted, and each one is told as a `pad:island` event.

## Build

```bash
cd apps/islands/_frontend
npm install
npm run build      # www/islands/build/ with .vite/manifest.json - tracked in git
npm run dev        # the dev server; its address goes into www/islands/build/hot
npm run types      # pad types: src/types/pad.d.ts from the samples and JSON answers of the pages
npm run check      # tsc --noEmit: the TypeScript islands against those types
```

Each framework compiles its own files (`vite.config.js`): React `*.react.jsx`, Solid
`*.solid.jsx`, Vue `*.vue`, Svelte `*.svelte`; Preact needs no compiler - its components use
`htm`. `{vite 'src/main.js', react}` in `_inits.pad` links the build, or the dev server while
`build/hot` is there - with the React fast-refresh preamble. `PAD_ISLANDS_WWW` points the build
at `www/islands/` when the project is built from a copy elsewhere.

## Files

```
apps/islands/
├── _config/config.php     # $padCommon and $padTidy off, $padCsrf on, $padViteBuild
├── _data/                 # examples.json (the catalogue), products, nav
├── _lib/islands.php       # islandsData, islandsGroups, islandsExample, islandsCart, islandsWeight
├── _frontend/             # the Vite project: package.json, vite.config.js, src/
├── _inits.pad, _inits.php # the page: {vite 'src/main.js', react}, the menu
├── index, guide
└── examples/

www/islands/
├── islands.css            # the look, light and dark
└── build/                 # what npm run build wrote
```
