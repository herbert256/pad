<?php
  
  function pad_eval_php ($php, $left, &$result, $myself, $start) {

    $parm = [];
    foreach ( $result as $k => $v )
      if ($k > $php and $k <= $result [$php] [5] - 1)
        $parm [] = $v[0];

    if ( ! count($parm ))
      foreach ( $result as $k => $v )
        if ($k > $php ) {
          if ( isset($v[6]) and $v[6] == 'array' ) {
            $parm = array_values($v[7]);
            unset($result[$k]);
          }
          break;
        }

    if ( $left >= $start and $result [$left] [1] == 'VAL') {
      $value = $result [$left] [0];
      unset ($result [$left]);
    } else
      $value = $myself;
  
    $name  = $result [$php] [3];
    $count = count($parm);

    pad_trace ("eval/php/s", "name=" . $name);
       
    $value = include $result [$php] [2] ;
      
    pad_check_value ($value);

    pad_trace ("eval/php/e", "result=" . $value);

    $result [$php] [0] = $value;
    $result [$php] [1] = 'VAL';

    foreach ( $result as $key => $parm)
      if ($key > $php and $key <= $result [$php] [5] - 1)
        unset($result[$key]);

  }

?>