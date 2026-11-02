# input:recurrencepicker

Native fieldset emits `{kind,start,timezone,interval,count,byday,exclude}`. Daily/weekly/monthly/yearly subset; interval/count 1..100. Weekly byday accepts comma-separated MO..SU; other frequencies reject it. Explicit exclusions are newline/comma-separated dates/instants. `padInputRecurrence($posted)` validates through padOccurrences and returns kind/rule plus preview for one year. Call it server-side before saving. Submitted invalid values are retained, with an error and correction controls. Submit updates authoritative preview; reset uses native form behavior. This deliberately has no independent browser recurrence implementation.

## Example

```pad
{input:form 'recurrencepicker'}{input:recurrencepicker 'schedule', label='Schedule'/}<button type='submit'>Preview</button><button type='reset'>Reset</button>{/input:form}
```

Run the focused fixture with `php tests/subsystems/input-recurrencepicker.php`.
