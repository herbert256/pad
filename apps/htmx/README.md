# PAD + htmx

## Introduction

htmx asks the server for a part of the page and swaps it in; PAD answers with that part alone - a {fragment} of the same template.

## Overview

The application is seven examples, each a page with the live example on top, the requests
htmx made under it - with the part PAD answered - and the template and PHP below that
(`{source}`). There is no JavaScript of the application's own but that request log and the
switch that turns the browser's check of a form off.

The pages stand in the frame the PAD applications share (`{common:padFrame}`, as in the
sections application): the brand and the menu - the home page, the guide and the examples
under the heading of their group - in the side, and in the bar the page's title, the cart, the
theme switcher and the light/dark switch. The colours of the pages are the frame's, so every
theme colours them too.

| Page | Shows |
|------|-------|
| `examples/search` | Active search - `hx-target="#results"` makes PAD answer `{fragment 'results'}` (the HX-Target header) |
| `examples/lazy` | Lazy loading - the slow part asked for once the page has loaded (`hx-trigger="load"`) |
| `examples/scroll` | Infinite scroll - the last row asks for the next batch, `&padFragment=rows` |
| `examples/edit` | Inline editing - one row of a loop as the answer, `padValidate`, the session |
| `examples/form` | A `{form}` with `rules=` and `client` posted through htmx, coming back with PAD's messages |
| `examples/cart` | Out-of-band updates - the card that was pressed and the cart count in the frame's bar |
| `examples/live` | Server-sent events - `padSse` pushes rows of HTML, htmx's sse extension swaps them in |

## How PAD answers htmx

- A request with `HX-Request: true` and an `HX-Target` that names a fragment of the page gets
  that fragment alone; without such a fragment the page renders whole (`pad/inits/vars.php`).
- `&padFragment=name` in the address names the fragment outright, and `$padFragmentOnly` in the
  page's PHP does the same.
- With `$padCsrf` on (`_config/config.php`) every post needs the session's token: `{form}`
  carries it, and `<div class="htmx-page" hx-headers="{^htmxHeaders}">` around the page
  (`_inits.pad`) sends it with every htmx request.
- An answer for a part never has the frame around it: the engine leaves the wrappers out of a
  fragment's answer.

## Files

```
apps/htmx/
├── _config/config.php     # $padCommon off, $padCsrf on
├── _data/                 # examples.json (the catalogue - and the menu), products, contacts, sales
├── _lib/htmx.php          # htmxData, htmxGroups, htmxExample, htmxCart, htmxText
├── _include/cartSummary.pad
├── _inits.pad, _inits.php # the shared frame: htmx and its sse extension, the menu, the cart in the bar
├── index, guide           # the examples, and how PAD answers htmx
└── examples/              # one page per example, with _inits/_exits round them

www/htmx/
├── htmx.css               # what stands in the pages, in the frame's colours
└── htmx-app.js            # the request log, the rules switch
```

htmx 2.0.11 and htmx-ext-sse 2.2.4 come from unpkg.com.
