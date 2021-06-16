<?php

  function pad_seq_random ( $min, $max, $step, $multiple, $even, $odd ) {

    $rand = rand ( $min, $max ); 
    $rand = pad_seq_check_even_odd_down ( $check, $even, $odd );
    $rand = pad_seq_check_multiple_down ( $check, $multiple );

    return $rand;

  }

  function pad_seq_check_even_odd_up ( $check, $even, $odd ) {

    if ( $even and ($check % 2) )
      $check++;

    if ( $odd and ! ($check % 2) )
      $check++;

    return $check;

  }

  function pad_seq_check_even_odd_down ( $check, $even, $odd ) {


    if ( $check == PHP_INT_MAX)
      return $check;

    if ( $even and ($check % 2) )
      $check--;

    if ( $odd and ! ($check % 2) )
      $check--;

    return $check;

  }

  function pad_seq_check_multiple_up ( $check, $multiple ) { 
    
    if ( $multiple > 1 and ($check % $multiple) )
      $check = ceil ( $check / $multiple ) * $multiple;

    return $check;

  }

  function pad_seq_check_multiple_down ( $check, $multiple ) {

    if ( $check == PHP_INT_MAX)
      return $check;

    if ( $multiple > 1 and ($check % $multiple) )
      $check = floor ( $check / $multiple ) * $multiple;

    return $check;

  }

?>