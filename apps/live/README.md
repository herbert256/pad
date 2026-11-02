# Live

## Introduction

Run a PAD page or preview a template on the HTTP server, with JSON, YAML, XML, CSV or PHP data. Live combines the former Live and Playground editors.

## Usage

Open `http://localhost/pad/live/`. The template and data/PHP editors stand on the left, the output on the right, in PAD's shared frame.

- **Run page** (Ctrl/Cmd+Enter) saves the template and data/PHP as `live/?page/<id>`. This is a full page with request values, sessions, forms that post back, scripts, `_common` and its demo database. **open alone** opens that page separately.
- **Render template** runs the template inside the current request using `padCode()`, with the former Playground restrictions and an output frame with an opaque origin.
- **Show HTML** displays the exact HTML from that template preview, with entities preserved.
- **Switch to PHP** exchanges the data and PHP panes. Each keeps its content; an empty PHP pane starts with only `<?php` and `?>`.
- **Open in WASM** transfers the template, both data panes and the selected mode to the browser editor. WASM's **Open in live** transfers them back.

The address bar always carries the current editors in its `#s=` hash. A shared link fills the editors and waits for a button; only the built-in example runs automatically. Existing Live links still work. Example icons elsewhere now open WASM (▶) or Live (⚡); server-only examples retain only Live.

## Execution modes

Both server modes require a local request naming localhost or a loopback address and a valid CSRF token for POST. Full pages keep their existing access to PHP, requests and the demo database. The preview endpoint (`?render`) has no `_common`, database credentials, request variables or PHP functions callable from templates. Its PHP data code still runs as written. Preview limits are five seconds, 64 KB of template/data/PHP input, and 256 KB of output. Preview scripts run inside their own sandbox and cannot access the editor.

Full pages use a separate iframe with their existing same-origin/form permissions. Switching modes stops the previous output document. Saved pages live under `DATA/live/` for a day; their IDs derive from their content.

WASM runs PHP in the browser and has no access to this server's files or demo database. Its data pane accepts the same usual text formats and PHP. Old `/playground/` bookmarks redirect to WASM while preserving their URL fragment.

## Files

| File | Purpose |
|---|---|
| `index.php`, `index.pad` | Combined editor in the shared frame |
| `save.php`, `page/_inits.php` | Saved pages and their paired data/PHP |
| `render.php`, `render.pad` | Template preview and HTML source view |
| `_lib/live.php` | Saved-page IDs, cleanup and the data reader shared with WASM |
| `_guard.php`, `_config/config.php` | Local access, CSRF and per-mode configuration |
| `www/live/live.js`, `live.css` | Editors, output modes, shared links and layout |
| `www/live/index.php` | Saved-page bootstrap |
