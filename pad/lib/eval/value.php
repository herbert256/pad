<?php

  // Injects the pipe segment's input value into the tokens, the first step of
  // padEvalResult. A '$$' token is the @ placeholder and simply becomes that value; a '%'
  // token is a whole-expression printf format (see padEvalParse) and becomes the value
  // formatted through it, which is how {echo $n | %05.2f} works. Both turn into VAL.

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