<?php


  function padSeq_action ( $sequence1, $action, $sequence2 ) {

    $padSeqResult       = $sequence1;
    $padSeqCnt        = 0;
    $padSeqActionValue = $action;
    $padSeqActionName  = $action;
    
    $padSequenceStore [$action] = $sequence2;

    return include PAD . "sequence/actions/$action.php";  

  }


  function padSeq_random ( $min, $max ) {

    $rand = rand ( $min, $max ); 

    if ( $rand < $min )
      $rand = $min;

    if ( $rand > $max )
      $rand = $max;

    return $rand;

  }

  function padSeq_reverse ( $x ) {

   $rev = 0;
    while ($x > 0) {
      $rev = ($rev  * 10) + $x % 10;
      $x = (int)($x / 10);
    }
    return $rev;

  }

  function padSeq_set ( $name, $value ) {

    $GLOBALS ["padSeqParm"] = $value;
    $GLOBALS ["padSeq_$name"] = $value;

  }


  function padSeq_array_action ($action) {

    $array  = $GLOBALS ['padSeqActionValue'];
    $arrays = padExplode ($array, '|');

    $parms [] = $GLOBALS ['padSeqResult'];

    foreach ($arrays as $store)
      if ( $store !== TRUE )
        if ( isset($GLOBALS ['padSequenceStore'] [$store]) )
          $parms [] = $GLOBALS ['padSequenceStore'] [$store];
        else
          $parms [] = $store;

    return call_user_func_array ($action, $parms);

  }

  function padSeq_truncate ( $array, $side, $count ) {

    if ( $side == 'left' )
      return array_slice ($array, $count);
    else
      return array_slice ($array, 0, $count*-1);

  }

  function padSeq_getCnt ( $first, $second, $third ) {

    global $pad, $padPrmsTag, $padSeqParm;

    if ( isset($padPrmsTag [$pad][$first])      and $padPrmsTag [$pad][$first]  !== TRUE and is_numeric($padPrmsTag [$pad][$first]) )
 
      return $padPrmsTag [$pad][$first];
 
    elseif ( isset($padPrmsTag [$pad][$second]) and $padPrmsTag [$pad][$second] !== TRUE and is_numeric($padPrmsTag [$pad][$second]) )
 
      return $padPrmsTag [$pad][$second];
 
    elseif ( isset($padPrmsTag [$pad][$third])  and $padPrmsTag [$pad][$third]  !== TRUE and is_numeric($padPrmsTag [$pad][$third]) )
 
      return $padPrmsTag [$pad][$third];
 
    elseif (                                    $padSeqParm !== TRUE           and is_numeric($padSeqParm) )
 
      return $padSeqParm;
 
    else
 
      return 1;

  }

?>