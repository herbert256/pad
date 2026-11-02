# jsonld:imageobject

Maps explicit fields to [Schema.org ImageObject](https://schema.org/ImageObject), checked against primary vocabulary on 2026-11-02. URL and date validation and script-safe JSON use the shared JSON-LD engine. Unlisted fields are omitted. `url` is contentUrl. Width and height are nonnegative pixel counts emitted as QuantitativeValue. Required: name, url. No search-engine eligibility claim.

## Example

```pad
{jsonld:imageobject name='Canal', url='https://example.org/canal.jpg', width=1200, height=800/}
```

Run the focused fixture with `php tests/subsystems/jsonld-imageobject.php`.
