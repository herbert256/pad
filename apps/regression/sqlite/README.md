# Regression: SQLite as the application database

## Introduction

Regression test for `$padSqlDriver = 'sqlite'`: the application database is an SQLite file
at `DATA/sqlite/regression.sqlite`, built on the first request from `_install/demo.sql` - the
demo database's `staff` table and a `notes` table for the writes. No database server is
involved. Each page runs one part of the database layer and the Regression suite compares
it with its answer in `regression/regression/sqlite/`.

## Files

| File | Description |
|------|-------------|
| `verbs.php/pad` | db()'s reading verbs - field, record, array, check - in their MySQL shapes |
| `placeholders.php/pad` | The placeholders, with the quote doubled as SQLite escapes it; hostile values stay values |
| `writes.php/pad` | insert answers the new id, update and delete the rows they touched |
| `tags.pad` | The database tags and a named query from `_data/staffByPhone.sql` |
| `error.pad` | A failing statement is a PAD error naming SQLite's message |
| `select.php/pad` | The Select subsystem's statements, a hostile bound value included |
| `fresh.php/pad` | A database file that does not exist is built from the setup file |
| `_install/demo.sql` | The setup file |
| `_config/config.php` | The SQLite driver, the file and the setup, `_common` off |
