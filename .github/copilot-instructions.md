<!-- Repository-specific Copilot instructions for PAD framework -->
# Copilot / AI Agent Instructions — PAD (PHP Application Driver)

Purpose: help AI coding agents be productive quickly in this repo by describing architecture, workflows, conventions and concrete examples.

- **Big picture**: PAD is a PHP-driven template engine where templates (`.pad`) drive execution and pair with PHP data files (`.php`). Core lives in `pad/`, apps live in `apps/`, and the public/web entrypoints are in `www/`.

- **Key entrypoints**:
  - Framework loader: [pad/pad.php](pad/pad.php)
  - Web document root examples: [www/index.php](www/index.php)
  - CLI helper: `apps/cli/pad` (run with PHP or directly if executable)
  - Primary developer reference: [CLAUDE.md](CLAUDE.md) (contains most framework rules)

- **Page pairing convention (critical)**: every page has a `pagename.php` (data) and a `pagename.pad` (template). Example: [apps/demo/index.php](apps/demo/index.php) ↔ [apps/demo/index.pad](apps/demo/index.pad). Do NOT remove either file without checking dependent apps.

- **Autoloaded dirs & lookup order**: directories named with a leading underscore are special and auto-loaded/inherited up the directory tree: `_lib`, `_include`, `_tags`, `_functions`, `_callbacks`, `_options`, `_config`, `_data`. When resolving a tag/function/include, search starts in the page directory and climbs to repo root (override/inherit pattern).

- **Routing / URL style**: PAD uses query-based page access (e.g. `?about`, `?admin/users`), not path-based routing. Preserve `?page` style when adding links or docs.

- **Common syntax pitfalls to respect** (taken from CLAUDE.md)
  - Use `{echo ... | func}` for pipe functions — `{$var | func}` is invalid.
  - Arithmetic pipes require a space: `{echo $x | + 1}` not `| +1`.
  - Conditionals require comparisons (`{if $x eq 0}`), not bare truthiness.

- **Development / run commands (minimal, discoverable)**:
  - Quick local server (use `www/` as docroot):

    php -S localhost:8000 -t www

  - Syntax/lint check for a PHP file:

    php -l path/to/file.php

  (There is no composer.json in the repo; treat this as a self-contained PHP codebase.)

- **Conventions to follow when editing**:
  - When adding features for an app, prefer adding files under `apps/<appname>/` and use `_lib`, `_tags`, or `_functions` there so the change is scoped.
  - For global framework changes, modify `pad/` sources and update any docs under `docs/` and `CLAUDE.md` accordingly.
  - Many apps mirror files between `apps/` and `www/` (entrypoints). If you change an app's entrypoint, verify the matching `www/` entrypoint or the app's `index.php` remains consistent.

- **Examples (patterns to mimic)**:
  - Custom tag: implement `apps/myapp/_tags/mytag.php` and call it in templates as `{mytag ...}`.
  - Pipe function: create `apps/myapp/_functions/currency.php` and use `{echo $price | currency}`.
  - Wrapper flow: `_inits.php`/_inits.pad run before page code; `_exits.php`/_exits.pad run after — maintain ordering when adding global wrappers.

- **Files worth checking for context**:
  - [CLAUDE.md](CLAUDE.md) — authoritative rules and examples; read before changing template semantics.
  - [README.md](README.md) — high-level project layout and license.
  - [pad/README.md](pad/README.md) and `pad/` sources — core engine.
  - `apps/*/README.md` for app-specific notes (examples in `apps/demo/`, `apps/hello/`).

- **When unsure**: Prefer non-invasive changes (add new `_lib` helpers, or new tags) rather than modifying core `pad/` behavior. If changing core, update `CLAUDE.md` and `docs/` with the rationale and examples.

Please review this file and tell me if you want more detail in any section (quickstart, CLI examples, or code snippets merged from `CLAUDE.md`).
