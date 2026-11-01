# Printables Application

## Introduction

Every kind of the engine's printables subsystem - pages made for paper - on a page of its own: the
template beside what it prints, sheets on a grey desk at their size, with a link to the same sheets
as a PDF. The kinds are the files of `pad/subs/printables/types/` - the list, the names and the
descriptions are read from them (`_lib/printables.php`), so a kind added to the engine is here
without a change to this application. The sample data comes from `{fake}` with fixed seeds, the
same on every request.

## Files

| File | Description |
|------|-------------|
| `index.pad` / `index.php` | The home page: a card per kind |
| `[kind].pad` / `[kind].php` | A kind, by the name in the address - `?labels`: its template beside what it prints; any other name is a page not found |
| `kinds/<kind>.pad` | The template a kind is shown with - a page of its own as well |
| `_lib/printables.php` | The catalog: `padPrintableKinds ()` and each type file's comment |
| `_inits.php` / `_inits.pad` | The shared frame, its menu a page per kind |
| `www/printables/printables.css` | The cards, the template beside the preview |

## Access

`http://localhost/pad/printables/`
