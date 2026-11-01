# Notations Application

## Introduction

Every notation of the engine's notations subsystem: text written in a notation and drawn on
the server - a formula in AsciiMath, a chemical equation, a tune in ABC, a guitar or ukulele
chord, notes on a piano, braille, morse, flag semaphore, the signal flags of the International
Code of Signals, a chess, go or draughts position, a tab for a guitar or a bass, a Karnaugh map. The home page has a card per notation, a
notation's page introduces it and shows its examples, each template beside what it draws, with
the icons that open it in the playground and the live application. The notations are the files
of `pad/subs/notations/types/` - the list, the cards and the lines are read from them
(`_lib/notations.php`), so a notation added to the engine is here without a change to this
application, shown by `[kind].pad` until it has a page of its own.

## Files

| File | Description |
|------|-------------|
| `index.pad` | The home page: a card per notation |
| `<kind>.pad` | A notation's page - math, chem, abc, chord, piano, braille, morse, semaphore, signals, chess, go, draughts, tablature, karnaugh: its introduction, then its examples |
| `[kind].pad` / `[kind].php` | A notation without a page of its own, by the name in the address - `?name`; any other name is a page not found |
| `_examples/<kind>/<n>-<slug>.pad` | The examples of a notation - first line `{meta title='...', about='...'/}`, then the template shown and run |
| `_include/examples.pad` | The examples of the page's notation: the template, coloured, beside its result |
| `_lib/notations.php` | The catalog - `padKinds ( 'notations' )` and each type file's comment - and the examples of a notation |
| `_inits.php` / `_inits.pad` | The shared frame, its menu a page per notation; the page's notation and its examples |
| `www/notations/notations.css` | The cards and the examples |

## Access

`http://localhost/pad/notations/`
