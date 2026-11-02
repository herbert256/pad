<?php

  // step - Piecewise-constant observations with numeric `x` (field, default x) and `value` (field, default value). Rows are sorted by x; duplicates and nonfinite x are rejected. Null/empty/missing values are explicit gaps. `align` is before/after/mid (after); `gaps` is break/connect (break). At most 2,000 rows. The accessible description names alignment, gap policy and observations.
  //
  // PHP and `client` browser SVGs share independent fixtures for every alignment/gap combination, equal ranges, duplicates and unsorted input.

try{return padChartStep(padChartGather(),...padChartSize(600,320));}catch(InvalidArgumentException $e){if($padCheckSyntax)padError($e->getMessage());return '';}
