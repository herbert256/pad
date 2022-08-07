<?php


  function pSeq_action ( $sequence1, $action, $sequence2 ) {

    $pSeq_result       = $sequence1;
    $pSeq_count        = 0;
    $pSeq_action_value = $action;
    $pSeq_action_name  = $action;
    
    $pSequence_store [$action] = $sequence2;

    return include PAD . "sequence/actions/$action.php";  

  }


  function pSeq_random ( $min, $max ) {

    $rand = rand ( $min, $max ); 

    if ( $rand < $min )
      $rand = $min;

    if ( $rand > $max )
      $rand = $max;

    return $rand;

  }

  function pSeq_reverse ( $x ) {

   $rev = 0;
    while ($x > 0) {
      $rev = ($rev  * 10) + $x % 10;
      $x = (int)($x / 10);
    }
    return $rev;

  }

  function pSeq_set ( $name, $value ) {

    $GLOBALS ["pSeq_parm"] = $value;
    $GLOBALS ["pSeq_$name"] = $value;

  }


  function pSeq_array_action ($action) {

    $array  = $GLOBALS['pSeq_action_value'];
    $arrays = pExplode ($array, '|');

    $parms [] = $GLOBALS ['pSeq_result'];

    foreach ($arrays as $store)
      if ( $store !== TRUE )
        if ( isset($GLOBALS['pSequence_store'] [$store]) )
          $parms [] = $GLOBALS['pSequence_store'] [$store];
        else
          $parms [] = $store;

    return call_user_func_array ($action, $parms);

  }

  function pSeq_truncate ( $array, $side, $count ) {

    if ( $side == 'left' )
      return array_slice ($array, $count);
    else
      return array_slice ($array, 0, $count*-1);

  }

  function pSeq_get_count ( $first, $second, $third ) {

    global $pPrmsTag[$p], $pSeq_parm;

    if ( isset($pPrmsTag[$p][$first])      and $pPrmsTag[$p][$first]  !== TRUE and is_numeric($pPrmsTag[$p][$first]) )
 
      return $pPrmsTag[$p][$first];
 
    elseif ( isset($pPrmsTag[$p][$second]) and $pPrmsTag[$p][$second] !== TRUE and is_numeric($pPrmsTag[$p][$second]) )
 
      return $pPrmsTag[$p][$second];
 
    elseif ( isset($pPrmsTag[$p][$third])  and $pPrmsTag[$p][$third]  !== TRUE and is_numeric($pPrmsTag[$p][$third]) )
 
      return $pPrmsTag[$p][$third];
 
    elseif (                                    $pSeq_parm !== TRUE           and is_numeric($pSeq_parm) )
 
      return $pSeq_parm;
 
    else
 
      return 1;

  }

?>