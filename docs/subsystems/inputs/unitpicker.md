# inputs: unitpicker

`{input:unitpicker 'distance', family='length', value=100, unit='cm', label='Distance'/}`
submits `distance[value]` and `distance[unit]` as a nested array. A native number field
and select work without scripting. `family` names a units family; choices come from
`padUnits($family)`. Changing the unit deliberately preserves the entered value.
Convert explicitly with `padConvert($posted['distance']['value'], $posted['distance']['unit'], 'm')`
after validation. Canonical symbols are case-sensitive. Nested fields refill from
PAD form source. Validate `distance[value]` and `distance[unit]` separately; an invalid
posted unit remains visible so server errors do not silently change the submission.
