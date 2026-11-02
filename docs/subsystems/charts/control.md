# control

Process observations with explicitly supplied center/lower/upper fields. `x`, `value`, `center`, `lower`, `upper` name fields. Numeric x is sorted, duplicates rejected. Missing observations break the measured line; each missing limit breaks that limit line. Limits hold after each observation until the next, so changing limits are visible. Lower must not exceed upper and a supplied center must lie inside known limits. Out-of-limit points carry a text violation label; no statistical limits or rules beyond supplied-limit comparisons are estimated. Up to 2,000 rows.

Shared SVG shell provides escaped titles/descriptions and theme colours. PHP/browser normalized-SVG parity covers valid and invalid data, with fixtures in `tests/subsystems/charts-control.json`.
