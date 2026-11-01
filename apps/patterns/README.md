# Patterns Application

## Introduction

Every pattern of the engine's patterns subsystem - seeded SVG backgrounds and generative art -
to look at: the home page has a card per pattern, a pattern's page draws it large beside the
template that does, with links to the seeds next to the one shown (`?topography&seed=8`), then
the example the pattern's own file gives and the pattern as the background of a box, written
with `as='url'`. The patterns are the files of `pad/subs/patterns/types/` - the list, the names,
the descriptions and the examples are read from them (`_lib/patterns.php`), so a pattern added to
the engine is here without a change to this application.

## Files

| File | Description |
|------|-------------|
| `index.pad` | The home page: a card per pattern, the pattern drawn on it |
| `[kind].pad` / `[kind].php` | A pattern, by the name in the address - `?topography`, a seed with `&seed=8`; any other name is a page not found |
| `_lib/patterns.php` | The catalog: `padPatternKinds ()` and each type file's comment; the seed a link asks for |
| `_inits.php` / `_inits.pad` | The shared frame, its menu a page per pattern |
| `www/patterns/patterns.css` | The cards and the template blocks |

## Access

`http://localhost/pad/patterns/`
