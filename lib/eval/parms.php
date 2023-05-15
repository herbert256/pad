<?php
 
  function padEvalParms ($type, $left, &$result, $myself, $start, $end=999999) {

    $kind = $result [$type] [2];
    $name = $result [$type] [0];

    $parm = [];

    foreach ( $result as $k => $v )
      if ($k <= $end and $k > $type and $k <= $result [$type] [3] - 1)
        if ( isset($v[4]) )
          foreach ($v[4] as $v2)
            $parm [] = $v2;
        else
          $parm [] = $v[0];

    if ( ! count($parm) )
      foreach ( $result as $k => $v )
        if ($k <= $end and  $k > $type ) {
          if ( isset($v[4]) ) {
            $parm = array_values($v[4]);
            unset($result[$k]);
          }
          break;
        }

    $count = count($parm);

    if ( $left >= $start and isset($result [$left] [4]) ) {
      $value = array_values($result [$left] [4]);
      unset($result [$left]);
    } elseif ( $left >= $start and $result [$left] [1] == 'VAL') {
      $value = $result [$left] [0];
      unset ($result [$left]);
    } else
      $value = $myself;
   
    $value = include pad . "eval/parms/$kind.php" ;
    
    $result [$type] [1] = 'VAL';
  
    if ( is_array($value) or is_object($value) or is_resource($value) ) {
      $result [$type] [0] = '*ARRAY*';
      $result [$type] [4] = padArraySingle ($value);
     } else {
      padCheckValue ($value);
      $result [$type] [0] = $value;
    }

    foreach ( $result as $key => $parm)
      if ( $key <= $end and $key > $type and $key <= $result [$type] [3] - 1 )
        unset($result[$key]);

    if ( $result [$type] [1] == 'VAL' ) {
      unset ( $result [$type] [2] );
      unset ( $result [$type] [3] );
    }

  }

?>