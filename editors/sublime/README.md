# PAD support for Sublime Text

Copy all files (and the snippets) into `Packages/User/`
(Preferences > Browse Packages...). Everything works without third-party
packages.

| File | Provides |
|------|----------|
| `PAD.sublime-syntax` | Syntax highlighting: HTML via the stock grammar, PAD constructs injected everywhere (incl. attribute values); `{ignore}` blocks stay PAD-free; `{-- --}` comments |
| `Mariana.sublime-color-scheme` | Color override for the default Mariana scheme: PAD owns purple, HTML/JS/CSS moved off it, red `$` + yellow variable names, braces match HTML's `< >` |
| `PAD.sublime-completions` | Autocomplete for all built-in tags, pipe functions, properties, options, type prefixes, operators and sequence types (generated from `pad/` - regenerate with the note below) |
| `pad.py` | Auto-close: type `{/` to get the open tags offered as completions, or press `cmd+alt+.` to close the innermost open PAD tag (falls back to HTML close tag) |
| `PAD.sublime-settings` | Auto-completion triggers for `{/`, `:` and `$` |
| `Default (OSX).sublime-keymap` | The `cmd+alt+.` binding (merge into your own keymap if you already have one) |
| `PAD Comments.tmPreferences` | `cmd+/` inside a PAD tag toggles `{-- --}` (HTML comments elsewhere) |
| `PAD Indent.tmPreferences` | Auto-indent after `{if}`, `{data}` etc., dedent on `{/tag}` / `{else}` / `{when}` |
| `PAD.sublime-build` | `cmd+B` on a `.pad` file renders the page through `http://localhost/pad/` (uses `padrun.sh`) |
| `snippets/` | `padif`, `padloop`, `paddata`, `padcase`, `padignore` + Tab |

The completions file is generated from the framework source, so it is exact
for the current tag/function set. After adding new built-in tags or
functions, regenerate it (the generator scans `pad/tags/`, `pad/functions/`,
`pad/properties/`, `pad/options/` and `pad/sequence/types/`).
