# Stockroom

## Introduction

A stock register with receipts and issues, an immutable movement journal, reorder planning, valuation charts and printable shelf labels.

## Open and use

Open `/stockroom/` under the PAD web root (normally `http://localhost/pad/stockroom/`). The **How it works** page includes a walkthrough and explains the template implementation. No database server or third-party packages are required; PHP needs PDO SQLite.

Add an item, receive its opening stock, then issue some units. Try issuing more than are available. Set its reorder level above the remaining stock and check the reorder report and shelf labels.

## Pages

| Page | Purpose |
|---|---|
| `index.pad` | Search stock and filter the items that need reordering. |
| `new.pad` | Create an item, with unit cost and reorder level. |
| `item.pad` | Receive or issue stock, view its journal, and edit its settings. |
| `movements.pad` | Filter, sort and page through the movement journal. |
| `reports.pad` | Calculate reorder quantities and stock value by category. |
| `labels.pad` | Print A4 shelf labels or use the printable subsystem PDF link. |
| `guide.pad` | Walkthrough, implementation notes and data contract. |

## PAD first

Every application workflow is in `.pad`: form rules, request handling, bound SQL, conditionals, variables, loops, flash messages, redirects and the rendered interface. There are **no PHP page controllers or application PHP libraries**. `_config/config.php` sets SQLite, CSRF and request-variable isolation. `www/stockroom/index.php` enters PAD. `_inits.pad` builds the shared frame using slots and an array menu. The main register pages bind search/filter values, count matching records and paginate 25 at a time. The reusable `common:workPager` template declares its parameters with `pad:parms`; `common:workStyles` versions the shared CSS by its content hash.

Native `pad:array`, `pad:record` and `pad:field` tags handle reads. `php:db()` is PAD's existing database helper called from template expressions for writes, with numbered placeholders and a separate values array. In a template, write placeholders as `&open;0&close;`, `&open;1&close;`, and so on; PAD decodes these literal braces for `db()` without treating them as nested tags. No request text is concatenated into SQL. Literal field rules are server-validated before `padPosted()` permits a write.

A single conditional INSERT checks that an issue leaves enough stock. A SQLite trigger applies the movement in the same transaction, and the stock column has a nonnegative constraint. A unique request key prevents the same form submission from changing stock twice. Item settings use a version check. Unit costs are stored as integer cents; valuation uses the current unit cost, not historical accounting cost.

## Persistence and scope

The first database access initializes `DATA/stockroom/stockroom.sqlite` from `_install/schema.sql` with fictional records. Preserve this file across application updates. The SQL schema is infrastructure; triggers enforce the audit/stock invariants independently of the UI. Dates in Equipment are local calendar dates, while SQLite activity timestamps are UTC.

This is a shared installation workspace, without application accounts or tenant isolation. It is a functional demo for trusted installation users. It sends no emails or supplier orders and requires no remote service. The forms and navigation work without custom application JavaScript.

## Verification

From the repository root, run `python3 tests/advanced-apps/run.py`. It starts a loopback PHP server against temporary databases, exercises real GET/POST requests, checks persisted invariants and removes only its own disposable test directory. It never uses the installation's application databases.
