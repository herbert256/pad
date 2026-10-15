# Ledger

## Introduction

A household budget - six months as charts, budgets per category, the transactions as a sortable table, CSV out and in, a quick entry posted as JSON - with its books in SQLite, built and brought up to date by its migrations, on a server or in the browser when `pad pack ledger` runs it offline.

## Overview

| Page | What it does |
|------|--------------|
| `index` | The month: income, spending, what is left - `{chart 'bar'}` of six months, `{chart 'donut'}` of where it went, a `{progress}` per budget, the accounts, the latest entries, and a quick entry that `ledger.js` posts as JSON |
| `transactions` | The month's transactions as a `{datatable}` - sorted and paged on the server - narrowed by category, account or a word; `?transactions&padFormat=csv` is the same rows as CSV (`$padExpose`) |
| `add` | A transaction: `{form}` with `rules=` (a date, an amount), its sign from its category |
| `quick` | The quick entry: JSON in (`padRequest` reads a JSON body), the booking and the new totals out (`$padExpose`), with `X-CSRF-Token` |
| `budgets` | What each kind of spending may cost a month |
| `import` | Transactions from a CSV file (`padUpload`): a bank's export, or this application's own |

## The database and its migrations

`_config/config.php` makes the application database SQLite, `DATA/ledger/ledger.sqlite`. Its
schema is `_migrations/`, and `_inits.php` runs what is pending on the first request that finds
it (`ledgerReady` - `padMigrateStatus`, `padMigrate`): a database made from nothing gets every
migration and then the seeder's half year of examples (`_seeds/01_ledger.php`, the same every
time from `padFakeSeed`); a database made by an older version gets only what it lacks.
`2026_10_01_090000_add_budgets.php` is such a later step - a PHP migration that adds the budgets
table and gives the categories it finds a budget.

That is what makes an offline application last: packed with `pad pack ledger`, its database
is kept in the visitor's browser, and a new pack of a newer version migrates it there on the
next start, the transactions untouched.

## Offline

```bash
pad pack ledger            # DATA/wasm/ledger/ - open index.html, or serve it over https and install it
pad pack ledger --serve    # the same, on http://127.0.0.1:8090/
```

The quick entry's JSON request, the CSV download and the import all work there as on a server;
the runtime's panel browses the tables and exports the database.
