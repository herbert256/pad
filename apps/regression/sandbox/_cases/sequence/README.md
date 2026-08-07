# Sequence regression cases

The sequence subsystem, as one group of the framework suites. The case format, the layout and
the whitespace rule are the same for every group and are described once in
[../README.md](../README.md); what follows is only what is particular to this group.

Add a case whenever a defect is fixed. The value of this suite is that it fails when
something changes; a case that only restates what the code already does adds nothing.

## Particular to this group

Two forms are deliberately left written out rather than shortened to `{tag/}`: the cases
named "on a closed tag" in `stores.php`. `{tag}{/tag}` and `{tag/}` are not the same path
through the engine, and a `name=` that stored only on the second was the defect those two
cases exist to catch - shortening them would quietly retire them.

`library.php` is the only group in the whole suite that uses the fourth `'scope'` entry. Its
cases reach `pqTruncate()` through the engine's trim option, which no sequence tag calls, so
they are rendered with `padCode()` and read `$seqFixture` - the list `sandbox/sequence.php`
sets up for them.

| file | covers |
|------|--------|
| `types.php` | the first eight terms of every deterministic sequence type |
| `parameters.php` | how a type's parameter may be written: number, range, store name, percentage |
| `options.php` | rows, from, to, sole, minimal, maximal, stop, skip, unique, increment, randomly |
| `actions.php` | the 29 actions, including their empty and missing-store boundaries |
| `plays.php` | make, keep, remove and flag in each of the five ways they can be written |
| `stores.php` | push, pull, name and resume |
| `membership.php` | keep/remove/flag answers matching what the type generates |
| `library.php` | `pqTruncate()` through the engine's trim option |

## What this suite cannot check

A template that raises a PAD error ends the whole request, so a case cannot assert that one
was raised - the page would never render to report it. The guards below therefore have to be
checked by hand. Each should answer with the message shown, not with a PHP error and not by
producing values.

    {sequence prmie, rows=3}                 Sequence 'prmie' is not a sequence type, a store or an action
    {sequence add='abc', rows=3}             The add sequence needs a number as its parameter, not 'abc'
    {sequence divide=0, rows=3}               The divide sequence cannot divide by zero
    {sequence multiple=0, rows=3}             The multiple sequence needs a step of 1 or more
    {sequence chance=0, rows=3}               The chance sequence needs a parameter of 1 or more
    {sequence chance='abc', rows=3}           The chance sequence needs a number or a percentage …
    {sequence random=0.5, rows=3}             The random sequence needs a parameter of 1 or more
    {sequence loop, push='sum'}               Store name 'sum' can not be equal to an Action name

These must also finish rather than run on. Each answers with an error or with no terms; none
may occupy the request:

    {sequence happy, from=0, rows=5}          predicate at zero
    {sequence powerful, from=0, rows=5}       predicate at zero
    {sequence loop, from=0, rows=4, keep='power|2'}   predicate at zero, as a play
    {sequence loop, from=1, to=10, increment=0}       a step that never advances
    {sequence range, from=1, to=9, increment='0'}     the same through the range type
