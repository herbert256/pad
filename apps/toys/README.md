# Toys Application

## Introduction

Every toy of the engine's toys subsystem, to play with: the home page has a card per toy, a
toy's page runs it and shows the template that does. The toys are the files of
`pad/toys/types/` - the list, the names and the descriptions are read from them
(`_lib/toys.php`), so a toy added to the engine is here without a change to this application.

## Files

| File | Description |
|------|-------------|
| `index.pad` | The home page: a card per toy |
| `[toy].pad` / `[toy].php` | A toy, by the name in the address - `?life`; any other name is a page not found |
| `_toys/<toy>.pad` | The template a toy is shown with when it is shown with options or content - the glider gun, the race of the sorting algorithms, a plant of your own |
| `_lib/toys.php` | The catalog: `padToyTypes ()` and each type file's comment |
| `_inits.php` / `_inits.pad` | The shared frame, its menu a page per toy |
| `www/toys/toys.css` | The cards and the template block |

## Access

`http://localhost/pad/toys/`
