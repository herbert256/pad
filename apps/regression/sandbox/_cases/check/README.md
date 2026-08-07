# Cases carried over from the check application

The check application renders 224 pages and asserts nothing: the outer crawl compares each
against a stored copy of its HTML, which catches any change but says only that one happened.
These are the pages of it that can state their answer instead.

A page that lives in both places is tested twice, once by comparison and once by assertion,
until the copy in check is retired.

Eighteen of them have left check already. `fragments/` and `tableFun/` were never tests: the
manual embedded them as its own examples with `app='check'`, so an application of documentation
depended on an application of tests, and check could not be retired while it did. They are
`manual/fragments/` and `manual/tableFun/` now and the manual reaches them without a prefix. The
cases stay here, under their old names, and say where they came from.

## What came over, and what did not

Of the 89 pages that looked self-contained, 69 are here. The rest need something a case in
this application cannot give them:

| left behind | why |
|-------------|-----|
| `vars/at/7,8,9`, `miscellaneous/local` | read check's own `_data` - mondial.xml, bakery.xml, departments |
| `vars/at/1,2` | use the `@globals` reference form, which does not see a variable put in place by the setup entry |
| `vars/at/random` | draws a different answer every run |
| `miscellaneous/parms`, `miscellaneous/scope` | want a query parameter, or a database, at page level |
| `start/nested`, `start/combi2/page9`, `start/code/{set,increment}/{clean,reset,sandbox}` | end the request with "Using null as an array offset" when run as a case rather than as a page |
| `file/index` | reads `$padOutputType`, which a case does not set |
| `deep/four` | renders another page through `{example}` |
| `vars/at/6` | its `{demo}` blocks do not survive being joined into one case |
| `miscellaneous/eval` | needs `padColorsString` from the `_common` application |

The eight `{table}{demo}` pages that did come over have that scaffolding stripped: it is
presentation from `_common`, not behaviour under test. The demos of a page share their data -
the first defines `{data 'abc'}` and the rest print it - so each page stays one case rather
than becoming one case per demo.

## Worth a second look

`start/code/set/*` and `start/code/increment/*` fail as cases with a PHP error rather than an
answer. That may be a limit of running them outside a page, or it may be a defect in `{code}`
with the reset, clean and sandbox options - the same area as the doubling that `{code}` had.
They are listed above rather than quietly dropped.
