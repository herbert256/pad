# Images Application

Pictures, avatars, emoji and country labels, seeded patterns, and image filters share this gallery. The picture and avatar examples formerly in Showcase now live under `examples/`.

## Introduction

Every pattern of the engine's patterns subsystem - seeded SVG backgrounds and generative art -
to look at: the home page has a card per pattern, a pattern's page draws it large beside the
template that does, with links to the seeds next to the one shown (`?topography&seed=8`), then
the example the pattern's own file gives and the pattern as the background of a box, written
with `as='url'`. The patterns are the files of `pad/subs/images/patterns/types/` - the list, the names,
the descriptions and the examples are read from them (`_lib/patterns.php`), so a pattern added to
the engine is here without a change to this application. The filters page does the same for the
image filters of `{image:thumbnail}`, the other subsystem shown here: every filter on one picture.

## Files

| File | Description |
|------|-------------|
| `index.pad` | The home page: a card per pattern, the pattern drawn on it |
| `[kind].pad` / `[kind].php` | A pattern, by the name in the address - `?topography`, a seed with `&seed=8`; any other name is a page not found |
| `filters.pad` / `filters.php` | Every image filter of `{image:thumbnail}` - the files of `pad/subs/images/filters/types/` - on the same picture, `www/images/photos/tulips.jpg`, with the template from the filter's own file; a placeholder shown with the picture moved out of its box, so what stands behind it shows |
| `_lib/patterns.php` | The catalog: `padPatternKinds ()` and each type file's comment; the seed a link asks for |
| `_inits.php` / `_inits.pad` | The shared frame, its menu a page per pattern |
| `www/images/patterns.css` | The cards and the template blocks |
| `www/images/photos/tulips.jpg` | The picture of the filters page; `{image:thumbnail}` writes what it makes of it to `www/images/_thumbs/` |

## Access

`http://localhost/pad/images/`
