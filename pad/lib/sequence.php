<?php


  function pSeq_action ( $sequence1, $action, $sequence2 ) {

    $padSeq_result       = $sequence1;
    $padSeqCnt        = 0;
    $padSeq_action_value = $action;
    $padSeq_action_name  = $action;
    
    $padSequenceStore [$action] = $sequence2;

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

    $array  = $GLOBALS ['padSeq_action_value'];
    $arrays = pExplode ($array, '|');

    $padarms [] = $GLOBALS ['padSeq_result'];

    foreach ($arrays as $store)
      if ( $store !== TRUE )
        if ( isset($GLOBALS ['padSequenceStore'] [$store]) )
          $padarms [] = $GLOBALS ['padSequenceStore'] [$store];
        else
          $padarms [] = $store;

    return call_user_func_array ($action, $padarms);

  }

  function pSeq_truncate ( $array, $side, $count ) {

    if ( $side == 'left' )
      return array_slice ($array, $count);
    else
      return array_slice ($array, 0, $count*-1);

  }

  function pSeq_getCnt ( $first, $second, $third ) {

    global $pad, $padPrmsTag, $padSeq_parm;

    if ( isset($padPrmsTag [$pad][$first])      and $padPrmsTag [$pad][$first]  !== TRUE and is_numeric($padPrmsTag [$pad][$first]) )
 
      return $padPrmsTag [$pad][$first];
 
    elseif ( isset($padPrmsTag [$pad][$second]) and $padPrmsTag [$pad][$second] !== TRUE and is_numeric($padPrmsTag [$pad][$second]) )
 
      return $padPrmsTag [$pad][$second];
 
    elseif ( isset($padPrmsTag [$pad][$third])  and $padPrmsTag [$pad][$third]  !== TRUE and is_numeric($padPrmsTag [$pad][$third]) )
 
      return $padPrmsTag [$pad][$third];
 
    elseif (                                    $padSeq_parm !== TRUE           and is_numeric($padSeq_parm) )
 
      return $padSeq_parm;
 
    else
 
      return 1;

  }

?>