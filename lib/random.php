<?php


  function pad_random_one ( $min, $max, $step, $multi, $floor, $ceil, $prime, $power ) {

    if ( $step )
      return pad_random_one_step  ( $min, $max, $step, $floor, $ceil );

    if ( $multi )
      return pad_random_one_multi ( $min, $max, $multi, $floor, $ceil );

    if ( $prime )
      return pad_random_one_prime ( $min, $max );

    if ( $power )
      return pad_random_one_power ( $min, $max, $power );

    return rand ( $min, $max);

  }


  function pad_random_one_step ( $min, $max, $step, $floor, $ceil ) {

    $rand = rand (0, $max-$min);

    if     ( $floor ) $steps = floor ( $rand / $step );
    elseif ( $ceil  ) $steps = ceil  ( $rand / $step );
    else              $steps = round ( $rand / $step );

    $rand = $min + ( $steps * $step );

    if ( $rand > $max ) {
      $steps = floor ( ($max - $min) / $step);
      $rand  = $min + ( $steps * $step );
    }

    return $rand;

  }


  function pad_random_one_multi ( $min, $max, $multi, $floor, $ceil ) {

    $rand = rand ( $min, $max);

    if     ( $floor ) $rand = floor ( $rand / $multi ) * $multi;
    elseif ( $ceil  ) $rand = ceil  ( $rand / $multi ) * $multi;
    else              $rand = round ( $rand / $multi ) * $multi;

    if ( $rand < $min ) $rand = $rand + $multi;
    if ( $rand > $max ) $rand = $rand - $multi;

    return $rand;

  }


  function pad_random_one_prime ( $min, $max ) {

    $rand = rand ( $min, $max );

    if ( gmp_prob_prime ($rand) )
      return $rand;

    $rand = gmp_intval ( gmp_nextprime ($rand) );

    if ( $rand <= $max )
      return $rand;
    elseif ( gmp_prob_prime ($min) )
      return $min;
    else
      return gmp_intval ( gmp_nextprime ($min) ); 

  }


  function pad_random_one_power ( $min, $max, $power ) {

    $min = ceil  ( log ( $min, $power ) );
    $max = floor ( log ( $max, $power ) );

    return $power ** rand ( $min, $max ); 

  }


?>