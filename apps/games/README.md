# Games Application

## Introduction

Every game of the engine's games subsystem, to play: the home page has a card per game, a game's
page plays it and shows the template that does. The games are the files of `pad/subs/games/types/` -
the list, the names and the descriptions are read from them (`_lib/games.php`), so a game added
to the engine is here without a change to this application.

## Files

| File | Description |
|------|-------------|
| `index.pad` | The home page: a card per game |
| `[game].pad` / `[game].php` | A game, by the name in the address - `?memory`; any other name is a page not found |
| `_games/<game>.pad` | The template a game that needs content is played with - the crossword's words, the quiz's questions |
| `_lib/games.php` | The catalog: `padGameTypes ()` and each type file's comment |
| `_inits.php` / `_inits.pad` | The shared frame, its menu a page per game |
| `www/games/games.css` | The cards and the template block |

## Access

`http://localhost/pad/games/`
