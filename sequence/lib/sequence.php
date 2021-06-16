<?php

  function pad_seq_random ( $min, $max, $increment, $even, $odd ) {

    $rand = rand ( $min, $max ); 
    
    $rand = pad_seq_check ( $rand, $increment, $even, $odd );

    return $rand;

  }

  function pad_seq_check ( $check, $increment, $even, $odd ) {

    if ( $increment <> 1 and $check % $increment ) {

      $check = round ( $check / $increment ) * $increment;

      if ( $check > $max )
        $check = $check - $increment;

      if ( $check < $min )
        $check = $check + $increment;

    }

    if ( $even and $check % 2 )
      $check++;

    if ( $odd and ! $check % 2 )
      $check++;

    if ( ( $even or $odd ) and $check > $max)
      $check = $check - 2;

    return $check;

  }

?>