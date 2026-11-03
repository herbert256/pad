# Subsystems Application

## Introduction

The companion to Tags and Types: one page per registered subsystem, with its syntax,
description, available kinds and examples. The home page, A-Z index, groups, cheat sheet
and search use the same frame and layout as the other reference applications.

Membership comes from `padSubsystems` and `padSubsystemKinds`, with each family listed
once. Tag subsystems link their kinds to the Tags app's `tag/<family>/<kind>` pages;
options and helpers link to `kind/<family>/<kind>`. The Types app covers core tag types.

- `subsystem/[subsystemName].php` and `.pad`: detail pages, such as `?subsystem/chart`.
- `_lib/catalog.php`: catalog, members, examples and index rows.
- `_lib/subsystems.php`: descriptions and representative syntax for tag subsystems.
- `examples/<family>/`: runnable PAD examples moved from Types.
- `_common/_lib/subsystemCatalog.php`: helper-family descriptions and usage examples.
- `_common/_lib/referenceCatalog.php`: shared documentation and example-source readers.
- `www/subsystems/`: entry point and stylesheet.

A registered family automatically appears in every index. Add examples under
`examples/<family>/`, with a first line of `{pad:meta title='...', about='...'/}`;
a paired PHP file supplies data. `.src` examples are displayed without execution.
Families without local examples use the shared helper sample or their syntax as a
source example. Service calls are never run just by browsing the catalog.
