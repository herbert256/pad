<?php


  /**
   * Resolves value placeholder tokens.
   *
   * Replaces $$ tokens with the passed value and % tokens
   * with sprintf-formatted values using the passed value.
   *
   * @param array &$result The token array (modified in place).
   * @param mixed $value   The value to substitute.
   *
   * @return void
   */
  function padEvalValue( &$result, $value ) {

    foreach ( $result as $key => $val ) {

      if ( $val [1] == '$$' ) {
        $result [$key] [0] = $value;
        $result [$key] [1] = 'VAL';
      }

      if ( $val [1] == '%' ) {
        $result [$key] [0] = sprintf ( $val [0], $value);
        $result [$key] [1] = 'VAL';
      }

    }

  }


?>