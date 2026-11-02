# section:contents

Curated chapter index in an ordered navigation list. It does not scan document headings; use widget:toc for that. Each entry has name, link, text. Uses existing section:item or data rows. All descriptions are scalar escaped text; links use the section URL policy. Explicit empty state; max 1000 entries. Native links need no script.

## Example

```pad
{section:contents title='Documentation'}{section:item 'Getting started', link='/tags/', category='Guides'}A practical introduction.{/section:item}{/section:contents}
```

Run the focused fixture with `php tests/subsystems/section-contents.php`.
