# Demo Application

## Introduction

Interactive demo showcasing PAD framework features with practical examples.

## Examples

| Page | Description |
|------|-------------|
| Guestbook | A simple guestbook where visitors can leave messages |
| Todo List | A task manager to add, complete, and delete tasks |
| Contact Form | A contact form with `{form}` and `{input}` whose rules stand on the fields (`rules='required|email'`), checked before `contact.php` runs - refill and inline errors |
| Page Counter | A visitor counter that tracks page views |
| Clock | Display current date and time using a custom tag |
| QR Codes | `{qr}` - a text typed in a form as a QR code, the four error correction levels, a Wi-Fi code |
| Barcodes | `{barcode}` - EAN-13 for a product list, Code 128 on a shipping label, EAN-8 and UPC-A |
| Calendar | `{calendar}` - a month with its agenda and links to the months around it, and a mini calendar from the pair form |

## Structure

```
demo/
├── index.php / index.pad     # Home page with example list
├── guestbook.php / .pad      # Guestbook example
├── todo.php / .pad           # Todo list example
├── todoPost.php              # Todo form POST handler
├── contact.php / .pad        # Contact form example
├── counter.php / .pad        # Page counter example
├── clock.pad                 # Clock display
├── qr.php / .pad             # QR codes
├── barcode.php / .pad        # Barcodes
├── calendar.php / .pad       # Calendar
├── _config/config.php        # Switches _common off, CSRF protection on
├── _inits.php / .pad         # Global layout wrapper
├── _include/todo.pad         # Todo list snippet
├── _tags/clock.php           # Custom clock tag
└── _data/navigate.json       # Navigation menu data
```

## Features Demonstrated

- **Page pairing**: `.php` data files paired with `.pad` templates
- **Global wrapper**: `_inits.pad` provides consistent layout with `@page@` placeholder
- **Custom tags**: `_tags/clock.php` shows how to create application-specific tags
- **Data files**: `_data/navigate.json` demonstrates JSON data integration
- **Form handling**: Contact form shows POST processing
- **Flash messages**: the guestbook and contact thanks travel as `padFlash()` messages over
  the redirect and show through `{flash}`
- **CSRF protection**: `$padCsrf = TRUE` in `_config/config.php` adds the session's token to
  every POST form and turns away a post without it (403)
- **Data iteration**: Examples of iterating over arrays

## Access

Via web browser: `http://server/demo/`
