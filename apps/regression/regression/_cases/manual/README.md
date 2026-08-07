# Examples carried over from the manual application

The manual embeds its examples with `{example 'name'}`: the page holds the prose, the example
is a file of its own that is rendered live beside its own source. These are those files, not
the pages around them, each carried over with the answer it is supposed to give.

The manual application is **unchanged**. An example lives in both places until you decide
otherwise.

## What is here, and what is not

The manual makes 59 `{example}` calls to 58 distinct names. Only **28** of those files exist in
the manual application at all; of those, **13** are here.

| left behind | why |
|-------------|-----|
| 30 names | no file of that name in the manual - see below |
| `php_and_html/z11,z12` | the `.php` half *returns* the page's data, which is the page-pair mechanism and not something a template carries |
| `php_and_html/z31,z32,z33,z41,z42` | a page-level `@else@` whose branch the `.php` half decides by returning TRUE or FALSE |
| `tag_return_values/return_1..5` | call a PHP function - `{functionTrue}`, `{functionArray}` - that the `.php` half defines |
| `callback/before`, `callback/demand` | read a total a `_callbacks/` handler accumulates |
| `miscellaneous/handling` | needs `padColorsString` from the `_common` application |

## The 30 names with no file

`{example}` looks in the current application unless given `app=`, and none of the manual's 59
calls passes one. So these render as an empty example block on a page that still returns 200:

- `fragments/*` and `tableFun/*` (17 names) exist, but in the **check** application, not the
  manual. `{example 'fragments/pipes_1', app='check'}` would find them; without the parameter
  it does not. They are already covered by the `check` group here.
- `classicModels/*` (4), `php_and_html/z00,z02,z03,z04`, `miscellaneous/true`,
  `lvl_occ/{$xxx}` and `{$1}` exist nowhere in the tree.

That is a finding about the manual, not something this suite fixes - the pages render, so the
crawl has never had reason to complain about them.
