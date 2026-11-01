# Sounds Application

## Introduction

Every sound of the engine's sounds subsystem on a soundboard: a card per sound with its button,
its WAV file and its recipe. The sounds are the files of `pad/subs/sounds/types/` - the list,
the names, the lines and the recipes are read from them (`_lib/sounds.php`), so a sound added to
the engine is on the board without a change to this application. The ways page shows how a page
plays a sound: a button, an element a selector finds, any element with `data-pad-sound`, a toast,
the WAV file.

## Files

| File | Description |
|------|-------------|
| `index.pad` / `index.php` | The soundboard: a card per sound - its button, its WAV, its recipe |
| `ways.pad` / `ways.php` | The ways to play a sound, an `{example}` each |
| `ways/<way>.pad` | The examples: button, target, attribute, toast, download |
| `_lib/sounds.php` | The catalog: `padSoundKinds ()`, each type file's comment and recipe |
| `_inits.php` / `_inits.pad` | The shared frame and its menu |
| `www/sounds/sounds.css` | The board and the cards |

## Access

`http://localhost/pad/sounds/`
