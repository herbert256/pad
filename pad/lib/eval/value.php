<?php

  // Injects the pipe segment's input value into the tokens, the first step of
  // padEvalResult. A '$$' token is the @ placeholder and simply becomes that value; a '%'
  // token is a whole-expression printf format (see padEvalParse) and becomes the value
  // formatted through it, which is how {echo $n | %05.2f} works. Both turn into VAL.

  function padEvalValue( &$result, $value ) {

    // An @ in a part after a pipe of a group's own - ($s | @ . '!') - is the value piped into
    // that part, which padEvalPiped puts in when it runs the group; the stack says, per open
    // bracket, whether a pipe of it stood before the token in the argument the token is in.

    $levels = [];
    $inside = 0;

    foreach ( $result as $key => $val ) {

      $kind = $val [1];

      if ( $kind == 'open' or $kind == 'a-open' ) {
        $levels [] = FALSE;
        continue;
      }

      if ( $kind == 'close' or $kind == 'a-close' ) {
        if ( array_pop ( $levels ) )
          $inside--;
        continue;
      }

      if ( $levels and ! empty ( $val ['arg'] ) and end ( $levels ) ) {
        $levels [ array_key_last ( $levels ) ] = FALSE;
        $inside--;
      }

      if ( $kind == 'pipe' and $levels and ! end ( $levels ) ) {
        $levels [ array_key_last ( $levels ) ] = TRUE;
        $inside++;
      }

      if ( $inside )
        continue;

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
