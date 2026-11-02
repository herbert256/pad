# timeline:rangecompare

Each record has unique stable id, label and paired plannedstart/plannedend and actualstart/actualend. Either complete pair can be absent and is labelled missing. Strict explicit-offset instants share one axis; equal instants are visible as 1px markers. Maximum 500 rows. A complete textual list accompanies the SVG and gives exact dates in timezone. Caller order is preserved. Does not infer progress.

## Example

```pad
{timeline:rangecompare data='rangeRecords', timezone='UTC'/}
```

Run the focused fixture with `php tests/subsystems/timeline-rangecompare.php`.
