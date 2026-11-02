# rule:required_if_any

`required_if_any:delivery,ship;plan,pro` requires a filled value when any semicolon-separated condition matches. Each condition is the existing field,value[,value...] contract; up to 20. Field references support nested bracket names. Missing/empty siblings never match empty values; values are trimmed and case sensitive. Lists match if any scalar item matches. Empty submitted lists use existing required conventions. Dependencies list every referenced field; message uses the normal localisation path.

## Example

```pad
{input:form 'required_if_any'}
{input:field type='text', 'address', label='Address', rules='required_if_any:delivery,ship;plan,pro'/}
<button type='submit'>Validate</button>
{/input:form}
```

Run the focused fixture with `php tests/subsystems/rule-required_if_any.php`.
