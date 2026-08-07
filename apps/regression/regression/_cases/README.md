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

## Known defects recorded as cases

Where the engine does something a reader would not expect, the case states what it *does* and
is named for it. The case passes today; if the defect is fixed the case fails, which is how a
suite is supposed to carry a known problem.

| case | what it records |
|------|-----------------|
| `functions/encoding.php` - url encodes a space as `+` | matches `urlencode`; FUNCTIONS.md used to show `%20` and has been corrected |

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

- `sequence:<type>` inside an expression. The action half works - `sequence:sum([1,2,3])` and the
  `action:` prefix both answer - but a name matching a directory in `sequence/types/` does not.
  `sequence/sequence/sequence.php` runs `actions/set.php`, which files the name as the *action*
  and picks the build from the first argument, so `sequence:fibonacci(8)` looks for an action
  called fibonacci and ends the request. Nothing in the repo or the documentation shows what that
  form is meant to mean, so there is nothing to assert either.

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
