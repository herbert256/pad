# Regression3 - the pages that use _common

The pages suite split in two. Every test here is an ordinary PAD page, fetched over HTTP with
`&padInclude` and compared with the `.txt` beside it, exactly as in `regression2` - the
difference is what the page is allowed to reach.

`regression2` switched `_common` off (`$padCommon = FALSE` in its `_config/config.php`), so its
tests prove a page needs nothing but its own application. The pages that do use `_common` -
`{example}`, `{demo}`, `{table}`, the `{block}` snippet, the colouring functions in
`_common/_lib/` - moved here, where `_common` is on as it is for any ordinary application.

What lives here:

| test | why it is here |
|------|----------------|
| `deep/` | the page chain; `deep/four` embeds `deep/five` through `{example}` |
| `error/` | the error pages themselves need nothing, but their menu (`error/index`) prints through `{block}` |
| `manual/` | the parked manual pages - `{demo}`, `{table}`, `{example}`, `{block}` and `{source}` throughout |
| `tableFun/` | the ten table pages `manual/table_fun` embeds through `{example}` |
| `misc/eval` | shows its forms through `{demo}` and `{table}` |
| `tags/redirect`, `tags/restart` | wrapped in `{demo}` and `{table}` |
| `hello/` | a copy of `regression2/hello` - the page `redirect` and `restart` land on, which must be in the same application |
| `index` | the one test fetched without `&padInclude`, so it asserts the frame itself: the `_common` wrapper and the title `_inits.php` derives |
| `menu` | the `{menu}` include - `lines.pad` around it, `menu.json` behind it, the link-per-application bar |
| `reference` | the `_common/_lib/` helpers a page calls directly: `getReference()` over the type handlers, and the Xref link builders in `menu.php` |
| `misc/db` | the demo database through the credentials `_common/_config` supplies - the one test that queries it |

The application also has an `_inits.pad` of its own, `{padOpen}@page@{padClose}` - so the `index`
test, the one fetched full, asserts the whole frame: the `_common` wrapper, the menu, and the
title printed through `showTitle`.

## Running them

The overview is in the regression application, at `?common/index` - the Common entry in its
menu, beside the Pages suite that drives `regression2`. `_lib/regression.php` there is the
runner.
