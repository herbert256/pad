# PAD support for Visual Studio Code

Local extension providing PAD template language support.

Install: copy (or symlink) this directory to
`~/.vscode/extensions/herbert.pad-lang-0.1.0`, then reload VS Code.

| Piece | Provides |
|-------|----------|
| `syntaxes/pad.tmLanguage.json` | Base grammar: the file is standard HTML |
| `syntaxes/pad-injection.tmLanguage.json` | Injection grammar: PAD tags, comments, constructs recognized everywhere (incl. attribute values); `{ignore}` blocks stay PAD-free |
| `package.json` configurationDefaults | Token colors: purple bold tags, teal braces (like HTML's `< >`), red `$`/`%` sigils, lemon variable names |
| `extension.js` | Completions for all built-in tags / pipe functions / properties / options / prefixes / sequence types (from `completions.json`, generated from `pad/`); type `{/` to complete the open tag; `alt+cmd+.` closes the innermost open tag |
| `language-configuration.json` | Auto-closing `{}` and quotes, `{-- --}` comment toggling, auto-indent for block tags |
| `snippets/pad.code-snippets` | `padif`, `padloop`, `paddata`, `padcase`, `padignore` |

The repo's `.vscode/tasks.json` adds **PAD: render page** (default build task,
`cmd+shift+B`): renders the current `.pad` file through
`http://localhost/pad/` via `editors/padrun.sh`.

`completions.json` is generated from the framework source
(`pad/tags/`, `pad/functions/`, `pad/properties/`, `pad/options/`,
`pad/sequence/types/`) - regenerate after adding built-ins.
