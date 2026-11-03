# Service desk

## Introduction

A support inbox with assignment, priority, handover notes, status changes and workload reports.

## Open and use

Open `/serviceDesk/` under the PAD web root (normally `http://localhost/pad/serviceDesk/`). The **How it works** page includes a walkthrough and explains the template implementation. No database server or third-party packages are required; PHP needs PDO SQLite.

Try creating a ticket, assigning it, adding a note and resolving it. Open the same ticket in two windows, save one, then save the other to see the stale-update check.

## Pages

| Page | Purpose |
|---|---|
| `index.pad` | Filter the inbox by status, priority or a literal search string. |
| `new.pad` | Create a validated support request. |
| `ticket.pad` | Assign, resolve or reopen a ticket, and add handover notes. |
| `reports.pad` | Read charts and a sortable workload table. |
| `guide.pad` | Walkthrough, implementation notes and data contract. |

## PAD first

Every application workflow is in `.pad`: form rules, request handling, bound SQL, conditionals, variables, loops, flash messages, redirects and the rendered interface. There are **no PHP page controllers or application PHP libraries**. `_config/config.php` sets SQLite, CSRF and request-variable isolation. `www/serviceDesk/index.php` enters PAD. `_inits.pad` builds the shared frame using slots and an array menu. The main register pages bind search/filter values, count matching records and paginate 25 at a time. The reusable `common:workPager` template declares its parameters with `pad:parms`; `common:workStyles` versions the shared CSS by its content hash.

Native `pad:array`, `pad:record` and `pad:field` tags handle reads. `php:db()` is PAD's existing database helper called from template expressions for writes, with numbered placeholders and a separate values array. In a template, write placeholders as `&open;0&close;`, `&open;1&close;`, and so on; PAD decodes these literal braces for `db()` without treating them as nested tags. No request text is concatenated into SQL. Literal field rules are server-validated before `padPosted()` permits a write.

An update includes the version that the form read. If another window saves first, the old form cannot overwrite it. SQLite triggers record creation and every status, priority or assignment update in the activity history.

## Persistence and scope

The first database access initializes `DATA/serviceDesk/serviceDesk.sqlite` from `_install/schema.sql` with fictional records. Preserve this file across application updates. The SQL schema is infrastructure; triggers enforce the audit/stock invariants independently of the UI. Dates in Equipment are local calendar dates, while SQLite activity timestamps are UTC.

This is a shared installation workspace, without application accounts or tenant isolation. It is a functional demo for trusted installation users. It sends no emails or supplier orders and requires no remote service. The forms and navigation work without custom application JavaScript.

## Verification

From the repository root, run `python3 tests/advanced-apps/run.py`. It starts a loopback PHP server against temporary databases, exercises real GET/POST requests, checks persisted invariants and removes only its own disposable test directory. It never uses the installation's application databases.
