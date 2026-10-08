# Regression: migrations and seeders

## Introduction

Regression test for `pad/lib/migrate.php` and `pad/lib/fake.php`: the application database
is an SQLite file at `DATA/migrate/regression.sqlite` that `_migrations/` builds - a `.sql`
migration with its `.down.sql`, a `.php` one whose up is a function, and one with a trigger
whose body holds semicolons - and `_seeds/` fills, the customers through `padFactory` with
fake data from a fixed seed. Every page starts with `padMigrateFresh ( TRUE )`, so a page
answers the same however often and in whatever order it runs; the Regression suite compares
each with its answer in `regression/regression/migrate/`.

## Files

| File | Description |
|------|-------------|
| `migrate.php/pad` | The pending migrations run as one batch; a second run finds none |
| `rollback.php/pad` | The down of the last batch, then of the rest; migrating back |
| `pretend.php/pad` | The statements each pending migration would send - a function's db () calls collected - nothing run |
| `seed.php/pad` | The seeders in name order, a trigger logging the orders; one seeder by name |
| `factory.php/pad` | padFactory's ids, NULL, a fixed row, and a value that reads as SQL staying a value |
| `split.php/pad` | How a .sql file is split: quotes, comments, a trigger body, DELIMITER, MySQL's escapes |
| `error.pad` | padMigrateFresh without TRUE is refused |
| `_migrations/` | The three migrations and their downs |
| `_seeds/` | `01_customers.php` (padFactory, padFake*) and `02_orders.php` (db ()) |
| `_lib/tables.php` | The tables and the status as text, for the pages |
| `_config/config.php` | The SQLite driver and file from `.env`, `_common` off |
