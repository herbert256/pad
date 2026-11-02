# Tables

`table:standard`, `table:grouped`, `table:pivot` and `table:matrix` share the table renderer. The old PAD datatable tag has been removed. Existing applications use `{table:standard ...}` with the same options and CSS classes.

Rows come from `data=` (an array, store or data file) or the pair body (CSV, JSON, YAML or XML). Every kind escapes labels and values and supports `caption`, `sortable`, `rows` (page size), `query` (request prefix), `striped`, `totals` and column `format` pipes. The standard and grouped kinds accept `columns` and `labels`. Sorting and paging work without JavaScript; totals cover all filtered rows.

- **standard**: the original formatted table. `filters='region, customer'` adds search inputs; `?filter[region]=north` narrows a column by a literal, case-insensitive substring. With `query='sales_'` the request uses `sales_filter[region]`. Unknown filter columns in the request and nonscalar filter values are ignored. Filtering precedes aggregation, sorting and pagination; an empty selection retains the form.
- **grouped**: `group='region'` or `group='region, team'` collects each distinct combination under a header. `totals='amount'` adds a subtotal per group and a grand total. Sorting acts within groups. Pagination counts data rows; a group that spans pages repeats its header and full-group subtotal on each page.
- **pivot**: `row='region', column='quarter', value='amount', aggregate='sum'` makes one row per region and a column per quarter. `aggregate` is `sum` (default), `count`, `avg`, `min` or `max`. Count counts records and needs no `value`; other aggregates skip null/empty values and reject nonnumbers. Missing intersections are zero for sum/count and empty for avg/min/max.
- **matrix**: the same aggregation, defaulting to `count`, with five levels of cell shading over the full result's numeric range. Values remain visible and accessible without color. Use `aggregate='sum'` and `value` for a numeric heat table.

Pivot/matrix output fields are `row_label`, `value_0`, `value_1`, ... in first-seen category order; `totals`, `format` and request sorting name these fields. Category labels are preserved exactly, including commas. Filters name original input fields. The legacy `padDatatable*` PHP helpers remain available in `_lib/table.php`; `_lib/reshape.php` supplies filtering, grouping and aggregation helpers.

Runnable examples for every kind are in `apps/tags/examples/table/` and the tags catalog. Kinds are registered by files in `types/`.
