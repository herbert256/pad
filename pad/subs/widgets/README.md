
## Indicators and data display

The former PAD progress, rating, placeholder and JSON viewer tags are widget kinds:
`widget:progress`, `widget:rating`, `widget:placeholder` and `widget:jsonview`. Their
options and output are preserved; their helpers now live in this subsystem.

- `widget:progressring` displays the same finite value/max as a circle; size is small,
  medium or large, and color uses the progress color slots. A rounded 100% is reserved
  for a completed value.
- `widget:ratingdistribution` takes rows with `score` (or `score='field'`) and one vote
  per row; `count='field'` supplies nonnegative integer vote counts. `max` is 1–20,
  default 5. It shows weighted average, vote totals and accessible meters.
- `widget:skeleton` draws text, card, avatar or table shapes via `shape=`, with `lines`
  (1–20), `columns` (1–12), and an accessible `label`. Optional `animate` respects reduced motion.

Every kind has a runnable example in the tags application.
