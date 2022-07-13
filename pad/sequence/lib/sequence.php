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


  function pad_seq_array_action ($action) {

    $array  = $GLOBALS['pad_seq_action_value'];
    $arrays = pad_explode ($array, '|');

    $parms [] = $GLOBALS ['pad_seq_result'];

    foreach ($arrays as $store)
      if ( $store !== TRUE )
        if ( isset($GLOBALS['pad_seq_store'] [$store]) )
          $parms [] = $GLOBALS['pad_seq_store'] [$store];
        else
          $parms [] = $store;

    return call_user_func_array ($action, $parms);

  }

  function pad_seq_truncate ( $array, $side, $count ) {

    if ( $side == 'left' )
      return array_slice ($array, $count);
    else
      return array_slice ($array, 0, $count*-1);

  }

  function pad_seq_get_count ( $first, $second, $third ) {

    global $pad_parms_tag, $pad_seq_parm;

    if ( isset($pad_parms_tag[$first])      and $pad_parms_tag[$first]  !== TRUE and is_numeric($pad_parms_tag[$first]) )
 
      return $pad_parms_tag[$first];
 
    elseif ( isset($pad_parms_tag[$second]) and $pad_parms_tag[$second] !== TRUE and is_numeric($pad_parms_tag[$second]) )
 
      return $pad_parms_tag[$second];
 
    elseif ( isset($pad_parms_tag[$third])  and $pad_parms_tag[$third]  !== TRUE and is_numeric($pad_parms_tag[$third]) )
 
      return $pad_parms_tag[$third];
 
    elseif (                                    $pad_seq_parm !== TRUE           and is_numeric($pad_seq_parm) )
 
      return $pad_seq_parm;
 
    else
 
      return 1;

  }

?>