# padSelect Application

## Introduction

A showcase of the PAD Select subsystem: the tables of a database declared once in
`$padSelect`, the relations between them in `$padRelations`, and templates that walk them as
tags - nested tables follow their relations, views stand on tables, joins, groups, totals,
pages and unions - without a join written in a template.

Every example is a page of four cells: the declarations it reaches, its template, the
result, and the SQL PAD sent for it - each shape of statement once, with how many were sent.

## The database

A small music catalogue of made-up names - labels, artists, genres, albums, tracks, singles,
playlists and reviews - in the MariaDB/MySQL database `padSelect`, user `padSelect`, password
`padSelect` (`.env`). `_install/padSelect.sql` creates the database, the user and the tables;
`pad/install/db.sh` loads it when a machine is set up from nothing.

## Structure

```
padSelect/
├── .env              # the database login, read with padEnv
├── _install/         # padSelect.sql - the database, the user, the tables and their rows
├── _lib/select.php   # the model: every table and relation the examples stand on
├── _lib/catalog.php  # the examples by group, with their titles and texts
├── _lib/showcase.php # what the cells show: the declarations reached, the statements sent
├── _inits.php        # the menu, the example's place in the catalog
├── _exits.php        # the cells, after the example's own PHP declared what it adds
├── _inits.pad        # the frame and the four cells around @page@
├── _events/sql.php   # keeps every statement db() sends
├── _tags/statements.php  # the SQL cell
├── index.pad/.php    # the overview: every group and its examples
├── model.pad/.php    # the tables as the database has them, and _lib/select.php
├── tables/           # a table is a tag, select:, the key, where=, order=, fields=, db=, @else@
├── relations/        # down, up, three levels, the three forms of a relation, itself, every level, counts
├── views/            # base=, a view of a view, a view nested, where= on a view
├── joins/            # inner, left, natural, more than one, a join to a view, join= on the tag
├── aggregates/       # group=, having=, rollup, distinct
├── paging/           # rows= and page=, the pager
├── unions/           # union=, a union of several, a member with rows of its own
└── output/           # type=record and field, htmlAttrJson, handling options
```

The stylesheet is `www/padSelect/padSelect.css`, inside the frame of the PAD applications.

## See also

- `docs/DATABASE.md` - the reference of the Select subsystem
- the manual page "PadSelect" - `manual/?pages/padSelect`
- `classicModels` - the subsystem over the Classic Models database
