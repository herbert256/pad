# Holidays

Country kinds in `types/` provide holiday rules, supported years, languages and region names. `_lib/holidays.php` computes dates and substitute days; `_lib/merge.php` combines requested country/region calendars while preserving their origins.

```php
include_once PAD . 'subs/holidays/_lib/holidays.php';
$rows = padHolidaysMerge(
  ['nl', 'de-by', 'gb-sct'],
  padHolidaysDay(2026, 1, 1),
  padHolidaysDay(2026, 12, 31)
);
// A string result is a validation message; an array contains the merged rows.
```

`padHolidaysMerge($places, $from, $to, $lang='')` accepts a comma-separated text or an array of one to sixteen country/region codes. Bounds are civil-day integers, inclusive, ordered, and span at most one hundred years within 1583–9999. Every requested country's known-year range is checked before any rows are returned. Invalid places, regions, ranges and languages return a validation message; callers report it in their own context.

Duplicate place codes are normalized and removed. Rows sort by date, normalized place code and original rule order, so changing input order cannot change the result. Holidays from different requested places are retained separately even when their names and dates coincide.

Each row retains `date`, `name`, `local`, `type`, `region` and `substitute`, adding:

| Field | Meaning |
| --- | --- |
| country / countryName | Source country code and display name. |
| place | Complete requested code, such as `de-by`. |
| sourceRegion / sourceRegionName | Requested region and its display name, including for nationwide holidays. |
| language | Language used for the local holiday name. |

The existing `region` field still describes whether the holiday itself is regional. For example, New Year's Day from the `de-by` calendar has `sourceRegion='by'` and `region=''`.

Without `lang`, each country's first supported language is used independently. An explicit language must be supported by every requested country. `padHolidaysMergedTable($rows,$caption)` renders dates, calendar provenance and escaped names, including each local name's own language and observances marked as no day off.
