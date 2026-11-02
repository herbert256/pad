# section:resources

Curated resource directory, grouped by consecutive category labels in caller order. Sort input by category to consolidate groups. Each entry has name, link, text, category. Uses existing section:item or data rows. All descriptions are scalar escaped text; links use the section URL policy. Explicit empty state; max 1000 entries. Native links need no script.

## Example

```pad
{section:resources title='Documentation'}{section:item 'Getting started', link='/tags/', category='Guides'}A practical introduction.{/section:item}{/section:resources}
```

Run the focused fixture with `php tests/subsystems/section-resources.php`.
