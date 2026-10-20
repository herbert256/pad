# PAD Evaluation Subsystem

This document explains how the PAD expression evaluator works.

## Overview

The evaluator turns the text of an expression - `{echo $price * 1.1 | round}`, the condition
of an `{if}`, a `where=` option - into a value. It works in four steps:

```
text ── validate ── scan ── parse ── evaluate ── value
        (shape)     tokens   tree     with the value piped in
```

The tokens of a clean expression and its tree are kept for the request, so a loop that
evaluates the same text once per row scans and parses it once; each evaluation then reads
the expression's fields and walks the tree.

| File | Does |
|------|------|
| `lib/eval/eval.php` | `padEval`, `padEvalBool`, `padEvalNumber`, `padEvalTrace` - the public face |
| `eval/eval.php` | The entry: fast paths, then validate, scan, check, parse, evaluate |
| `lib/eval/validate.php` | The checks before parsing: brackets, strings, words, operands |
| `lib/eval/scan.php` | The scanner: the text as tokens |
| `lib/eval/tree.php` | The parser: the tokens as a tree |
| `lib/eval/run.php` | The evaluator: the tree as a value, the operators, calls and lookups |
| `lib/eval/const.php` | The operator tables and binding powers |
| `eval/parms/<kind>.php` | A call with arguments - a built-in, an application's function, `php:`, a tag ... |
| `eval/single/<kind>.php` | A lookup without arguments - `field:`, `data:`, `array:` ... |
| `eval/fast.php` | An expression that is only the name of a built-in pipe function |

## Entry Point

`padEval ( $eval, $value, $pipe )` reaches `eval/eval.php` through the try guard
(`try/eval/eval.php`). `$value` is the value piped in; `$pipe` says the text is a pipe body -
what `{$x | ...}` or a tag pipe applies - whose head is a function as well.

```php
// A plain name of a built-in pipe function: eval/fast.php calls it directly
// A number, a quoted string without quote or backslash, a plain $field: answered at once

$tokens = padEvalTokens ( $eval );                  // validate and scan, kept per request
if ( $tokens === FALSE ) return '';                 // the validation named the fault
if ( ! padEvalCheckPipes ( $tokens, $eval, $pipe ) ) return '';
$tree = padEvalTreeGet ( $tokens, $eval, $pipe );   // parse, kept per request
return padEvalSegments ( $tree, $value );           // the leaves, then the segments
```

## Step 1: Validation (`lib/eval/validate.php`)

Before anything is scanned, `padEvalValidate` checks that every string is closed and that
`( )` and `[ ]` are balanced and nested, naming the position in characters. After scanning,
`padEvalCheckPipes` judges the tokens: a word after a `|` must name a function, an operator
needs its operands where no piped value stands in, `===`, a lone `:`, a `=>` outside a list,
punctuation words, a `not` that denies no test. A text that passed is not judged again in
the request when its words alone decide it.

## Step 2: Scanning (`lib/eval/scan.php`)

`padEvalScan` splits off the pipe segments that are a printf format (`%05.2f`) and
`padEvalScanText` reads the rest, each kind of token with a function of its own. A token is
`[0]` its text or value and `[1]` its kind:

| Kind | Meaning | Example |
|------|---------|---------|
| `VAL` | A number, or a quoted string (`'lit' => TRUE`) | `'hello'`, `123`, `1.5E-10`, `4..10` |
| `OPR` | An operator, `[2]` as written when it is a symbol | `+`, `**`, `LT` (from `<`), `AND` (from `&&`), `NEG` |
| `$` | A field | `$name`, `$user.name`, `$items@2` |
| `$$n` | The field another field names | `$$pick` |
| `prop` | A property and its target | `first@items`, `current@` |
| `&` | A property of the tag | `&current` |
| `#` | An option of the tag | `#title` |
| `$$` | The `@` placeholder - the value piped in | `@`, `$$` |
| `hex` | The digits of a hex value | `0x41` |
| `other` | A bare word - an operator word, a function, a reference, a constant | `and`, `upper`, `php:strlen`, `TRUE` |
| `pipe` `open` `close` `a-open` `a-close` `arrow` | `\|` `(` `)` `[` `]` `=>` | |
| `%` | A whole pipe segment that is a printf format | `%05.2f` |

Whitespace and commas end a token and are dropped; the token after a comma inside brackets,
and after a `=>`, carries `'arg' => TRUE`.

- Strings take `'` or `"`, with the escapes `\n` `\r` `\t` `\\` `\'` `\"`
- A `.` before a digit opens a number - `.5` - after a comma or where no value stands
  before it; after a value it is the concatenation: `'a'.1` is `a1`, `[1, .5]` is 1 and .5
- A `-` or `+` before a number is its sign only where no value stands before it: `5 -3` is a
  subtraction, `5 * -3` keeps the sign. Before anything else it is the prefix `NEG` or
  `POS` where an operator, a bracket or a comma stands before it - `5 * -$b`; at the head
  of an expression or a pipe segment it is the operator whose left side is the piped value
- A field's name runs on as far as the at-syntax allows: dotted paths, `name@tag`, prefixes
  with `:`, the `<`, `>` and `*` of a path - `$u.tags.1>` is the first tag from the end

## Step 3: Parsing (`lib/eval/tree.php`)

The grammar, weakest first:

```
expression  segment ( | segment )*        each segment's answer is the input of the next
segment     a printf format, or a range
range       values ? range? : range       the inline ternary; its condition is one value
            values                        values side by side are joined as text
value       operators by binding power, prefix operators, calls
primary     a literal, $field, @, ( arguments ), [ list ], a call, a lookup
```

What a bare word is depends on where the expression stands: `padEvalWords` asks
`padTypeFunction` (or reads the `type:` prefix written) whether it names a function, a tag,
a field, a data store ... A word that names nothing is a constant (`padConstant`). A tree is
kept per text, per pipe body and per what its words name.

### Binding power

The operators with a value on both sides, strongest first - those of one power are taken
left to right, except `**`, which binds right to left (`2 ** 3 ** 2` is `2 ** 9`):

| Power | Operators |
|-------|-----------|
| 130 | a call after a value - `$s upper` - takes that value as its input |
| 120 | `**` |
| 110 | the prefix `!`, `-`, `+` - `-$b ** 2` is `-($b ** 2)`, `2 ** -$b` is `2 ** (-$b)` |
| 100 | `*` `/` `%` |
| 90 | `+` `-` |
| 80 | `.` |
| 75 | the tests `in`, `like`, `matches`, `range`, `between`, `contains` written after a value |
| 70 | `lt` `le` `gt` `ge` (`<` `<=` `>` `>=`) |
| 60 | `eq` `ne` (`=` `==` `!=` `<>`) |
| 50 | the prefix `not` - `not $a eq $b` denies the comparison |
| 40 | `and` (`&&`) |
| 30 | `xor` |
| 20 | `or` (`\|\|`) |
| 10 | `??` |

Weaker than all of them is the inline ternary `cond ? then : else`: the first `?` of a range
splits it at its own `:` (a nested `?` takes the next one), so `a ? b : c ? d : e` is
`a ? b : (c ? d : e)`. Nothing before the `?` means the piped value is the condition:
`{$n | ? 'some' : 'none'}`. With no then branch - `$title ?: 'Untitled'` - a true condition
is the answer itself.

### What an operator lacks, the piped value stands in for

- An operator where a value should be takes the piped value as its left side:
  `{echo 10 | - 3}` is 7, `{echo $x | * 2 + 1}` is `$x * 2 + 1`
- One operator closing a range takes everything before it as its left side and the piped
  value as its right one: `{echo 5 | 2 + 3 *}` is 25
- Of two operators side by side the first takes the piped value on both sides and stands as
  a value: `{echo 50 | '"' . . '"'}` is `"5050"`; an operator alone squares the value -
  `{echo 5 | *}` is 25
- A prefix operator alone works on the piped value: `{$flag | !}`

### Calls and tests

- A function with brackets takes its arguments from them; without them it takes the one
  value right after it, when one follows: `$s like 'h%'`. A test after it is no argument -
  it tests the function's answer: `{$s | lower in ('a', 'b')}`
- Written after a value, a call takes that value as its input - `$s upper`; a test takes the
  arithmetic and the concatenation on its left: `$n + 1 range (9, 9)`. A `not` between the
  value and a test denies the test: `$s not in ('a', 'b')`
- A built-in written as a call with nothing piped into it - at the head of an expression
  that is no pipe body, or as an argument of another call - reads its first argument as the
  value: `{echo upper($s)}`, `replace('l', upper('x'))`, and an application's function the
  same: `exclaim('ab')`. `date` keeps PHP's order: `date('Y', 0)` is 1970
- A lookup - `field:x`, `data:rows`, a bare word naming a field - takes no arguments

### Brackets and lists

An argument of `( )` and an item of `[ ]` is a range of its own: a comma ends it, a `=>`
ends the key of a list item. With a `|` of its own it is a pipeline whose head takes the
segment's value: `($n | + 1) * 2`, `upper($s | trim)`, `[$n | + 1, 2]`; an `@` after that
pipe is the value piped into its part. Values left side by side in `( )` stand side by side
where the brackets stood; after a function they are its arguments.

A `[ ]` list takes keys written with `=>` - `[ 'title' => 'Minutes', 'n' => $n + 1 ]` - mixed
with plain items as PHP mixes them (`padEvalArrayItems`).

### Nodes

A node is a list, its kind first:

| Kind | Holds |
|------|-------|
| `lit` | a value written in the template, and whether it was a quoted string |
| `leaf` | a token read before the tree: a field, an option, a property, a hex value, a constant |
| `me` | the value piped in - `@` |
| `fmt` | a printf format |
| `un` / `bin` | an operator and its operand(s), and how it was written |
| `tern` | condition, then (`NULL` for `?:`), else (`NULL` when empty) |
| `join` | values side by side |
| `list` | the entries of a `[ ]` list |
| `call` | kind, name, input, arguments, brackets, whether a built-in reads its first argument |
| `look` | kind, name, and whether it may be missing - its value going into a fallback |
| `pipe` | the parts of a pipeline inside brackets |
| `seq` | nodes read and left out, then a node |
| `err` / `none` / `more` | a fault reported where it is evaluated |

## Step 4: Evaluation (`lib/eval/run.php`)

`padEvalSegments` reads the leaves of the expression first, in the order written
(`padEvalLeaves`) - so under the strict check a missing field is named even in the branch a
ternary does not take - and then evaluates each segment with the previous one's answer as
its input. `padEvalNode` walks the tree: the branch a ternary does not take is left alone,
both sides of the other operators are evaluated.

### Operators (`padEvalApply`)

| Left | Right | Function |
|------|-------|----------|
| - | any | `padEvalUnary` - `!` and `not` answer `'1'` or `''`, the signs a number |
| scalar | scalar | `padEvalScalar` |
| array | array | `padEvalArrays` |
| array / scalar | scalar / array | `padEvalMixed` |

- The comparisons and the logical operators answer `1` for true and `''` for false; a
  boolean compares as PAD writes it, TRUE as 1 and FALSE as empty
- `.` concatenates; the arithmetic reads each operand as the number it spells
  (`padEvalNumber`) - text that is no number is an error under the strict check, 0 in the
  lenient walk
- `??` answers the left value unless it is empty - `''`, NULL, FALSE, an empty list, a
  missing field - and then the right one
- An array meets a scalar as one value: a list of one is that value, a list of numbers their
  sum against a number; the logical operators read an array's truth. Two arrays compare whole
  and are otherwise reduced to the sum of their leaves, or the leaves run together
- A date object is its text to an operator, except two dates compared with each other
- A NAN answer is empty

### Calls and lookups

A call goes to `padEvalCall ( $kind, $name, $value, $parm )`, which includes
`eval/parms/$kind.php` with `$value`, `$parm` and `$count` in scope; a lookup goes to
`padEvalLook`, which includes `eval/single/$kind.php` with `$name`.

### Single kinds (`single/`)

| Kind | File | Returns |
|------|------|---------|
| `field` | `single/field.php` | `padFieldValue($name)` |
| `data` | `single/data.php` | `$GLOBALS['padDataStore'][$name]` |
| `content` | `single/content.php` | `$GLOBALS['padContentStore'][$name]` |
| `property` | `single/property.php` | `padTagValue($name, 1)` |
| `parm` | `single/parm.php` | `padOptValue($name, 1)` |
| `array` | `single/array.php` | `padArrayValue($name, TRUE)` |
| `constant` | `single/constant.php` | `constant($name)` |
| `level` | `single/level.php` | `padGetLevelArray($name)` |
| `bool` | `single/bool.php` | `$padBoolStore[$name]`, FALSE when not set |
| `flag` | `single/flag.php` | `$padBoolStore[$name]` - an error when not set |
| `pull` | `single/pull.php` | The stored sequence `$pqStore[$name]` |
| `object` | `single/object.php` | An application global as an array (`padToArray`) |
| `local` | `single/local.php` | A `_data/` file's data |
| `include` | `single/include.php` | An `_include/` snippet, rendered |

### Kinds with parameters (`parms/`)

| Kind | File | Description |
|------|------|-------------|
| `tag` | `parms/tag.php` | Tag as function: `padTagAsFunction($name, $value, $parm)` |
| `pad` | `parms/pad.php` | A built-in pipe function, `functions/$name.php`, after its parameter count is checked |
| `php` | `parms/php.php` | A PHP function `$padPhpFunctions` allows |
| `app` | `parms/app.php` | An application's `_functions/` file |
| `function` | `parms/function.php` | Whichever function kind the name is |
| `action` | `parms/action.php` | A sequence action |
| `script` | `parms/script.php` | A `_scripts/` script |
| `sequence` | `parms/sequence.php` | Sequence generator |

## Fast Paths

An expression that is only the name of a built-in pipe function - one the application has
no function of that name in place of - goes to `eval/fast.php`, which calls the function
through `eval/parms/pad.php`. A number, a quoted string with no
quote or backslash in it, and a plain `$field` are answered in `eval/eval.php` before any
scanning - outside a pipe body. None of them is taken while the trace records.

## Tracing

With `$padEvalTrace` on, `padEvalTrace` records the expression, the tokens and the tree of
the last evaluation in `$_eval` - begun anew by each one, so an expression the validation
refused leaves its text alone there.

## Example

Expression: `$price * 1.1 | round`

1. **Scan**: `[price, $] [*, OPR] [1.1, VAL] [|, pipe] [round, other]`
2. **Words**: `round` names the built-in function - kind `pad`
3. **Parse**: two segments - `bin(*, leaf $price, lit 1.1)` and `call(pad, round)`
4. **Evaluate**: the leaf `$price` is read (123.45); the first segment is 135.795, which is
   the input of the second: `round` answers 136

## Boolean Handling

The comparison and logical operators answer `1` for true and `''` for false. A value is
tested for truth as PHP tests it:
- **False**: `''`, `'0'`, `0`, NULL, FALSE and an empty array
- **True**: everything else - an array when it has elements

## Error Handling

Under `$padCheckSyntax` (the default) errors are reported via `padError()`. Before anything
runs, `lib/eval/validate.php` reads the expression and names the fault and its position -
`Expression error: ...`:
- a `(` or `[` that is never closed, a `)` or `]` that closes nothing, a string never closed -
  inside a quoted expression `{users where='$admin eq 1)'}` gives "the ) at position 12 closes
  nothing that was opened"; written bare in a tag's parameters, `{echo 1 + 2)}`, the parameter
  split reports it first: "Closing ) without an opening ("
- a pipe function that does not exist, a word called like a function that names none
- a comparison or logical operator - or `??` - with nothing on its left or its right, where no
  pipe value stands in for it; an arithmetic one is not named: `{echo 5 *}` answers 0
- two operators side by side after a value, where no pipe value stands in for the operand
  between them - `$n + * 3`, `$n eq eq 3`; in a pipe the first of the two works on the piped
  value on both sides - `{echo 50 | '"' . . '"'}` is `"5050"`, the regression/common page
  misc/eval shows the form
- `===` and `!==`, named as the PHP spellings they are - `eq` (`==`) and `ne` (`!=`) compare
- a word of punctuation alone - `&`, `~`, `^`, `;` - and a `:` with no `?` before it
- a `=>` outside a `[ ]` list
- a `not` after a value with no test behind it
- a pipe operator written without its space - `| +1` for `| + 1`

While it runs:
- "there is no field named '$x'" - a missing field (`padEvalLeaves`), also as `field:x` and
  as the name `$$x` holds; "the field '$x' is a list, not a value", "... an object ...".
  A field whose value goes straight into a fallback may be missing (`padEvalTreeMissing`):
  the left side of `??`, the value piped into `default` or `optional` - with a prefix too -
  brackets around it or not, and the whole answer of an `{echo}` whose tag pipe begins with
  one (`padEvalFallbackPipe`). A field that is part of more - `$x + 1 | default(0)`,
  `'a' . $x ?? 'b'` - is no fallback's, and is named
- "'a' is not a number for +", "a division by zero in /" - arithmetic
- "the ? of an inline ternary has no :", "a branch of an inline ternary is empty", "the
  condition of an inline ternary is not one value"
- "the key 'a' of a list has no value after its =>", "a => in a list with no key before it"
- "the function 'replace' wants 2 parameters", with "after the value it works on" for a
  built-in written as a call, whose first argument is that value
- "the PHP function 'str_pad' refused its arguments: ..." - a wrong type or number, a value
  out of range, a division by zero, with a word on the piped value a call with arguments is
  not handed (`eval/parms/php.php`)
- "there is no value in '( )'", "a list and another value stand side by side with no operator
  between them", "More than one result back" - an expression that reduced to no single value
- "Unsupported \\ char" - Invalid escape sequence
- "Escape \\ char only allowed inside a string"

With the check off nothing is reported, and the evaluator answers what it can: a value that is
no number counts as 0 (`'a' + 1` is 1), a missing field is empty (`$nope + 1` is 1), an unknown
escape loses its backslash (`'a\qb'` is `aqb`), a ternary without its `:` keeps what it has
(`1 ? 'a'` is `1a`), and an expression that cannot be reduced - an unclosed bracket, a
division by zero, a PHP function refusing its arguments, a mistake the check before parsing
names - answers `''`.
