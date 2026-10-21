# Common Resources

## Introduction

Shared resources and utilities the engine adds to every PAD application while $padCommon is TRUE, the default.

## Structure

```
_common/
├── _config/       # Shared configuration
├── _data/         # Shared data files
├── _exits.php     # Common exit processing
├── _include/      # Shared template snippets - padFrameEnd closes the frame
├── _inits.pad     # Common HTML wrapper - left out by an application with the frame
├── _inits.php     # Common initialization (title extraction)
├── _lib/          # Shared PHP functions
└── _tags/         # Shared custom tags - padFrame opens the frame of the PAD applications
```

Its files for the browser are in `www/_common/`: `pad.css` and `pad.js`, the frame's.

## Features

- **Global wrapper**: `_inits.pad` provides a basic HTML structure with `@page@` placeholder
- **Title extraction**: `_inits.php` automatically derives page title from URL path
- **Shared libraries**: Common functions available across applications
- **Reusable tags**: Custom tags that can be used by multiple apps
- **The frame of the PAD applications**: admin, edit, tags, pad, manual, reference, sequence
  and regression look the same - a dark side with the `{}` logo and the application's name,
  which opens the menu of the applications, the application's own menu under it, and a bar
  with the page's title and the light/dark switch above the page

## The frame

An application's `_inits.pad` opens it, fills the parts of its own as slots, and closes it
below the page:

```
{common:padFrame}
  {slot 'head'}{asset 'admin.css', tag}{/slot}    in <head>
  {slot 'title'}...{/slot}                          the bar's left half, in place of the title
  {slot 'tools'}...{/slot}                          the bar's right half, before the switch
  {slot 'side'}...{/slot}                           the side's room under the menu
  {slot 'foot'}...{/slot}                           the bottom of the side
{/common:padFrame}
@page@
{common:padFrameEnd}
```

Its `_inits.php` gives the menu as `$frameMenu` - rows of `head` (a heading) or `link`,
`label`, `icon` (a name of the `{icon}` tag) and `current` - and the title is `$title`, left
out of the bar with `$skipTitle`. `{common:padFrame fill}` is for a page that fills the
window, the editor. An application that runs with `_common` sets `$commonFramed = TRUE` in
its `_inits.php`, so the `_common` wrapper leaves the document to the frame. The list of
applications in the menu under the logo is in `_tags/padFrame.php`.

## Usage

Nothing is linked: the engine adds `_common` to every application - its `_lib`, `_tags`, `_include`, `_data`, the `_inits.pad` wrapper and `_inits.php`/`_exits.php` - unless the application sets `$padCommon = FALSE` in its `_config/config.php`.
