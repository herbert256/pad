# table:ranked

Ranks finite score values with `method=dense|competition|ordinal`, descending by default. Equal scores use stable ID lexical order. Optional previous rank yields signed change (positive means up), absent means New. `filter=` on label field runs before ranking; pagination runs after ranking. Defaults id=id,label=name,score=score,previous=previous. Bounds 10000 rows and pagesize <=1000.

## Example

```pad
{table:ranked data='rankingRecords', method='competition'/}
```

Run the focused fixture with `php tests/subsystems/table-ranked.php`.
