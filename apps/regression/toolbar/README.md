# Regression: the debug toolbar

## Introduction

Regression test for `$padToolbar` (pad/lib/toolbar.php). The sample page switches the
toolbar on and reads the database once; the verdict fetches it three ways and asserts that a
local whole page carries the bar - before `</body>`, with the tag tree, the SQL statement and
the template file - while a bare fragment (`&padInclude`) and a request that says it was
forwarded carry none. The crawl compares the verdict, so a toolbar that stops behaving turns
`yes yes yes` into a NO.

## Files

| File | Description |
|------|-------------|
| `index.php/pad` | Fetches the sample page three ways and states the verdicts |
| `verdict.php/pad` | The same check run on every load, so the crawl holds the toolbar to it |
| `sample.php/pad` | A page that switches the toolbar on, reads the database and loops |
| `_config/config.php` | `_common` off, the demo database |
