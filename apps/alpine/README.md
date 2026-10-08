# PAD + Alpine

## Introduction

Alpine brings parts of a PAD page to life where they stand; PAD hands it its state as JSON and answers its requests.

## Overview

Six examples, each a page with the live component on top, the requests it made to PAD under
it and the template, PHP and script below that (`{source}`).

| Page | Shows |
|------|-------|
| `examples/state` | `x-data="{^profile}"` - the state is the JSON of a PHP array, no brace of Alpine's in the template |
| `examples/components` | Tabs, an accordion and a dialog registered with `Alpine.data` in `www/alpine/`, called with PAD's data as the argument |
| `examples/search` | `x-model.debounce` and a fetch of the same page with `padFormat=json` (`$padExpose`) |
| `examples/form` | One list of rules: `padValidate` on the server, `padValidateClient` + `{validator}` in the browser |
| `examples/todos` | A list kept in the session - a change shows at once, the server's answer is the truth |
| `examples/live` | `padSse` with `every=`, an `EventSource` opened in `x-init` |

## The braces

PAD reads every `{` of a template as the start of a tag, and Alpine writes objects with them.
The examples never write one: state comes from PHP through the `^` sigil (`x-data="{^profile}"`,
JSON escaped for the attribute), and longer components live in `www/alpine/examples/*.js` as
`Alpine.data` - files the web server sends as they are. A brace followed by a space is no PAD
tag either, for a short object: `x-data="{ open: false }"`.

## Files

```
apps/alpine/
├── _config/config.php     # $padCommon off, $padTidy off, $padCsrf on
├── _data/                 # examples.json (the catalogue), products, todos, nav
├── _lib/alpine.php        # alpineData, alpineGroups, alpineExample
├── _inits.pad, _inits.php # the page: the csrf-token meta, alpine-app.js, then Alpine deferred
├── index, guide           # the examples, and Alpine on a PAD page
└── examples/              # one page per example, with _inits/_exits round them

www/alpine/
├── alpine.css             # the look, light and dark
├── alpine-app.js          # pad.get, pad.post (with the CSRF header), the request log, the theme
└── examples/*.js          # the Alpine.data components of each example, by the page's name
```

Alpine 3.17.4 comes from unpkg.com.
