# PAD + web components

## Introduction

PAD custom tags write custom elements with their shadow root already in them - declarative shadow DOM - and small modules upgrade them in the browser.

## Overview

| Page | Shows |
|------|-------|
| `examples/cards` | `{card}` - a custom tag of `_tags/card.pad` writing `<pad-card>` with its shadow root, the stylesheet inlined by `{shadow css=}`, named slots - no script |
| `examples/tabs` | `<pad-tabs>` complete before its module ran (the first panel shows), then upgraded with clicks and arrow keys |
| `examples/rating` | `<pad-rating value="4">` lit by its attribute in CSS, posting a vote with the CSRF token, showing PAD's average |
| `examples/stepper` | `<pad-stepper>`, form-associated: `ElementInternals` gives the `{form}` its value, `padValidate` checks it on the server |
| `examples/lit` | A Lit element fed by PAD through `items="{^items}"` |

## How it works

- `{shadow css='www:elements/card.css'} ... {/shadow}` writes `<template shadowrootmode="open">`
  with the stylesheet inlined: a shadow root takes no styles from the page, and a `<style>` in a
  template would have its braces read as PAD tags. The browser builds the root while it parses.
- A module in `www/webcomponents/elements/` defines the element; it finds the root PAD rendered
  in `this.shadowRoot` and adds behaviour only.
- Custom properties cross the shadow boundary, so the elements follow the page's light and dark
  theme (`var(--surface)`, `var(--accent)`).

## Files

```
apps/webcomponents/
├── _config/config.php     # $padCommon and $padTidy off, $padCsrf on
├── _data/                 # examples.json (the catalogue), nav
├── _lib/webcomponents.php # wcData, wcGroups, wcExample
├── _tags/                 # card.pad, tabs.pad, rating.pad, stepper.pad - the elements as PAD tags
├── index, guide
└── examples/

www/webcomponents/
├── webcomponents.css      # the page's look, light and dark
├── app.js                 # the theme switch
└── elements/              # the shadow stylesheets and the modules that upgrade the elements
```

Lit 3 comes from cdn.jsdelivr.net.
