# pad-lsp - minimal PAD language server

Zero-dependency Node server (stdio). Provides:
- completion of all built-in tags, pipe functions, properties, options,
  type prefixes, operators and sequence types (from `completions.json`,
  generated from `pad/`)
- close-tag suggestions after typing `{/` (innermost open tag first)

Run: `node /Users/herbert/pad/editors/lsp/pad-lsp.js` (spoken over stdio by
the editor - never started by hand).

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

(Helix/Zed syntax *highlighting* additionally needs a Tree-sitter grammar,
which does not exist for PAD - the LSP gives them completions only.)

### Emacs (eglot)

```elisp
(add-to-list 'eglot-server-programs
             '(pad-mode . ("node" "/Users/herbert/pad/editors/lsp/pad-lsp.js")))
```
