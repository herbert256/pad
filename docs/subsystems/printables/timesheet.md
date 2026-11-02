# printable:timesheet

Each item is `person | project | YYYY-MM-DD | duration`. `mode=decimal` uses nonnegative decimal hours (up to four fractional digits); `mode=clock` uses H:MM elapsed hours/minutes. Values are never converted into pay or work policy. `total=` is caller-supplied labelled text. `blankrows=0..100`. Repeated column headings/title/page numbers, wrapped cells, A4/Letter. Maximum 2000 rows. Shared HTML/PDF drawing plan retains physical geometry.

## Example

```pad
{printable:timesheet data='timeEntries', mode='clock', total='3:30', blankrows=3}{$person} | {$project} | {$date} | {$duration}{/printable:timesheet}
```

Run the focused fixture with `php tests/subsystems/printable-timesheet.php`.
