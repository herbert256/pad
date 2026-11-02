# table:masterdetail

Visible `columns=` comma list, stable `id=` field (default id), scalar `detail=` field (default detail). Optional `template=` is trusted application HTML with `[[field]]` slots; every substituted scalar is escaped. Native details controls provide keyboard disclosure and nested markup. `filter=` searches visible values, `sort=` orders parent records, `descending` reverses it. Filtering/sorting run before `page`/`pagesize` (25 default). Pass request page explicitly with application links/forms. Bounds: 10000 rows, 50 columns, pagesize <=1000.

## Example

```pad
{table:masterdetail data='detailRecords', columns='id,name', detail='description'/}
```

Run the focused fixture with `php tests/subsystems/table-masterdetail.php`.
