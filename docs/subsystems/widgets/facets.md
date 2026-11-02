# widget:facets

Consumes the latest `padSearchInfo()` after a search with facets enabled. `selected=` is the exact typed filter map; selected values absent from counts remain visible at zero. Native checkboxes submit JSON scalar tokens as `facet[field][]`, preserving strings, numbers, booleans and null. Decode with `padFacetDecode($_GET["facet"] ?? [])`, then pass to search filters; no second index. Put inside an application GET form with query and submit controls. Source authorization belongs to the application. Max 1000 buckets / 50 posted fields.

## Example

```pad
{widget:facets/}
```

Run the focused fixture with `php tests/subsystems/widget-facets.php`.

selected accepts a literal typed map or its quoted application-array name.
