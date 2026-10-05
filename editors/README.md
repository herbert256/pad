# Editor support for PAD

| Directory | Editor | Provides | Install |
|-----------|--------|----------|---------|
| `sublime/` | Sublime Text | Full kit: syntax, colors, completions, close-tag plugin, snippets, comments, indent, build | copy files + `snippets/*` into `Packages/User/` |
| `vscode/pad/` | VS Code, Cursor, Windsurf, VSCodium | Full kit: injection grammar, token colors, completion/close-tag provider, snippets, language config | copy dir to `~/.vscode/extensions/herbert.pad-lang-0.1.0` (or `~/.cursor/extensions/`, ...) |
| `vim/` | Vim / Neovim | Syntax highlighting (HTML base + PAD injected everywhere, split sigil/name vars) | copy `syntax/` + `ftdetect/` into `~/.vim/` or `~/.config/nvim/` |
| `lsp/` | Any LSP editor (Neovim, Helix, Emacs, Sublime-LSP, ...) | A zero-dependency Node language server: completions, close-tag suggestions, diagnostics from a real render under the strict check, hover from `docs/reference`, go-to-definition along PAD's lookup order (`{$total}` to the paired `.php`) | see `lsp/README.md` for per-client config |
| `notepad-plus-plus/` | Notepad++ (Windows) | User Defined Language: keywords, comments, folding, PAD-purple styling | Language > User Defined Language > Import |
| `bbedit/` | BBEdit (macOS) | Codeless language module: keywords, strings, `{--` comments | copy `PAD.plist` to `~/Library/Application Support/BBEdit/Language Modules/` |
| `kate/` | Kate / KWrite / Qt Creator | XML syntax definition with HTML include | copy `pad.xml` to `~/.local/share/org.kde.syntax-highlighting/syntax/` |
| `emacs/` | Emacs | `pad-mode` derived from `mhtml-mode` with PAD font-lock and `{-- --}` comments | `(add-to-list 'load-path ".../editors/emacs") (require 'pad-mode)` |
| `padrun.sh` | shared | Renders a `.pad` file through `http://localhost/pad/` (used by the Sublime build and the VS Code task) | - |
| `render.php` | shared | Renders a page of any application on the command line, no server needed: `php editors/render.php <app> [<page>]` - status 1 and the JSON error of `pad/error/claude.php` when PAD reports one (used by the language server's diagnostics) | - |
| `fixture/` | tests | The application `shop` the tooling tests run against (`editors/lsp/test.js`) | - |

JetBrains IDEs (PhpStorm): install the bundled "TextMate Bundles" plugin,
then Settings > Editor > TextMate Bundles > `+` and select `editors/vscode/pad`
- the VS Code grammar is imported directly.

Not covered: a Tree-sitter grammar (needed for native Zed/Helix
highlighting) - a separate parser project; the LSP covers completions there.

The completion data (`vscode/pad/completions.json`, `lsp/completions.json`,
`sublime/PAD.sublime-completions`) is generated from the framework source by
`php editors/generate.php` - tags, pipe functions, properties, options (including the
handling options), type prefixes and sequence types, each from its directory under `pad/`.
Regenerate after adding a built-in; `./ci.sh` runs `php editors/generate.php --check` and
fails while a list is stale.
