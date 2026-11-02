# Types Application

## Introduction

A page for every type of PAD tag - the prefix before the colon of `{pad:if}`, `{chart:pie}`,
`{field:name}`, `{sequence:fibonacci}`: what the type reaches in one line, how it is written
with its required prefix (every named tag is
reached by its prefix alone), its section of `docs/reference/TYPES.md` and the comment of its
handler, the tags it reaches by name - the built-in tags of `pad:`, the kinds of a subsystem,
the sequence types, the sequence actions, the pipe functions, the tags of `_common` - each
linked to its page in the tags application, and live examples. Over the pages stand the
indexes: by name, by required prefix, by group, a cheat sheet with the syntax of every
type, and a search. It is the tags application's companion: that one has a page per tag, this
one a page per kind of tag.

## Files

| File | Description |
|------|-------------|
| `index.pad` | The home page: the indexes and every group with its types |
| `names.pad` | The types from A to Z |
| `order.pad` | Every required namespace and the types it selects |
| `groups.pad` | The types by group - tags, stores, values, sequences, subsystems |
| `cheatsheet.pad` | The syntax of every type on one page |
| `search.pad` | `?search&q=word` - the types whose name, line, syntax or members hold every word |
| `type/[typeName].pad`, `.php` | The page of a type, `?type/field` |
| `_lib/types.php` | What the application says of each type: its line, its group, its syntax; the groups; the required namespaces |
| `_lib/catalog.php` | The catalog - the handlers of `pad/types/` - each type's section of TYPES.md, its members, its examples, and the rows of every index |
| `_inits.pad`, `_inits.php` | The frame every PAD application shares (`_common/_tags/padFrame`), with this application's menu and the search box in its bar |
| `examples/<type>/<n>-<slug>.pad` | A live example - first line `{pad:meta title='...', about='...'/}`; with a paired `.php` for its data |
| `examples/<type>/<n>-<slug>.src` | An example that is shown but never run - a database table, a shell script |
| `_tags/greeting.pad`, `_include/card.pad`, `_data/menu.json` | What the examples of `app:`, `include:` and `local:` reach |
| `_config/config.php` | Switches `_common` off - the frame is reached as `common:padFrame` - and fixes the timezone |

The stylesheet is `www/types/types.css`, the tags application's.

## Adding a type

A handler added to `pad/types/` has its page and its place in every index: its line read from
the comment of the handler until `_lib/types.php` says more, and its examples once
`examples/<type>/` holds them.

## Subsystem options and helpers

The **Options and helpers** group covers all twelve `padSubsystemKinds` families as well as
the 44 tag handlers in the other groups. Each family has a page, an example and links to every
kind's detail page in the tags application. These families are excluded from the tag-prefix
list: their pages show actual options and helper calls rather than a colon prefix.
Membership and examples are shared through `apps/_common/_lib/subsystemCatalog.php`.
