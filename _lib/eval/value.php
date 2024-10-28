<?php


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