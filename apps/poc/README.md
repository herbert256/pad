# PAD in the browser - proof of concept

## Introduction

A copy of the demo application, used to prove that a whole PAD application runs in a browser
opened from the disk - `file://`, no web server, no network. `wasm/app.mjs` packs PHP
compiled to WebAssembly, the PAD engine and this application into one directory; open its
`index.html` and every page, link and form works as on a server: the guestbook, the todo list,
the contact form with its rules and CSRF token, the flash messages after a redirect.

```bash
npm install --prefix /tmp/pad-wasm @php-wasm/universal @php-wasm/web-8-4 @php-wasm/node esbuild
NODE_PATH=/tmp/pad-wasm/node_modules node wasm/app.mjs poc        # DATA/wasm/poc/
NODE_PATH=/tmp/pad-wasm/node_modules node wasm/verify-app.mjs     # the same requests in Node
```

What the application writes under `DATA/` is kept in the browser's localStorage. On a server
it is the demo, writing to `DATA/poc/` instead of `DATA/demo/`. See `wasm/README.md`.

## Examples

| Page | Description |
|------|-------------|
| Guestbook | A simple guestbook where visitors can leave messages |
| Todo List | A task manager to add, complete, and delete tasks |
| Contact Form | A contact form with `{form}` and `{input}` whose rules stand on the fields (`rules='required|email'`), checked before `contact.php` runs - refill and inline errors |
| Page Counter | A visitor counter that tracks page views |
| Clock | Display current date and time using a custom tag |

## Structure

```
poc/
├── index.php / index.pad     # Home page with example list
├── guestbook.php / .pad      # Guestbook example
├── todo.php / .pad           # Todo list example
├── todoPost.php              # Todo form POST handler
├── contact.php / .pad        # Contact form example
├── counter.php / .pad        # Page counter example
├── clock.pad                 # Clock display
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

Via web browser: `http://server/poc/`, or from the disk after `node wasm/app.mjs poc`: `DATA/wasm/poc/index.html`
