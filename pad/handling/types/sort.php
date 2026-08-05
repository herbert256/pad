<?php

  // Handles the sort option: orders the tag's data set by one or more of its fields.
  //
  // The option value is a ';'-separated list of "field [ASC|DESC] [flag ...]" specs, e.g.
  // sort="dept ASC; name NATURAL DESC"; every word that is not a direction names a PHP
  // SORT_* flag. A bare sort, or an empty value, sorts on all fields of the first row.
  // Each spec adds a column of values plus its direction and flags to the argument list
  // of array_multisort(), which reorders $padData [$pad] by reference. Empty data sets
  // return at once.

  if ( ! count ($padData [$pad] ) )
    return;

  $padSortArgs   = [];
  $padSortFields = padExplode($padPrm [$pad] ['sort'], ';');

  if ( $padPrm [$pad] ['sort'] === TRUE or ! count ($padSortFields)) {
    $padSortFields = [];
    foreach ($padData [$pad] as $padV1) {
      foreach ($padV1 as $padK2 => $padV2)
        $padSortFields [] = $padK2;
      break;
    }
  }

  foreach ($padSortFields as $padK => $padV) {

    $padSortSort = '';
    $padSortFlags = 0;

    $padSortParms = padExplode($padV, ' ');

    foreach($padSortParms as $padK2 => $padV2) {
      if ($padK2==0)
        $padSortField = $padV2;
      elseif (strtolower($padV2) == 'asc')
        $padSortSort = 'ASC';
      elseif (strtolower($padV2) == 'desc')
        $padSortSort = 'DESC';
      else
        $padSortFlags = $padSortFlags | constant("SORT_" . strtoupper($padV2) );
    }

    $padSortArgs [] = array_column ($padData [$pad], $padSortField);

    if ($padSortSort)
      $padSortArgs [] = constant("SORT_$padSortSort");

    if ($padSortFlags)
      $padSortArgs [] = $padSortFlags;

  }

  $padSortArgs [] = &$padData [$pad];

  call_user_func_array ('array_multisort', $padSortArgs);

?>