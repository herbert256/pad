<?php
 
  function pad_eval_type ($type, $left, &$result, $myself, $start) {

    global $pad_eval_cnt;

    $parm = [];
    foreach ( $result as $k => $v )
      if ($k > $type and $k <= $result [$type] [5] - 1)
        $parm [] = $v[0];

    if ( ! count($parm ))
      foreach ( $result as $k => $v )
        if ($k > $type ) {
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
  
    $kind  = $result [$type] [2];
    $name  = $result [$type] [3];
    $count = count($parm);
    
    $value = include PAD_HOME . "eval/$kind.php" ;
      
    if ( is_array($value) or is_object($value) or is_resource($value) ) {
      $result [$type] [6] = 'array';
      $result [$type] [7] = pad_array_single ($value);
      $value = '*ARRAY*';
    } else {
      pad_check_value ($value);
      $result [$type] [0] = $value;
      $result [$type] [1] = 'VAL';
    }

    foreach ( $result as $key => $parm)
      if ($key > $type and $key <= $result [$type] [5] - 1)
        unset($result[$key]);

  }

  function pad_array_single ($value) {

    $value = pad_xxx_to_array ($$value);

    $array = [];

    foreach ( $value as $v1 )
      if ( ! is_array($v1) )
        $array [] = $v1;
      else 
        foreach ( $v1 as $v2 ) {
          if ( ! is_array($v2) )
             $array [] = $v2;
          else 
            foreach ( $v2 as $v3 ) {
              if ( ! is_array($v3) )
                $array [] = $v3;
              break;
            }
          break;
        }

    return $array;

  }

?>