# Packing lists and attendance

Both use the common printable paper, preview, print and PDF backends. `paper` is `a4` or `letter`, `title` is at most 55 characters, and `copies` duplicates complete documents. Each rendered template instance is one logical row. Text wraps before pagination; a row stays together. Headers and page numbers repeat. A row exceeding one page or the 2,000-character name limit is an error.

```pad
{printable:packinglist data='items', paper='a4', title='Trip equipment'}
  {$name}<aside>{$quantity}</aside>
{/printable:packinglist}
{printable:attendance data='people', dates='2026-11-02,2026-11-03,2026-11-04', paper='letter'}
  {$name}
{/printable:attendance}
```

Packing quantities are plain text up to 24 characters. The final column is a printed checkbox. Attendance takes one to seven unique ISO dates (default: today's UTC date), shows the complete dates above the register and month/day in the column headers. Empty cells have at least 10 mm of writing height. HTML inside row templates is reduced to plain text; quantities come from `aside`. Long unbroken names wrap, rather than extending into another cell. The PDF writer's existing font and script limitations apply.
