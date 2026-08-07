# Cases carried over from the check application

The check application is gone. It rendered 224 pages and asserted nothing: the outer crawl
compared each against a stored copy of its HTML, which caught any change but said only that one
happened. This group is the half of it that can state its answer instead.

The other half is `pages/`. Where a page needed to be a page - a {page} chain nine deep, a
callback reading an occurrence's fields, a database, a script, a request that ends in an error -
it moved there and is fetched over HTTP against a recorded answer. Between the two, everything
check tested is tested, and by assertion rather than by comparison.

The group keeps its name because that is where these came from, and the case names are still the
page paths they had.

## What became what

| check had | where it is |
|-----------|-------------|
| 74 self-contained pages | cases here |
| the `{page}` and `{code}` scenarios under `start/` | `pages/start/` - 111 files, 23 tests |
| `db/`, `select/`, `deep/`, `error/`, `file/`, `vars/at/`, `tags/`, `functions/` | `pages/` under the same names |
| `fragments/` | `manual/fragments/` - never tests, the manual's own examples |
| `tableFun/` and the pages the manual never linked | `pages/manual/` and `pages/tableFun/` |
| `_data/`, `_scripts/`, the error `_tags/` | moved beside the tests that read them |

## What did not come, and why

Five pages were dropped rather than moved, each already covered:

- `vars/at/3`, `4`, `5`, `5a`, `10` - the wildcard forms of a dotted reference, which
  `variables/wildcards.php` asserts over a cube built for the purpose.
- `vars/at/6` - properties reached by name and by relative level, which
  `properties/naming.php` asserts in all four spellings. Its own relative levels counted from a
  nesting the move changed, so it could not have come across unedited anyway.

## Worth a second look

`start/code/set/*` and `start/code/increment/*` fail as sandbox cases with a PHP error rather
than an answer. They are `pages/` tests now and pass there, so the fault is in running them
outside a page rather than in the pages themselves - but the `{code}` reset, clean and sandbox
options are the same area as the doubling that `{code}` had, and it is worth knowing.
