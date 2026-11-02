<?php

  // ewma - `padTimeSeries(kind, rows, options=[])` returns `rows`, `method`, `parameters`
  // (including defaults), time mode/timezone and input, missing and duplicate counts.
  // All four kinds return data, not markup. Every output row has `time`, `value`,
  // `series` and `count`. Count is finite observations in a bucket/window, one for an
  // EWMA update, or two endpoints for a defined difference. Unknown options fail.
  //
  // Inputs are at most 100,000 records. `time='time'` and `value='value'` select fields;
  // optional `series` selects a string/integer identity field (typed IDs stay distinct).
  // Without it all rows belong to `''`. Times are homogeneous finite epoch seconds or
  // ISO instants with explicit offsets, restricted to years 0000..9999. Instants use
  // whole seconds, output in `timezone='UTC'` by default. Series retain first-seen order;
  // observations sort by time. Values must be finite native numbers or missing/null.
  //
  // `duplicates='error'|'last'|'mean'` defaults to error; last follows input order and
  // mean averages available values at the same time. `missing='keep'|'error'` defaults
  // to keep. No operation silently fills missing values with zero.
  //
  // * `resample`: `stepSeconds=3600`, `origin=0`, `aggregate='mean'` (also sum/min/max).
  //   Fixed-duration half-open buckets, from the first occupied bucket through the
  //   last; empty buckets have null value/count zero, even for sum. Origin and step
  //   must be whole seconds for ISO input. These are elapsed-time buckets, not local
  //   calendar periods. Bounds must be representable; at most 100,000 output buckets.
  //   Fractional numeric boundaries snap to the nearest grid index within four machine
  //   epsilons scaled by `max(1, abs((time-origin)/step), abs(time/step), abs(origin/step))`.
  //   Thus `0.3` at a `0.1` step is on the third boundary. Uncertainty of a quarter
  //   bucket or more fails explicitly. The returned parameters name this boundary policy.
  // * `rolling`: trailing sample-count `window=5`, `minObservations=window`, same
  //   aggregates. Missing samples occupy a window position; a missing current sample
  //   remains null even when other observations suffice. Work is capped at ten million
  //   input-row/window combinations.
  // * `ewma`: `alpha=.3` in (0,1], initial value is the first observation.
  //   `gap='reset'` restarts at the next observed value; `carry` retains state across
  //   gaps, but the missing row still outputs null. This is observation-weighted, not
  //   elapsed-time-adjusted smoothing.
  // * `difference`: `lag=1` in 1..10000 compares sorted observations, not time buckets.
  //   `rate=true` divides by the actual time difference in seconds. Missing endpoints
  //   yield null, and arithmetic overflow is an explicit error.
  //
  // The pure helper is suitable for `table:standard` and `chart:line` after selecting
  // the returned rows. Values are not forecasting or causal claims. Tests use
  // hand-computed irregular series, typed groups, duplicates, gaps and DST folds.

  return ['call'=>fn($rows,$options)=>padSeriesCompute('ewma',$rows,$options)];
?>
