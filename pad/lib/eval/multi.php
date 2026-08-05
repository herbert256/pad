<?php

  // Last step of padEvalResult: concatenates values that ended up next to each other with
  // no operator between them, so an expression like {echo 'a' $b 'c'} yields one string.
  // Only scalar neighbours are joined - a pair involving an array is left alone, and
  // padEvalResult will then complain that more than one result came back.

  function padEvalMulti( &$result ) {

    $previous = NULL;

    foreach ( $result as $now => $dummy ) {

      if ( $previous !== NULL )

        if ( $result [$now] [1] == 'VAL' and $result [$previous] [1] == 'VAL' )

          if ( ! is_array ( $result [$previous] [0] ) and ! is_array ( $result [$now] [0] ) ) {

            $result [$now] [0] = $result [$previous] [0] . $result [$now] [0];

            unset ( $result [$previous] );

          }

      $previous = $now;

    }

  }

?>