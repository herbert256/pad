# ClassicModels Application

## Introduction

Some PAD pages built on the Classic Models sample database, demonstrating the PAD Select
subsystem: declared tables, relations between them, and the joins PAD derives from those.

These pages were part of the manual until its examples were reorganised, after which the
manual kept a page referring to them but the examples themselves were gone. They are
recovered here, from commit ac148cccd, as an application of their own.

## Structure

```
classicModels/
├── _inits.php      # the table declarations and their relations - the whole point
├── index.pad       # the page that shows the four examples
├── offices.pad     # offices to employees, and the same the other way round
├── employees_1.pad # employee, manager and boss, through two virtual tables
├── employees_2.pad # the same relation walked a level further
├── orders.pad      # orders, their details and the products behind them
└── options.pad     # one line per select option - where, join, group, union and the rest
```

## The database

The eight tables - customers, employees, offices, orderdetails, orders, payments,
productlines and products - live in the application database this installation already uses,
so nothing has to be imported to run these pages.

`_install/classicmodels.sql` holds the schema and the data, and is what `pad/install/db.sh`
loads when a machine is set up from nothing. It moved here from `apps/pad/_install/` so that
the application and the tables it needs are in one place.

`_inits.php` declares each table with its key and sort order, the relations between them,
and three virtual tables - sales, managers and bosses - which are the employees table
reached through a different key.
