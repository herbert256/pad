# widget:inspector

`record=` is an array; `fields=` explicitly maps property names to labels or metadata `{label,unit,action,actionlabel}`. Maximum 100 fields. Absent, null and empty values remain distinct; object properties are never introspected. Nested arrays use existing jsonview disclosures. Scalar values escape; actions accept http(s), local-root, query and fragment URLs. No editing is inferred.

## Example

```pad
{widget:inspector record='record', fields='fields'/}
```

Run the focused fixture with `php tests/subsystems/widget-inspector.php`.

Array-valued options accept a literal array or the quoted name of an application array/store.
