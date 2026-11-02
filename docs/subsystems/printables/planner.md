# printables: planner

`{printable:planner date='2026-11-02', weekstart='monday', from=8, to=18, slot='30', weeks=2}Tasks: ...{/printable:planner}`
creates one physical page per week. A4 (210 × 297 mm) or Letter; landscape supported
by the common paper helper. Date is an exact UTC civil date, aligned to Monday or
Sunday; working hours are explicit 0–24 and slots 30/60 minutes. `holidays=$labels`
accepts a YYYY-MM-DD-to-label map supplied by the application. Body content reserves
30 mm for tasks; otherwise the grid fills that space. Geometry is shared by browser
print and PAD PDF. Text exceeding its physical cell is clipped like other printables;
preview long holiday names and tasks before printing.
