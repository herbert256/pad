<?php

  function pad_eval_php ($function, $left, &$result, $myself, $start) {

    $parm = [];

    if ( $left >= $start and $result [$left] [1] == 'VAL') {
      $parm [] = $result [$left] [0];
      unset ($result [$left]);
    } elseif ( $myself !== null )
      $parm [] = $myself;

    foreach ( $result as $k => $v )
      if ($k > $function and $k <= $result [$function] [5] - 1)
        $parm [] = $v[0];

    if ( ! count($parm ))
      foreach ( $result as $k => $v )
        if ($k > $function ) {
          if ( isset($v[6]) and $v[6] == 'array' ) {
            $parm = array_values($v[7]);
            unset($result[$k]);
          }
          break;
        }

    pad_timing_start ('app');

    $result = call_user_func_array ( $result [$function] [2] , $parm [] );

    pad_timing_end ('app');
  
    $result [$function] [0] = $result;
    $result [$function] [1] = 'VAL';

    foreach ( $result as $key => $parm)
      if ($key > $function and $key <= $result [$function] [5] - 1)
        unset($result[$key]);

  }

?> 