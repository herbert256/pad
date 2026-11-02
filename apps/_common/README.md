# Common Resources

## Introduction

Shared resources and utilities the engine adds to every PAD application while $padCommon is TRUE, the default.

## Structure

```
_common/
├── _config/       # Shared configuration
├── _data/         # Shared data files - padApps.php, the PAD applications of the brand's pull-down
├── _include/      # Shared template snippets - padFrameEnd closes the frame
├── _inits.php     # Common initialization (title extraction)
├── _lib/          # Shared PHP functions
└── _tags/         # Shared custom tags - padFrame opens the frame of the PAD applications,
                   #   padBrand is the brand top left of every one
```

Its files for the browser are in `www/_common/`: `pad.css` and `pad.js`, the frame's, and
`brand.css` and `brand.js`, the brand's.

## Features

- **The brand of the PAD applications**: every PAD application shows the same corner top left,
  `{common:padBrand/}` - the `{}` logo, a link to the PAD home, and the application's name,
  the pull-down of the applications - written by the frame
- **The frame of the PAD applications**: every PAD application - with `_common` on, or
  reaching it with the `common:` prefix - looks the same: a side with the brand on top, the
  application's own menu under it, and a bar with the page's title and the theme switcher
  (`{widget:themes/}`) above the page - light, or in the theme chosen, the side with the page
- **Title extraction**: `_inits.php` derives the page's title from its name, for the bar
- **Shared libraries**: Common functions available across applications
- **Reusable tags and snippets**: `{app:example}`, `{app:demo}`, `{app:table}`, `{pad:block}`, `{include:lines}` ...

`_common` has no wrapper of its own: the frame is opened by each application's `_inits.pad`.

## The frame

An application's `_inits.pad` opens it, fills the parts of its own as slots, and closes it
below the page:

```
{common:padFrame}
  {pad:slot 'head'}{pad:asset 'admin.css', tag/}{/pad:slot}    in <head>
  {pad:slot 'title'}...{/pad:slot}                          the bar's left half, in place of the title
  {pad:slot 'tools'}...{/pad:slot}                          the bar's right half, before the switcher
  {pad:slot 'side'}...{/pad:slot}                           the side's room under the menu
  {pad:slot 'foot'}...{/pad:slot}                           the bottom of the side
{/common:padFrame}
@page@
{common:padFrameEnd/}
```

Its `_inits.php` gives the menu as `$frameMenu` - rows of `head` (a heading) or `link`,
`label`, `icon` (a name of the `{icon:star}` tag) and `current` - and the title is `$title`, left
out of the bar with `$skipTitle`. `{common:padFrame fill}` is for a page that fills the
window, the editor. The theme switcher stands in every bar, unless `$padTheme = FALSE`; its
post is answered before the application runs (`pad/inits/themes.php`). Offline (`pad pack`)
the frame writes `pad.css` and `pad.js` into the page. The list of applications in the pull-down of the brand is
`_data/padApps.php`.

## The brand

An application with a bar of its own puts the brand in it - it links its stylesheet and script
itself:

```
<header class="topbar">{common:padBrand/} <nav>...</nav></header>
```

Its colours are its own and follow the page's `color-scheme`; its name takes the colour of the
bar. Its list of the applications opens in front of everything the page has - a popover in the
top layer, placed by `brand.js` - with the page dimmed behind it; a click on the dimmed page or
Escape closes it. The frame writes it as `{common:padBrand frame/}`, with the links in its `<head>`.

## Usage

Nothing is linked: the engine adds `_common` to every application - its `_lib`, `_tags`, `_include`, `_data` and `_inits.php` - unless the application sets `$padCommon = FALSE` in its `_config/config.php`; the `common:` prefix reaches its tags and snippets even then.
