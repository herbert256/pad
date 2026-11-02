# PAD + React Client Application

## Introduction

Demonstrates how to combine PAD (server-side template engine) with React (client-side JavaScript framework).

## Overview

PAD builds the page on the server - the data, the HTML, the routing by file name. React takes
over the parts that move: islands in the page, each handed its first data by PAD and talking
back to PAD while it runs. Fourteen examples, each a page with the live component on top and
its template, PHP and component underneath (coloured on the server by the `{widget:source}` tag).

- `?index` - the home page: what goes where, and an island that asks the server again
- `?patterns` - every example, grouped
- `?guide` - building an island of your own, and the runtime's calls

The pages stand in the frame the PAD applications share (`{common:padFrame}`, as in the
sections application): the brand and the menu - these three pages and the examples under the
heading of their group - in the side, and in the bar the page's title and the theme
switcher. The colours of the pages and the components are the frame's, so every
theme colours them too.

## The examples

**Server to React** - the data is in the HTML before the first paint.

| Page | Shows |
|------|-------|
| `examples/props` | Islands with props: `data-props="{^field}"`, one island per row of a PAD loop |
| `examples/enhance` | Progressive enhancement: a complete PAD table that React takes over |
| `examples/products` | A custom tag (`_tags/json.php`) writing `_data/products.json` into an attribute |
| `examples/topic` | `{widget:reactData}` with four providers in `_providers/` querying the database |

**React back to PAD** - the component asks the server while it runs.

| Page | Shows |
|------|-------|
| `examples/search` | One page, two answers: HTML for the browser, JSON for `fetch()` through `$padExpose` |
| `examples/feedback` | A React form checked by `padValidate`, posted with the CSRF token in a header |
| `examples/cart` | Two islands sharing a cart kept in the PHP session |
| `examples/chart` | React holds the controls, PAD draws the `{chart:bar}` and answers one `{pad:fragment}` |
| `examples/live` | Polling a page's JSON, paused while the tab is hidden |

**React basics** - `examples/counter`, `components`, `form`, `toggle`, `click`.

## How it is put together

```
apps/react/
├── _config/config.php     # $padCommon off, $padCsrf on
├── .env                   # the support database of the topic example
├── _inits.pad, _inits.php # the shared frame: React, the runtime, the menu
├── _data/                 # examples.json (the catalogue - and the menu), products, team, sales
├── _lib/react.php         # reactData, reactGroups, reactExample, reactCart, reactText
├── _tags/json.php         # {app:json 'products'/} - a _data file for an attribute
├── _providers/            # the {widget:reactData/} providers of the topic example
├── _guide/                # the sample files the guide shows
├── index, patterns, guide # the three pages on top of the menu
└── examples/
    ├── _inits.php/.pad    # the example's header, from _data/examples.json
    ├── _exits.pad         # its sources and the links to the next one
    └── props.pad ...      # one page per example - its .php when it needs one

www/react/
├── react.css              # what stands in the pages, in the frame's colours
├── pad-react.js           # the runtime - plain JavaScript
├── ui.js                  # shared pieces: RequestLog, Stars, Avatar, CountUp, useToast
├── index.js               # the home page's island
└── examples/<page>.js     # the component of each example, named after its page
```

A component lives in `www/`, never in a template: PAD reads every brace of a template, and a
file the web server hands out as it is never meets the parser. React 18 and Babel come from
unpkg.com; Babel turns the JSX into JavaScript in the browser, so there is no build step.

## The runtime

`www/react/pad-react.js` is loaded on every page, before Babel:

| Call | What it does |
|------|--------------|
| `PadReact.island(name, Component)` | Renders the component on every `[data-island=name]`, the JSON of `data-props` as props (a list arrives as `props.data`), what PAD rendered inside as `props.serverHtml` |
| `PadReact.get(page, params)` | `?page&...&padFormat=json` - the variables the page names in `$padExpose` |
| `PadReact.post(page, body, { token })` | A form-encoded post with the `X-CSRF-Token` header from `<meta name="csrf-token">` |
| `PadReact.fragment(page, name, params)` | One `{pad:fragment}` of the page, as HTML |
| `PadReact.useData(page, params, first)` | A hook: the page's data, asked again when the params change |
| `PadReact.useDebounced(value, ms)` | The value once it stood still - for a search box |

It also puts the version of `www/react/` (its newest file's time, `$reactVersion` in
`_inits.php`) on the address of every component, so a browser never runs the old copy of a
component that changed. Light, dark and the themes are the frame's.

## Going to production

Babel in the browser is for learning. Compile the files of `www/react/` with esbuild or Vite
into one bundle, load React's production builds, and drop `type="text/babel"` - the PAD side,
the islands and the JSON answers stay as they are.

See [docs/REACT.md](../../docs/REACT.md) for the patterns in the framework's documentation.
