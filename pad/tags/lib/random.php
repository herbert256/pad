<?php

  function pad_random ( $min, $max, $step, $multi, $count, $floor, $ceil, $prime, $power ) {

    $random = [];

    for ($now=0; $now < $count; $now++)
      $random [] = pad_random_one ($min, $max, $step, $multi, $floor, $ceil, $prime, $power); 

    return $random; 

  }


  function pad_random_unique ( $min, $max, $step, $multi, $count, $floor, $ceil, $prime, $power ) {

    $random = [];

    if ( $multi ) {
 
      $full = ceil ( ($max - $min ) / $multi ) ;
 
      if ( ($min%$multi) == 0 )
        $full++;

    } elseif ( $power ) {
 
      $min = ceil  ( log ( $min, $power ) );
      $max = floor ( log ( $max, $power ) );

      $full = ( $max - $min ) + 1;
  
    } elseif ( $prime ) {
 
      $full = pad_count_primes ( $min, $max );
 
    } elseif ( $step ) {

      $full = ceil ( ($max - $min ) / $step ) ;

      if ( (($max-$min) % $step) == 0 )
        $full++;
 
    } else {
 
      $full = ( $max - $min ) + 1;
 
    }

    if ( $count > $full)
      $count = $full;

    for ($now=0; $now < $count; $now++) {

      do {
        $one = pad_random_one ($min, $max, $step, $multi, $floor, $ceil, $prime, $power );
      } while ( isset ( $random [$one] ) ) ;

      $random [$one] = $one; 

    }

    return $random;

  }


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

    if ( gmp_prob_prime ($rand) == 2 )
      return $rand;

    $rand = gmp_intval ( gmp_nextprime ($rand) );

    if ( $rand <= $max )
      return $rand;
    elseif ( gmp_prob_prime ($min) == 2 )
      return $min;
    else
      return gmp_intval ( gmp_nextprime ($min) );

  }


  function pad_random_one_power ( $min, $max, $power ) {

    return $power ** rand ( $min, $max );

  }


  function pad_count_primes ( $min, $max ) {

    $count = 0;

    for ( $i = $min; $i <= $max; $i++ )
      if ( gmp_prob_prime ($i) )
        $count++;

    return $count;
 
  }

?>