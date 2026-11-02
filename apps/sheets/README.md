# Sheets Application

## Introduction

Spreadsheets in a page, made with the `{sheet:grid}` tag of the formulas subsystem: the cells
are lines of text with a comma between them, or the rows of `data=`, and their formulas -
spreadsheet syntax, read by a parser of the engine's own - are computed on the server with the
functions of `pad/subs/formulas/types/`, which answer what Excel answers. Each example page runs
its template and shows it, with the WASM editor and live icons; the functions page lists every
function as its file describes it, each example computed on a sample sheet - a function added to
the engine is there without a change to this application.

## Files

| File | Description |
|------|-------------|
| `index.pad` / `index.php` | The home page: a card per sheet |
| `budget`, `loan`, `grades`, `dates`, `editable` | A page per example sheet, each `{include:runSheet/}` - its lead, the sheet run, its template and its PHP |
| `functions.pad` / `functions.php` | Every function: what its file says, its examples and what they show on the sample sheet |
| `_sheets/<name>.pad` | The template of an example; `_sheets/loan.php` builds the loan's cells in PHP |
| `_data/sample.csv` | The sample sheet the examples of the functions read (the framework cases of `regression/framework/formulas` read it too) |
| `_include/runSheet.pad` | The layout of an example page |
| `_lib/sheets.php` | The examples, an example's template and PHP, the functions from their files |
| `_inits.php` / `_inits.pad` | The shared frame, its menu a page per sheet |
| `www/sheets/sheets.css` | The cards, the template blocks and the table of functions |

## Access

`http://localhost/pad/sheets/`
