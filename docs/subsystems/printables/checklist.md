# printable:checklist

Each rendered item is a label; optional `<aside>` names its group. Labels wrap before pagination, group headings repeat on continuation pages, and title/page number repeat. `blankrows=0..100` adds writing lines. Checkboxes are exactly 4×4 mm. A4/Letter; max 2000 items and 2000 characters per label. Uses shared HTML/PDF drawing plan, so physical geometry is the same.

## Example

```pad
{printable:checklist data='checklistItems', title='Departure checks', blankrows=3}<aside>{$group}</aside>{$name}{/printable:checklist}
```

Run the focused fixture with `php tests/subsystems/printable-checklist.php`.
