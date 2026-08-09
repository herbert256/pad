# Regression 4 - the Framework suite

## Introduction

The framework cases as fetched pages, exactly the Pages model: every case is a triple in a
group directory - the `.pad` its template, the `.txt` the outcome beside it, an optional
`.php` its variables - and each is fetched directly as the page it is, so a case gets the
isolation a request grants by construction and the crawl walks it like any other page. The
suite is driven from the regression application's Framework page, the way Pages drives
regression2 and Common drives regression3. `_common` is switched off; the fixtures a case
group needs - the custom tags and functions, the callbacks, the staff and nums data - are
this application's own, and `_lib/fixtures.php` pulls in the regression runner the harness
group asserts.

## Files

| File | Description |
|------|-------------|
| `<group>/<case>.pad` | One case per page, its name saying what it asserts |
| `<group>/<case>.txt` | The outcome beside it - a `/regex/` for a case that draws |
| `_lib/fixtures.php` | Includes the regression runner - functions and fixture globals |
