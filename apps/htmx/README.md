# PAD + htmx

## Introduction

htmx asks the server for a part of the page and swaps it in; PAD answers with that part alone - a {fragment} of the same template.

## Overview

The application is seven examples, each a page with the live example on top, the requests
htmx made under it - with the part PAD answered - and the template and PHP below that
(`{source}`). There is no JavaScript of the application's own but that request log, the
theme switch and the switch that turns the browser's check of a form off.

| Page | Shows |
|------|-------|
| `examples/search` | Active search - `hx-target="#results"` makes PAD answer `{fragment 'results'}` (the HX-Target header) |
| `examples/lazy` | Lazy loading - the slow part asked for once the page has loaded (`hx-trigger="load"`) |
| `examples/scroll` | Infinite scroll - the last row asks for the next batch, `&padFragment=rows` |
| `examples/edit` | Inline editing - one row of a loop as the answer, `padValidate`, the session |
| `examples/form` | A `{form}` with `rules=` and `client` posted through htmx, coming back with PAD's messages |
| `examples/cart` | Out-of-band updates - the card that was pressed and the cart count in the header |
| `examples/live` | Server-sent events - `padSse` pushes rows of HTML, htmx's sse extension swaps them in |

## How PAD answers htmx

- A request with `HX-Request: true` and an `HX-Target` that names a fragment of the page gets
  that fragment alone; without such a fragment the page renders whole (`pad/inits/vars.php`).
- `&padFragment=name` in the address names the fragment outright, and `$padFragmentOnly` in the
  page's PHP does the same.
- With `$padCsrf` on (`_config/config.php`) every post needs the session's token: `{form}`
  carries it, and `<body hx-headers="{^htmxHeaders}">` (`_inits.pad`) sends it with every htmx
  request.

## Files

```
apps/htmx/
├── _config/config.php     # $padCommon off, $padCsrf on
├── _data/                 # examples.json (the catalogue), products, contacts, sales, nav
├── _lib/htmx.php          # htmxData, htmxGroups, htmxExample, htmxCart
├── _include/cartSummary.pad
├── _inits.pad, _inits.php # the page: htmx and its sse extension, the menu, the cart count
├── index, guide           # the examples, and how PAD answers htmx
└── examples/              # one page per example, with _inits/_exits round them

www/htmx/
├── htmx.css               # the look, light and dark
└── htmx-app.js            # the request log, the rules switch, the theme
```

htmx 2.0.11 and htmx-ext-sse 2.2.4 come from unpkg.com.
