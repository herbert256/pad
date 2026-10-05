# CLI Application

## Introduction

The `pad` command - new, serve, render, lint - and the command-line application it runs when it is given no command.

## Install

Link it into your PATH once:

```bash
ln -s /path/to/pad/apps/cli/pad /usr/local/bin/pad
```

## Commands

```bash
pad new shop                       # a new application: apps/shop/ and www/shop/index.php
pad new shop/orders/list           # a new page in it: list.php + list.pad
pad serve [port] [host]            # PHP's built-in server over www/, no Apache needed
pad serve 8000 --mount=pad         # the same under /pad/, the way Apache mounts it here
pad render demo clock              # any application's page to stdout
pad render shop search q=shoes     # with request values, as ?search&q=shoes would set them
pad lint shop                      # every page rendered under the strict check
pad lint shop orders               # only the pages of one directory
pad help                           # the list
```

- **new** never overwrites: an application or page that exists is refused. The application
  of `pad new a/b/c` is the shortest part of the name with an entry point in `www/`, so a
  nested one (`regression/pages`) works too.
- **serve** runs `php -S` over `www/`. `www/pad.php` derives the mount prefix from
  `SCRIPT_NAME`, so every application is at `http://127.0.0.1:8000/<app>/`. Four workers by
  default (`--workers=n`), so a page may fetch another page of the same server.
- **render** runs the page in this process, as a GET. `PAD_HOST` sets the server the page's
  own cross-application links point at.
- **lint** renders each page in a child process of its own, four at a time, with the strict
  syntax check on whatever the application chose, and lists every failure with its place in
  the template:

  ```
  ok    index
  FAIL  orders/list  Field '$totl' not found
        apps/shop/orders/list.pad:14:11  {$totl | money}  - did you mean $total?

  2 pages, 1 failed
  ```

Without a command word, `pad` runs this application: `pad` renders `index.pad` ("Hello
world"), `pad mypage` the page named.

The repository is the one the script stands in, unless the `PAD_HOME` environment variable
names another - a second checkout runs its own engine, and the children of `serve` and
`lint` inherit the same answer.

## Files

| File | Description |
|------|-------------|
| `pad` | The command: dispatches a command word to `_commands/`, else runs this application |
| `_commands/` | One file per command - `new`, `serve`, `render`, `lint`, `help` - and `lib.php` they share |
| `index.pad` | Default template (outputs "Hello world") |
| `_config/config.php` | CLI-specific configuration |

## Output Type

For CLI applications, set the output type in `_config/config.php`:

```php
$padOutputType = 'console';
```

## Capturing sample data

`pad sample <app> <page>` renders a page of another application once, for real, and writes
the variables its PHP made - with the answers of its named database tags - to that
application's `_samples/<page>.json`, the file the designer preview reads
(`?<page>&padSample`, see `pad/lib/sample.php`). The rendered page goes to standard output,
the name of the file to standard error:

```bash
./pad sample shop orders > /dev/null
# pad sample: wrote apps/shop/_samples/orders.json
```

Review the file before committing it: it holds whatever the page read, real data included.
A page rendered on the command line with the `web` output type is written as `console`.

## Exit Status

The process status reports how the request ended, so scripts can test it: 0 when the
request finished with a 2xx or 3xx status, 1 for everything else - a PAD error, a missing
page, a request the boot net had to end. A failed request also prints a machine-readable
JSON error body, but the status alone is enough for a shell gate:

```bash
pad render shop checkout || echo "render failed"
pad lint shop            || echo "a page failed"
```

## Other Applications

`apps/cli/pad` renders the cli application. `php editors/render.php <app> [<page>]` renders
a page of any application the same way, with the same exit status - the editor tooling's
diagnostics use it.
