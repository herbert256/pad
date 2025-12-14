<?php


  /**
   * Concatenates consecutive VAL tokens into one.
   *
   * When two adjacent tokens are both VAL (value) types and neither
   * is an array, merges them by string concatenation into one token.
   *
   * @param array &$result The token array (modified in place).
   *
   * @return void
   */
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