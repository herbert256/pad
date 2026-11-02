# Bounded constraint solvers

`padSolve(kind, problem, options = [])` returns `status`, `solution`, `steps` and
`maxSteps`, plus kind-specific metadata. Status is `solved`, `infeasible`, `limit`
or `cancelled`. A work limit never claims infeasibility. `maxSteps=100000` accepts
1..10000000; optional `cancel` is a callable checked at work boundaries. Limit and
cancelled results contain no partial solution. Validation errors call `padError`.
Unknown problem fields/options fail. Identifier lists contain unique nonempty
strings of at most 256 bytes. All ties follow input order.

- `exactcover`: `{columns: [...], rows: {rowId: [columnId,...], ...}}`. All columns
  must be covered exactly once. At most 256 columns, 4096 nonempty rows and 65536
  incidences. Algorithm X chooses the fewest-candidate column and returns one list
  of selected row IDs. Steps count search calls and row/incidence inspections.
- `matching`: `{left:[...], right:[...], edges:[[leftId,rightId],...]}`. Maximum
  bipartite cardinality, not weighted. Each side has at most 500 IDs and edges at
  most 20000; duplicates fail. Returns pairs, cardinality and unmatchedLeft.
  `requirePerfect=true` means every left node must be matched; an infeasible result
  still includes the proven maximum matching. Steps count augmentations/edge scans.
- `assignment`: `{rows:[...], columns:[...], costs:[[...],...]}`. Minimum-cost
  injection: every row must get a distinct column; excess columns may be unused.
  At most 128 rows/columns. Rectangular Hungarian algorithm; null forbids an edge,
  costs are finite native numbers, negative costs allowed. The objective is solved
  in exact native integers: floating costs must round-trip unchanged through one
  common decimal scale of at most 9 places. For example, 0.1 and 0.25 use scale 100;
  1/3 and 0.1+0.2 are rejected rather than rounded to another optimization problem.
  Every scaled magnitude must be at most
  `intdiv(PHP_INT_MAX, 8*(rowCount+columnCount+1))`; larger ranges/spreads fail.
  These bounds include intermediate arithmetic and the sum. Solved results expose
  integer `scaledCost` and `costScale` (1..1000000000, powers of ten): their ratio
  is the exact decimal objective. `cost` is an integer for scale 1, otherwise its
  convenient floating representation; original per-edge costs are retained.
  More rows than columns is infeasible. Steps count candidate column inspections.

This is deterministic bounded combinatorial computation, not workflow execution or
resource/calendar scheduling. `/showcase/?analysis/cover-puzzle` tiles a tiny board;
`/showcase/?analysis/allocation` assigns workers to tasks and also computes a
capability-only maximum matching. Tests enumerate small instances independently.
