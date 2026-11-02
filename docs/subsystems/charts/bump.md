# bump

`{chart:bump label='team', value='spring,summer,autumn'}...{/chart:bump}` accepts one row per entity and a numeric column per ordered period. With no value option all inferred numeric columns are periods. Each period is ranked independently, largest value first; ties use competition ranks (1, 1, 3). Values may be negative. Missing observations have no rank and break the path; they are never treated as zero or bridged.

The vertical axis shows ranks, first at the top. The legend identifies entities, point tooltips and the description give both rank and original value. Exact ties overlap intentionally. Colors repeat every eight entities. Standard chart title, dimensions, palette and client options apply.

The parity regression checks tied, negative and missing values against the PHP and JS renderers.
