<?php

  // Stage zero of the evaluator: a structural check of the raw expression before the
  // tokeniser runs, so a mistake in the source is reported as itself rather than surfacing
  // later as a baffling "More than one result back" or a silently swallowed string.
  //
  // Three things are checked, each pinpointed by the position at which it goes wrong: that
  // every ' and " string is closed, and that ( ) and [ ] are balanced and correctly nested.
  // Nothing else is judged here - the tokeniser and the stages after it read the rest. A %
  // printf format is kept whole by the tokeniser and so is skipped here as well.
  //
  // Position is one-based over the expression as the tokeniser sees it - padUnescape()d, so
  // a brace that travelled as an entity is counted as the one character it becomes. Inside a
  // string a backslash escapes the next character, so \' and \" do not close it; a quote of
  // the other kind is an ordinary character there and is left alone.
  //
  // Returns TRUE when the expression is sound, and FALSE when it has reported the first
  // fault it found - eval/eval.php then stops and the expression yields ''.

  function padEvalValidate ( $eval ) {

    if ( str_starts_with ( trim ( $eval ), '%' ) )
      return TRUE;

    $text = padUnescape ( $eval );
    $len  = strlen ( $text );

    $quote   = '';    // the open quote character, '' when outside a string
    $quoteAt = 0;     // where that string opened
    $stack   = [];    // the open ( and [ still waiting to close, each [ char, position ]

    for ( $i = 0; $i < $len; $i++ ) {

      $one = $text [$i];
      $at  = $i + 1;

      if ( $quote ) {

        if ( $one == '\\' )      $i++;             // an escape takes the next character with it
        elseif ( $one == $quote ) $quote = '';     // the matching quote closes the string

        continue;

      }

      if ( $one == "'" or $one == '"' ) {
        $quote   = $one;
        $quoteAt = $at;
        continue;
      }

      if ( $one == '(' or $one == '[' ) {
        $stack [] = [ $one, $at ];
        continue;
      }

      if ( $one == ')' or $one == ']' ) {

        $open = ( $one == ')' ) ? '(' : '[';

        if ( ! $stack )
          return padEvalValidateError ( "the $one at position $at closes nothing that was opened", $text );

        list ( $was, $wasAt ) = array_pop ( $stack );

        if ( $was != $open )
          return padEvalValidateError ( "the $one at position $at does not match the $was opened at position $wasAt", $text );

        continue;

      }

    }

    if ( $quote )
      return padEvalValidateError ( "the string opened with $quote at position $quoteAt is never closed", $text );

    if ( $stack ) {
      list ( $was, $wasAt ) = end ( $stack );
      return padEvalValidateError ( "the $was opened at position $wasAt is never closed", $text );
    }

    return TRUE;

  }

  function padEvalValidateError ( $why, $text ) {

    padError ( "Expression error: $why  ->  $text" );

    return FALSE;

  }

?>