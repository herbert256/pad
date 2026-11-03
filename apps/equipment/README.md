# Equipment

## Introduction

An equipment register and reservation desk with conflict detection, collection and return tracking, an agenda and a complete booking history.

## Open and use

Open `/equipment/` under the PAD web root (normally `http://localhost/pad/equipment/`). The **How it works** page includes a walkthrough and explains the template implementation. No database server or third-party packages are required; PHP needs PDO SQLite.

Reserve the same item for overlapping dates to see the conflict refusal. Try an adjacent booking, collect the first item, then return it. Cancel a different reservation to release those dates.

## Pages

| Page | Purpose |
|---|---|
| `index.pad` | See bookings, collections this month and overdue returns. |
| `book.pad` | Reserve an item for a whole-day interval. |
| `booking.pad` | Collect, return or cancel a reservation. |
| `resources.pad` | Browse or add equipment with storage and collection notes. |
| `guide.pad` | Walkthrough, implementation notes and data contract. |

## PAD first

Every application workflow is in `.pad`: form rules, request handling, bound SQL, conditionals, variables, loops, flash messages, redirects and the rendered interface. There are **no PHP page controllers or application PHP libraries**. `_config/config.php` sets SQLite, CSRF and request-variable isolation. `www/equipment/index.php` enters PAD. `_inits.pad` builds the shared frame using slots and an array menu. The main register pages bind search/filter values, count matching records and paginate 25 at a time. The reusable `common:workPager` template declares its parameters with `pad:parms`; `common:workStyles` versions the shared CSS by its content hash.

Native `pad:array`, `pad:record` and `pad:field` tags handle reads. `php:db()` is PAD's existing database helper called from template expressions for writes, with numbered placeholders and a separate values array. In a template, write placeholders as `&open;0&close;`, `&open;1&close;`, and so on; PAD decodes these literal braces for `db()` without treating them as nested tags. No request text is concatenated into SQL. Literal field rules are server-validated before `padPosted()` permits a write.

Each reservation is inserted only when the same SQL statement finds no overlapping active booking. Intervals include the collection date and exclude the return date, so one booking may begin when another ends. The transition query only permits reserved to collected or cancelled, and collected to returned. History is recorded by SQLite triggers.

## Persistence and scope

The first database access initializes `DATA/equipment/equipment.sqlite` from `_install/schema.sql` with fictional records. Preserve this file across application updates. The SQL schema is infrastructure; triggers enforce the audit/stock invariants independently of the UI. Dates in Equipment are local calendar dates, while SQLite activity timestamps are UTC.

This is a shared installation workspace, without application accounts or tenant isolation. It is a functional demo for trusted installation users. It sends no emails or supplier orders and requires no remote service. The forms and navigation work without custom application JavaScript.

## Verification

From the repository root, run `python3 tests/advanced-apps/run.py`. It starts a loopback PHP server against temporary databases, exercises real GET/POST requests, checks persisted invariants and removes only its own disposable test directory. It never uses the installation's application databases.
