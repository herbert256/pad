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

      // A format sprintf cannot fill with the one value - %s and %s, a conversion it does
      // not know - is named under the strict check and empty in the lenient walk, where the
      // error sprintf threw ended the request in both.

      if ( $val [1] == '%' ) {

        try {
          $result [$key] [0] = sprintf ( $val [0], $value );
        } catch ( ValueError | ArgumentCountError $padEvalFormatError ) {
          if ( $GLOBALS ['padCheckSyntax'] )
            padError ( "the format '" . padMakeSafe ( $val [0], 40 ) . "' is not one sprintf can fill with the one value" );
          $result [$key] [0] = '';
        }

        $result [$key] [1] = 'VAL';

      }

    }

  }

?>