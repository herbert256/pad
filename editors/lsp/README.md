# pad-lsp - the PAD language server

Zero-dependency Node server (stdio, Node 18 or newer). Provides:

- **completion** of all built-in tags, pipe functions, properties, options, type prefixes,
  operators and sequence types (from `completions.json`, generated from `pad/` by
  `editors/generate.php`), and close-tag suggestions after typing `{/` (innermost open tag
  first)
- **diagnostics** when a page is opened or saved: PAD itself renders the page on the
  command line (`php editors/render.php <app> <page>`) under the strict syntax check, and
  the JSON error of `pad/error/claude.php` becomes a diagnostic on the tag it names - `the
  pair {if 1 eq 1} never closes`, `Field '$typo' not found`, a PHP error in the paired
  `.php` with its file and line. A `_inits.pad` / `_exits.pad` is checked through the
  `index` page of its directory; snippets in `_include/`, `_tags/` and the other `_xxx`
  directories have no page of their own and are not rendered. The render runs the page's
  `.php` as a GET request on the command line would - the same as opening it in a browser
- **hover** with the reference text of `docs/reference/*.md` for the tag (`TAGS.md`),
  pipe function (`FUNCTIONS.md`), option (`OPTIONS.md`, `HANDLING.md`), property
  (`PROPERTIES.md`), type prefix (`TYPES.md`) or construct (`CONSTRUCTS.md`) under the
  cursor; for an application's own tag, include or function the file and the comment at
  its top; for a field (`{$total}`, `$total` in an expression) the lines that assign it
- **go-to-definition** in PAD's own lookup order:

  | Under the cursor | Goes to |
  |------------------|---------|
  | `{mytag}` | the nearest `_tags/mytag.php` / `.pad` from the page's directory up to the application root, then `_common/_tags/`, then the engine's `pad/tags/mytag.php`; failing those a `{data 'mytag'}` block of the template, `_include/mytag.pad`, or the line that sets `$mytag` |
  | `{app:x}` `{common:x}` `{include:x}` `{pad:x}` | that type's file only |
  | `\| money` | `_functions/money.php` up the directories, then `pad/functions/money.php` |
  | `callback='x'` | `_callbacks/x.php` up the directories |
  | `{$total}`, `$total`, `%total` | the line of the paired `orders.php` that assigns it, a `{set $total = ...}` of the template, then `_inits.php`, `_exits.php` and `_lib/*.php` up the directories; when nothing assigns it, an array key `'total' =>` (a row field) |
  | `first@items`, an option, `app:` | the engine file under `pad/properties`, `pad/options` / `pad/handling/types`, `pad/types` |

The application a file belongs to is found from the path: nested applications
(`apps/regression/errors`) are recognised by their entry point under `www/`.

Run: `node /Users/herbert/pad/editors/lsp/pad-lsp.js` (spoken over stdio by the editor -
never started by hand). Diagnostics need `php` on the `PATH`.

## Settings

Passed as `initializationOptions` (all optional):

| Option | Default | Meaning |
|--------|---------|---------|
| `php` | `php` (or `$PAD_LSP_PHP`) | the PHP binary the diagnostics render with |
| `diagnostics` | `true` | `false` switches the renders off |
| `timeout` | `15000` | milliseconds one render may take |

## Test

```bash
node editors/lsp/test.js     # speaks LSP to the server against editors/fixture/apps/shop
```

`./ci.sh` runs it as its `lsp` line (skipped when `node` is not installed).

## Client configuration

### Neovim (built-in LSP)

```lua
vim.filetype.add({ extension = { pad = 'pad' } })
vim.api.nvim_create_autocmd('FileType', {
  pattern = 'pad',
  callback = function()
    vim.lsp.start({
      name = 'pad-lsp',
      cmd = { 'node', '/Users/herbert/pad/editors/lsp/pad-lsp.js' },
      root_dir = '/Users/herbert/pad',
    })
  end,
})
```

`K` shows the hover, `gd` (or `vim.lsp.buf.definition()`) jumps, and the diagnostics
appear after `:w`.

### Sublime Text (Package Control: "LSP")

`Preferences > Package Settings > LSP > Settings`:

```json
{
  "clients": {
    "pad-lsp": {
      "enabled": true,
      "command": ["node", "/Users/herbert/pad/editors/lsp/pad-lsp.js"],
      "selector": "text.html.pad"
    }
  }
}
```

### Helix (`~/.config/helix/languages.toml`)

```toml
[language-server.pad-lsp]
command = "node"
args = ["/Users/herbert/pad/editors/lsp/pad-lsp.js"]

[[language]]
name = "pad"
scope = "text.html.pad"
file-types = ["pad"]
language-servers = ["pad-lsp"]
```

Highlighting, folding and text objects in Helix (and Zed) come from the Tree-sitter grammar
in `editors/tree-sitter-pad` - its README has the `[[grammar]]` entry.

### Emacs (eglot)

```elisp
(add-to-list 'eglot-server-programs
             '(pad-mode . ("node" "/Users/herbert/pad/editors/lsp/pad-lsp.js")))
```
