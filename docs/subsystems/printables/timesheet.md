# printable:timesheet

Each item is `person | project | YYYY-MM-DD | duration`. `mode=decimal` uses nonnegative decimal hours (up to four fractional digits); `mode=clock` uses H:MM elapsed hours/minutes. Values are never converted into pay or work policy. `total=` is caller-supplied labelled text. `blankrows=0..100`. Repeated column headings/title/page numbers, wrapped cells, A4/Letter. Maximum 2000 rows. Shared HTML/PDF drawing plan retains physical geometry.

## Example

The paired PHP page supplies the application data:

```php
<?php
$timeEntries=[['person'=>'Ada','project'=>'Design','date'=>'2026-11-02','duration'=>'1:30'],['person'=>'Ada','project'=>'Review','date'=>'2026-11-02','duration'=>'2:00']];
```

The PAD template renders it:

```pad
{printable:timesheet data='timeEntries', mode='clock', total='3:30', blankrows=3}{$person} | {$project} | {$date} | {$duration}{/printable:timesheet}
```

Run the focused fixture with `php tests/subsystems/printable-timesheet.php`.
