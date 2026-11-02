<?php

  // intersection - `padIntervals(kind, left, right = [], options = [])` returns a list of coverage
  // records. Kinds are `union`, `intersection`, `subtract` and `overlaps`; this is a
  // data helper, not a template renderer. Each operand has at most 10,000 records
  // containing `start`, `end`, optional `id`, and an optional grouping field (default
  // `resource`). Bounds are homogeneous finite native numbers or explicit ISO instants
  // with offsets and whole seconds. Half-open `[start,end)` intervals may be empty;
  // reversed bounds fail. Numeric and instant modes cannot be mixed.
  //
  // Union merges adjacent ranges; intersection and subtraction produce disjoint
  // coverage. Overlaps instead returns one record for each overlapping left/right
  // pair, with original `index` and `id` on each side. Touching intervals never overlap.
  // Set operations intentionally discard source IDs because several sources may
  // contribute to one range. Group IDs preserve string/integer identity and first-seen
  // group order. Within groups bounds sort ascending. Missing group defaults to `''`.
  //
  // Options: `group` selects the group field (reserved names `start`, `end`, `id`,
  // `left` and `right` are rejected), `timezone='UTC'` formats instant output,
  // and `maxResults=10000` (1..100000) bounds output. Pairwise work is capped at ten
  // million comparisons; exceeding a limit fails without partial results. No state,
  // reservation or network action occurs. Invalid input calls `padError`.
  //
  // Calendar availability retains its signature, input limits and timezone handling;
  // it delegates coverage union/subtraction to this core. Validate with
  // `php tests/subsystems/expansion-intervals.php` and the calendar availability fixtures.

  return [ 'call' => fn ( $a, $b, $options ) => padIntervalCompute ( 'intersection', $a, $b, $options ) ];
?>
