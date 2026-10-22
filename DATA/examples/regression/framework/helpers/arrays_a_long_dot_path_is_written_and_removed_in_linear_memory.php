<?php

  // Every level of the walk reads its segment from the one path they share: each level
  // shifted its own copy of the rest of the path and kept it until the walk came back,
  // memory quadratic in the segments - 276 MB for a key of 4000 segments, 8 KB, which a
  // JSON body handed to padArrUndot could bring.

  $key = 'a' . str_repeat ( '.a', 3999 );

  memory_reset_peak_usage ();
  $base = memory_get_usage ();

  $nested = padArrUndot ( [ $key => 1 ] );
  $set    = [];
  padArrSet ( $set, $key, 2 );
  $after  = padArrGet ( $set, $key );
  $gone   = padArrForget ( $set, $key );

  $used = memory_get_peak_usage () - $base;

  $r = json_encode ( [
    'undot'  => padArrGet ( $nested, $key ),
    'set'    => $after,
    'forget' => padArrHas ( $gone, $key ),
    'linear' => $used < 16 * 1024 * 1024
  ] );

?>
