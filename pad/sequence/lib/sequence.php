<?php
  
  function pad_seq_random ( $min, $max ) {

    $rand = rand ( $min, $max ); 

    if ( $rand < $min )
      $rand = $min;

    if ( $rand > $max )
      $rand = $max;

    return $rand;

  }

  function pad_seq_reverse ( $x ) {

   $rev = 0;
    while ($x > 0) {
      $rev = ($rev  * 10) + $x % 10;
      $x = (int)($x / 10);
    }
    return $rev;

  }

  function pad_seq_set ( $name, $value ) {

    $GLOBALS ["pad_seq_parm"] = $value;
    $GLOBALS ["pad_seq_$name"] = $value;

  }


  function pad_seq_array_action ($action, $array='') {

    $arrays = pad_explode ($array, '|');

    $parms [] = $GLOBALS ['pad_seq_result'];
    foreach ($arrays as $store)
      $parms [] = $GLOBALS['pad_seq_store'] [$store];

    return call_user_func_array ($action, $parms);

  }

?>