# rule:prohibited_if

`prohibited_if:method,pickup` requires the target to be empty when a sibling matches any listed value. Uses existing required_if field parsing and trimmed, case-sensitive scalar/list comparisons. Missing sibling does not match. Empty strings and lists containing only blanks are allowed; "0" is filled. Cross-field browser dependencies and localisable messages use the normal validator contract.

## Example

```pad
{input:field type='text', 'address', label='Address', rules='prohibited_if:method,pickup'/}
```

Run the focused fixture with `php tests/subsystems/rule-prohibited_if.php`.
