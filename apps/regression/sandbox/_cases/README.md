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

## Fixed, and kept as cases

Each of these was found by writing the case, and the case stayed once the defect was fixed, so
the day it comes back it fails here first.

| what was wrong | where it was fixed |
|----------------|--------------------|
| a lone `0` printed nothing - `A{echo 0}B` gave `AB`, and `key@`/`remaining@`/`done@` each lost a row | `level/go.php`, `level/start.php` - both tested `"0"` for truth |
| `{box/}` with no options never reached the tag | `level/tag.php` - the name is resolved again once the slash is off |
| an unclaimed tag's leftover depended on where it ran | `inits/vars.php` - `$padBetweenOrg` is declared at boot |
| `like()` ended the request on every call | `functions/like.php` - `$parm` is an array |
| `stripLow` raised "Unknown filter with ID 4" | `functions/stripLow.php` - the flag belongs in the third argument |
| `afterLast` left the delimiter on the answer | `functions/afterLast.php` - the whole delimiter is skipped |
| `{data 'x'}` died under `padCode`/`padSandbox` | `inits/vars.php` - the stores are declared at boot |
| `{else}` was documented but absent | `tags/if.php`, `tags/case.php` - each takes its own |
| `{parm:x}`, `function:` and `action:` ended the request on a missing include | `types/parm.php`, `eval/parms/function.php`, `eval/parms/action.php` |
| `parm:` answered `1` instead of the value | `lib/field/field.php` - `$lvl` was not forwarded |
| five properties could not be reached at all | `at/properties/` - the forwarders were missing |
| `after` ate a character and the `before` pair answered `''` on an absent delimiter | `functions/{after,before,beforeLast}.php` - the `afterLast` convention everywhere |
| `{break}` and `{continue}` threw away what the row had already printed | `level/nextLevel.php` - only the remainder goes |
| `{cease}` truncated after a constant key and never stopped over string keys | `tags/cease.php` - the loop's own key |
| `toContent=`/`toBool=`/`toData=` stored and printed anyway | `options/` - end-phase handlers blank `$padContent`, which the walker copies back |
| a bare-word ternary branch read as a property of the wrong level | `level/ternary.php` - one bare word is that word |
| `{first@-2}` landed one level short of `{first -2}` | `at/_lib/lib.php` - one formula for both resolution routes |
| `action:` in a pipe swallowed its value | `eval/parms/action.php` - passes it through |
| `@tidy@` shipped to the browser | `exits/tidy.php` - consumed either way |
| a flat list summed where the same values as data rows ran together | `lib/eval/reduce.php` - flattened before the numeric test |
| `encodeHigh` encoded bytes, so 'café' rendered mojibake | `functions/encodeHigh.php` - per character |
| `noError` was parsed and ignored, leaking the tag's own source | `level/no.php` - swallows the way optional does |
| `@providers` read by level index where `{reactData}` stores by id | `at/groups/providers.php` - the whole store is searched |
| `error=` reset on an option it does not carry, and nothing included it | `options/error.php`, `level/flags.php`, `try/catch/level/go.php` - the alias notOk= always was |
| `{if bool="name"}` did nothing - the raw option text fell through to padEval | `tags/if.php`, `options/bool.php` - and an unset flag is FALSE, not a truthy name |
| `demand` returned TRUE that nothing read | `level/flags.php` - a demanded tag that produced nothing ends the request |
| `script:` in an expression died on a missing include | `eval/parms/script.php` - the missing half of types/script.php |
| `{code reset/clean/sandbox}` died inside a nested pass | fixed in passing by the audit-era repairs; `tags/nested.php` asserts all four forms |

## Known defects recorded as cases

Where the engine does something a reader would not expect, the case states what it *does* and
is named for it. The case passes today; if the defect is fixed the case fails, which is how a
suite is supposed to carry a known problem.

| case | what it records |
|------|-----------------|
| `functions/encoding.php` - url encodes a space as `+` | matches `urlencode`; FUNCTIONS.md used to show `%20` and has been corrected |
| `tags/constructs.php` - a walking tag makes every row its own last | the manual says walking breaks the properties, and it does; the case pins it so a fix is noticed |

## Fixed after living here as a known defect

**`{code reset}`, `{code clean}` and `{code sandbox}` used to end the request inside a nested
pass** - `key($padData[$pad])` came back NULL in `occurrence/init.php` and the next line
indexed with it, so a two-line case died in the *outer* request's build. This section carried
the diagnosis for as long as no case could survive it. The audit-era engine repairs fixed the
crash in passing, and `tags/nested.php` now asserts all four option forms, including the
difference the crash used to hide: a plain pass and `reset` leak what the pass newly created,
`clean` and `sandbox` unset it (`start/pad/end.php` runs the unset for those two only).

## One thing a case must not assume

**The four stores outlive a case.** `padSandbox()` isolates variables, not stores: the data,
content, bool and sequence stores are request-scoped on purpose, so a name one case pushes is
visible to every case after it. A case that needs a name to be *undefined* has to pick one no
other case anywhere could define - `{b}` in `escaping/` found a sequence store the sequence
group had pushed and iterated it.

## Documented but not implemented

These appear in CLAUDE.md or `docs/reference/TAGS.md` and do not work. They are not cases,
because there is nothing correct to assert:

- `{if first@items}` - a property inside a condition does not read as a property at all. Within
  an expression `@` is the current-value placeholder, so `first@items` tokenises as three things:
  `first`, a property with no target, which is empty; `@`, the value being piped in; and `items`,
  the tag name, which resolves to its whole data array. It is that array the operator then meets.
  The working form is the tag pair, `{first@items}...{/first@items}`, and CLAUDE.md has been
  corrected to show it.

`sequence:<type>` used to be listed here as well. It is implemented: the argument is which term
of the type is wanted, so `sequence:fibonacci(8)` is the eighth Fibonacci number, and
`expressions/calls.php` has it.

`{else}` and occurrence variables used to be listed here and no longer are. `{else}` is
implemented; occurrence variables always worked - `%` marks the assignment and the value is
read back with `$`, which is what CLAUDE.md now says.

## Coverage of the evaluator

The `expressions` group is written against `pad/eval/`, one case for each shape that reaches a
file there: 37 of its 38 files are entered when the suite runs.

The one that is not is `eval/actions/singleRight.php`, and no case can reach it. It is meant for
a unary operator followed by another operator, but `padEvalDouble()` collapses every adjacent
operator pair before the precedence walk begins, so the branch in `lib/eval/operations.php` that
names the file never fires. The comment in the file itself records this.

## Coverage of the manual

The manual application has been read page by page against these groups twice. The second pass
found five things the first had missed, all of them now cases:

- merging a content store with the content of the tag using it - `@content@` from either side,
  `merge=` in its three spellings, and a store carrying its own `@else@`. Five sections of the
  content page and not one case: `tags/content.php`.
- a page *variable* written as a tag, which is the other half of the tag-return-values page from
  the PHP *function* form that `tags/returns.php` had: `variables/astag.php`.
- three of the twelve ways the levels page builds one table - two separate stores nested, the same
  name at both levels, and the data tag reading a file named as its second parameter - plus a bare
  `{sequence 3}`: `data/levels.php`.
- the parse order the parse page is entirely about, that the innermost tag is the first resolved:
  `tags/parse.php`.
- the `array:` tag form, the one of the prefix page's eleven that nothing reached.

`{script:}` looked missing here and is not: `pages/tags/script` covers it, over the four
interpreters that are portable.

The first pass, for the record: the data handling options, tag return values, `@start@`/`@end@`,
walking and what it breaks, callbacks, the open-tag against close-tag naming of a data or content
block, PAD's own list format and YAML, naming a property's tag four ways, the `?` and `$$`
variable kinds, and the six PAD entities.

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
`pad/types/`. What the rest need is outside what a case can state, or was until a spelling
was found that pins it down - where one was, the case says which trick carries it:

- ending the request - `redirect`, `restart`, `exit`, `error`, `exception`, and the `error`
  option; these are pages tests in regression2 instead
- a database - `field`, `record`, `array`, `check` - pages tests in regression2
- the network - `get` and `curl` are cases now, pinned by shape and by a `SELF://` fetch of
  a stable regression2 page, which needs the local server the suite already needs; `ajax`
  is pinned by shape; `page` and `reactData` are pages tests
- the response mode - `output` has a case naming `'web'`, the mode the suite is already in,
  which is the only one safe to name mid-request
- the filesystem - `files` and `dir` list this application's own scan/ directory, whose
  content is part of the suite; `file` writes, and stays a pages test
- output that differs every run - `dump` and the `dump` option (a state dump under DATA on
  every run is a side effect a suite must not repeat); `trace` broke two ways the moment a
  pages test asked, and lives in regression2 with a one-word answer

Two options are dead rather than untested, and each says so in its own file: `bool=` has a
handler that no phase list names, and `demand=` only returns TRUE and nothing reads it.
`noError` swallows an unresolved tag the way `optional` does.
