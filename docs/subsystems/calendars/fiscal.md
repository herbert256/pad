# calendar:fiscal

`periods=` is a caller-supplied array of label/start/end records. Boundaries are strict YYYY-MM-DD, start inclusive, end exclusive. Sorted periods must have contiguous coverage with no overlaps; optional from/to must exactly match coverage. Maximum 1000 periods. Labels can name quarters or 4-4-5 periods; no fiscal or leap-week convention is assumed.

## Example

The paired PHP page supplies the application data:

```php
<?php
$fiscalPeriods=[['label'=>'FY27 Q1','start'=>'2026-04-01','end'=>'2026-07-01'],['label'=>'FY27 Q2','start'=>'2026-07-01','end'=>'2026-10-01']];
```

The PAD template renders it:

```pad
{calendar:fiscal periods='fiscalPeriods', from='2026-04-01', to='2026-10-01'/}
```

Run the focused fixture with `php tests/subsystems/calendar-fiscal.php`.

Array-valued options accept a literal array or the quoted name of an application array/store.
