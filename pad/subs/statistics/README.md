# Statistics

`padStats(kind, rows, options = [])` returns analysis data, never markup. Kinds:
`describe`, `quantiles`, `histogram`, `regression`, `outliers`. Invalid arguments use
`padError` and return null if the error handler continues.

Inputs are lists of at most 100000 native finite PHP numbers. Numeric strings and
booleans are rejected. `field` selects a record field. Null and absent fields are
missing: `missing='omit'` (default) drops them; `missing='error'` rejects them. Every
result includes `count`, `missing`, `missingPolicy` and `method`. Regression takes
records with `x` and `y` fields (selectable with options); missing pairs are omitted
as pairs, and invalid nonmissing values still fail.

- `describe`: sum, mean, min/max, median, q1/q3, sample variance (`n-1`) and sd.
  Empty inputs have sum zero and null moments; a singleton has null variance/sd.
- `quantiles`: `probabilities=[0,.25,.5,.75,1]`, at most 1000 probabilities.
  Hyndman-Fan type 7 linear interpolation, also used by PAD chart quartiles.
- `histogram`: `bins=10` (1..1000), or 2..1001 strictly increasing `edges`.
  Bins include their lower bound; only the final bin includes its upper bound.
  Explicit edges return separate underflow/overflow counts. Constant data returns
  one point-mass bin. Indistinguishable generated edges fail; reduce bin count.
- `regression`: ordinary least squares with intercept; `status='degenerate'` for
  fewer than two pairs or constant x. Constant y has slope zero and null r/rSquared.
- `outliers`: Tukey fences, `factor=1.5` in [0,100], type-7 quartiles. Returns the
  original observation indices and values strictly outside the fences.

Compensated summation and shifted centering preserve small spreads on large
origins. Overflow is an explicit error, never infinity or a silently clipped answer.
No automatic interpretation, causal inference, sampling or spreadsheet coercion.

Example: `padStats('histogram', [1,2,2,3,5], ['edges'=>[0,2,4,6]])`.
The runnable `/showcase/?analysis/statistics` page feeds the same histogram result
to `table:standard` and `chart:bar`.
