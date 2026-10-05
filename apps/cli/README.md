# CLI Application

## Introduction

The `pad` command - new, serve, render, lint, export, test - and the command-line application it runs when it is given no command.

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
pad export demo out/               # a static copy for any static host
pad test shop                      # the application's own tests, in apps/shop/_tests/
pad test shop --record             # ... writing the answers that are missing
pad test --all                     # every application that has tests
pad help                           # the list
```

- **new** never overwrites: an application or page that exists is refused. The application
  of `pad new a/b/c` is the shortest part of the name with an entry point in `www/`, so a
  nested one (`regression/pages`) works too.
- **serve** runs `php -S` over `www/` - with `$padReload = 'local'` in an application's
  config, a saved file shows in the browser at once. `www/pad.php` derives the mount prefix from
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

- **export** renders every page as the web gets it - the web output type, tidy as the
  application sets it, no toolbar - into `<dir>/<page>.html` (`orders/list.html` for a page
  in a subdirectory), and copies what `www/<app>/` holds beside the entry point (less `.php`
  files and `_` names). Links are rewritten: `?page`, `/app/?page` and the absolute form
  become the relative `page.html` when that page was exported, and a link to an application
  file (`style.css`) the relative path to its copy, so a page one directory down still finds
  it. Query values after the page name cannot be static: such a link lands on the page as it
  renders without them. A page that fails or answers nothing is reported and left out.

- **test** runs the application's own tests. A test is a page in `_tests/` - `cart.pad`, and
  `cart.php` when it needs data - next to its answer `cart.txt`: the exact output, a
  `/regex/` over it, or `HTTP 500` with an optional `/regex/` on the next line, the forms
  the framework's suites use. Each test page renders bare, in a process of its own, with
  every `{assert}` checked; no URL reaches `_tests/`. A test without an answer counts against
  the run until `--record` writes it from what the page renders now (an existing answer is
  never overwritten). `./ci.sh` runs `pad test --all --brief` as its `apptests` line.

  ```
  shop
    ok    cart
    FAIL  checkout
          want: <p>Total 42</p>
          got:  HTTP 500 - assert failed: $total eq 42
                apps/shop/_tests/checkout.pad:2:1  {assert $total eq 42}
    2 tests, 1 failed
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
| `_commands/` | One file per command - `new`, `serve`, `render`, `lint`, `export`, `test`, `help` - and `lib.php` they share |
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
