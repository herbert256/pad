<?php

  // forest - Labelled horizontal supplied intervals. `label`, `value`, `low`, `high` select fields (defaults of the same names). Every bound must be finite and low <= estimate <= high. `interval` names the supplied interval; no inference is made. `scale` linear/log; log requires positive estimates, bounds and `reference` (default 1 for log, 0 linear). The reference line and endpoint ticks are explicit. Up to 60 rows; canvas height expands to keep labels legible.
  //
  // Shared SVG shell provides escaped titles/descriptions and theme colours. PHP/browser normalized-SVG parity covers valid and invalid data, with fixtures in `tests/subsystems/charts-forest.json`.

try{return padChartForest(padChartGather(),...padChartSize(600,320));}catch(InvalidArgumentException $e){if($padCheckSyntax)padError($e->getMessage());return '';}
