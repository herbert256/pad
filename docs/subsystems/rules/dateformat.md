# rule:dateformat

Strict formats: `Y-m-d`, `d/m/Y`, `m/d/Y`, `Y-m-dTH:i`. Leading zeroes and four-digit years 0001..9999 are mandatory. Gregorian round trips reject overflow; datetime-local values allow hours 00..23 and minutes 00..59. No timezone inference or suffixes. Empty values remain optional unless paired with required. PHP/browser share committed fixtures.

## Example

```pad
{input:field type='text', 'date', label='Date', rules='dateformat:Y-m-d'/}
```

Run the focused fixture with `php tests/subsystems/rule-dateformat.php`.
