# Inputs Application

## Introduction

Every rich form field of the engine's inputs subsystem, to try: the home page has a card per kind,
a kind's page puts it in a form that posts back and shows what the page's PHP received, whether
the post kept the rules of the template, and the template itself. One form holds every kind with
rules, checked in the browser too (`client`). The kinds are the files of `pad/inputs/types/` - the
list and the descriptions are read from them (`_lib/inputs.php`), and a kind without a page of
its own here is shown by `[kind].pad`.

## Files

| File | Description |
|------|-------------|
| `index.pad` | The home page: a card per kind |
| `form.pad` | Every kind in one form, with rules and `client` |
| `<kind>.pad` | A kind in a form of its own, its fields and their rules written out - the rules of a form are read from the page's own text |
| `[kind].pad` / `[kind].php` | A kind with no page of its own yet, without rules; any other name is a page not found |
| `_include/about.pad`, `_include/received.pad` | A kind's description, and what a post brought |
| `_lib/inputs.php` | The catalog: `padInputTypes ()` and each type file's comment, and the posted fields as a table |
| `_inits.php` / `_inits.pad` | The shared frame, its menu a page per kind |
| `www/inputs/inputs.css` | The cards, the forms and the table |

## Access

`http://localhost/pad/inputs/`
