# Framework regression cases

Each directory here is one group, shown by the page of the same name. A file in it returns a
list of cases, and a case is:

    [ 'what it checks',
      <<<'PAD'
      {if 1 eq 1}
        yes
      {/if}
      PAD,
      'yes' ],

The template is rendered with `padSandbox()` - its own scope, so one case cannot see or
disturb the next and the order they run in cannot change the outcome - and compared with the
expected output. A case that needs a variable or a store sets it up itself, in the same
template.

Write the expected value as a regular expression between slashes when the answer cannot be
stated, such as anything derived from the current time. Slashes at **both** ends are what
makes it a pattern: `/test` is an ordinary value that `afterLast` produces.

A case may carry a fourth entry when the template alone cannot set the scene:

- **`'scope'`** renders with `padCode()` in the page's own scope instead of sandboxed. That is
  for engine code no tag can reach on its own, and only `sequence/library.php` needs it; such a
  case depends on its page setting up whatever it reads, so it gives up the isolation the
  others have.
- **an array** of `name => value` variables to put in place first. A nested pass binds the
  globals that exist when it opens, so these reach the template even sandboxed, and arrays of
  any depth come through - `{$a.b.c}` reads one. They are dropped again afterwards.

The array form is what lets a page whose data came from a paired `.php` file be a case at all:
the data is stated in the case, beside what it is supposed to produce, instead of in a second
file. `check/setup.php` is nothing but proof of the three shapes it has to carry.

## Layout

One statement to a line, what a tag encloses indented under it, and a pair with nothing
between it written as the single `{tag/}` form. The line breaks that introduces are removed
before the comparison, so the expected value states the output and nothing else. Spacing
written within a line is kept, and is the only spacing a case can assert.

## What this suite cannot check

A template that raises a PAD error ends the whole request, so a case cannot assert that one
was raised - the page would never render to report it. The following end the request and are
therefore absent, each verified by hand:

    {echo 10 | / 0}                     Division by zero
    {$nosuchvariable}                   PAD: Field '$nosuchvariable' not found
    {if first@xs}...{/if}               PAD: More then one reault back: first@xs
    {echo %t}                           Unknown format specifier "t"

## Known defects recorded as cases

Where the engine does something a reader would not expect, the case states what it *does* and
is named for it. The case passes today; if the defect is fixed the case fails, which is how a
suite is supposed to carry a known problem.

| case | what it records |
|------|-----------------|
| `tags/constructs.php` - a walking tag makes every row its own last | the manual says walking breaks the properties, and it does; the case pins it so a fix is noticed |

## One thing a case must not assume

**The four stores outlive a case.** `padSandbox()` isolates variables, not stores: the data,
content, bool and sequence stores are request-scoped on purpose, so a name one case pushes is
visible to every case after it. A case that needs a name to be *undefined* has to pick one no
other case anywhere could define - `{b}` in `escaping/` found a sequence store the sequence
group had pushed and iterated it.

## A spelling that reads as something else

`{if first@items}` - a property inside a condition does not read as a property at all. Within
an expression `@` is the current-value placeholder, so `first@items` tokenises as three things:
`first`, a property with no target, which is empty; `@`, the value being piped in; and `items`,
the tag name, which resolves to its whole data array. It is that array the operator then meets,
and comparing an array against a value is not implemented, so the request ends. The working
form is the tag pair, `{first@items}...{/first@items}`, which is what the documentation shows.
There is no case, because there is nothing correct to assert.

## Coverage of the evaluator

The `expressions` group is written against `pad/eval/`, one case for each shape that reaches a
file there: 37 of its 38 files are entered when the suite runs.

The one that is not is `eval/actions/singleRight.php`, and no case can reach it. It is meant for
a unary operator followed by another operator, but `padEvalDouble()` collapses every adjacent
operator pair before the precedence walk begins, so the branch in `lib/eval/operations.php` that
names the file never fires. The comment in the file itself records this.

## Coverage of the manual

Every demonstration in the manual application has a case here stating its answer. The ones
easiest to overlook, and where they live:

- merging a content store with the content of the tag using it - `@content@` from either side,
  `merge=` in its three spellings, and a store carrying its own `@else@`: `tags/content.php`.
- a page *variable* written as a tag, beside the PHP *function* form of `tags/returns.php`:
  `variables/astag.php`.
- the levels page's table built from two separate stores nested, the same name at both levels,
  the data tag reading a file named as its second parameter, and a bare `{sequence 3}`:
  `data/levels.php`.
- the parse order, that the innermost tag is the first resolved: `tags/parse.php`.
- the `array:` tag form: `prefixes/`.

`{script:}` is not here and not missing: `pages/tags/script` covers it, over the four
interpreters that are portable.

Two things the manual described could not be turned into cases here, and are `pages/` tests
instead - a case renders in a nested pass with a scope of its own, and neither of these is
about a template, it is about a request:

- the row phase of a *streaming* callback, which reads the occurrence's fields as plain PHP
  variables. Those fields are the nested pass's locals rather than globals, so the callback
  cannot see them. `pages/callback` is a real request and does. The init and exit phases and
  the whole before form are covered here.
- the pages that are only about how PHP and HTML files pair up. `pages/pairing` is that pair,
  and `pages/phponly` the half of it with no template at all.

The same split is why `object:` has a case in both: `expressions/references.php` has to render
in the runner's own scope because a sandboxed pass has the application globals taken out of it,
and `pages/globals` is the same reference in a request, where a page variable is a global like
any other.

## Coverage of the tags and options

The `tags` and `options` groups reach 71 of the 112 files under `pad/tags/`, `pad/options/` and
`pad/types/`. What the rest need is outside what a case can state, except where a spelling
pins it down - where one does, the case says which trick carries it:

- ending the request - `redirect`, `restart`, `exit`, `error`, `exception`, `dump`, and the
  `demand` option on a tag that produced nothing; these are pages tests in regression2
- a database - `field`, `record`, `array`, `check` - pages tests in regression2
- the network - `get` and `curl` are cases, pinned by shape and by a `SELF://` fetch of a
  stable regression2 page, which needs the local server the suite already needs; `ajax` is
  pinned by shape; `page` and `reactData` are pages tests
- the response mode - `output` has a case naming `'web'`, the mode the suite is already in,
  which is the only one safe to name mid-request
- the filesystem - `files` and `dir` list this application's own scan/ directory, whose
  content is part of the suite; `file` writes, and is a pages test
- output that differs every run - the `dump` option (a state dump under DATA on every run is
  a side effect a suite must not repeat), and `trace`, whose output is machine-bound and
  lives as a pages test in regression2 with a one-word answer
