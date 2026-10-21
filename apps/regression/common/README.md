# Regression3 - the pages that use _common

The pages suite split in two. Every test here is an ordinary PAD page, fetched over HTTP with
`&padInclude` and compared with the `.txt` beside it, exactly as in `regression/pages` - the
difference is what the page is allowed to reach.

`regression/pages` switched `_common` off (`$padCommon = FALSE` in its `_config/config.php`), so its
tests prove a page needs nothing but its own application. The pages that do use `_common` -
`{example}`, `{demo}`, `{table}`, the `{block}` snippet, the colouring functions in
`_common/_lib/` - moved here, where `_common` is on as it is for any ordinary application.

What lives here:

| test | why it is here |
|------|----------------|
| `deep/` | the page chain; `deep/four` embeds `deep/five` through `{example}` |
| `manual/` | the parked manual pages - `{demo}`, `{table}`, `{example}`, `{block}` and `{source}` throughout |
| `tableFun/` | the ten table pages `manual/table_fun` embeds through `{example}` |
| `misc/eval` | shows its forms through `{demo}` and `{table}` |
| `tags/redirect`, `tags/restart` | wrapped in `{demo}` and `{table}` |
| `hello/` | a copy of `regression/pages/hello` - the page `redirect` and `restart` land on, which must be in the same application |
| `index` | the one test fetched without `&padInclude`, so it asserts the frame itself: `_common`'s `padFrame` and `padFrameEnd` and the title `_inits.php` derives |
| `reference` | the `_common/_lib/` helper a page calls directly: `getReference()` over the type handlers |
| `misc/db` | the demo database through the credentials `_common`'s `.env` supplies - the one test that queries it |
| `misc/env` | `padEnv` reads `_common`'s `.env` for an application that runs with `_common` |

The application's `_inits.pad` is the frame every PAD application shares,
`{common:padFrame}@page@{common:padFrameEnd}` - so the `index` test, the one fetched full,
asserts the whole frame: the side with the menu of the applications, the bar with the title
`_common`'s `_inits.php` derives, and the page in it.

## Running them

The overview is in the regression application, at `?common/index` - the Common entry in its
menu, beside the Pages suite that drives `regression/pages`. `_lib/regression.php` there is the
runner.
