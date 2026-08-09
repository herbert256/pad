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

  // A second, token-level check, run after the tokeniser has split the expression: the
  // word that follows a | must name a pipe function (or a tag, which the type system can
  // apply as one). Without this a misspelled function is silently taken for a bare constant
  // - {echo $x | uppr} quietly returns the word "uppr" rather than the upper-cased value -
  // which is the single most confusing way an expression can go wrong.
  //
  // $pipe says the whole expression is itself a pipe body - what an opening or closing tag
  // pipe, or a {$x | ...} variable pipe, applies to a value - and there the head word is a
  // function too, not the value it would be in a general expression. So with $pipe the head
  // segment is judged as well; without it, only what follows each | is.
  //
  // Only a segment that is one bare word is judged. A quoted string, a number, a $field or
  // an @ placeholder is a value the pipe deliberately substitutes; an operator word (eq,
  // and, ...) is the unary-with-the-piped-value form; an explicit type:name is checked
  // where it resolves, and already reports itself. Everything longer is an expression that
  // the later stages judge on their own terms.

  function padEvalCheckPipes ( $result, $eval, $pipe = FALSE ) {

    $seg      = 0;
    $segments = [ 0 => [] ];

    foreach ( $result as $token )
      if ( $token [1] == 'pipe' ) {
        $seg++;
        $segments [$seg] = [];
      } else
        $segments [$seg] [] = $token;

    foreach ( $segments as $idx => $tokens ) {

      if ( $idx == 0 and ! $pipe       ) continue;   // in a general expression the head is a value
      if ( count ( $tokens ) != 1     ) continue;   // an expression judges itself in the stages after
      if ( $tokens [0] [1] != 'other' ) continue;   // a quoted string, number, $field or @ is a value

      $word = $tokens [0] [0];
      $up   = strtoupper ( $word );

      if ( in_array ( $up, padEval_txt )        ) continue;   // eq, and, or ... the unary operator form
      if ( in_array ( $up, padEval_precedence ) ) continue;
      if ( isset ( padEval_alt [$word] )        ) continue;
      if ( str_contains ( $word, ':' )          ) continue;   // an explicit prefix reports itself elsewhere
      if ( padTypeFunction ( $word )            ) continue;   // a real function, or a tag applied as one
      if ( defined ( $word )                    ) continue;   // a defined constant

      return padEvalValidateError ( "there is no pipe function named '$word'", $eval );

    }

    // A comparison or logical operator needs a value on both sides. When one is missing the
    // evaluator borrows the pipe value for it, which is the point of {echo $x | + 1} - so
    // the check is made only where there is no pipe value to borrow: a single-segment
    // expression that is not itself a pipe body. There a leading or trailing eq, ne, and,
    // or the rest is a mistake - {if $x eq} - not a shorthand, and is named as one.

    if ( ! $pipe and count ( $segments ) == 1 and $segments [0] ) {

      $tokens = array_values ( $segments [0] );
      $first  = $tokens [0];
      $last   = $tokens [ count ( $tokens ) - 1 ];

      if ( padEvalComparison ( $first ) )
        return padEvalValidateError ( "the operator '{$first[0]}' has nothing on its left", $eval );

      if ( count ( $tokens ) > 1 and padEvalComparison ( $last ) )
        return padEvalValidateError ( "the operator '{$last[0]}' has nothing on its right", $eval );

    }

    return TRUE;

  }

  // Whether a parse token is a two-sided comparison or logical operator - the ones a dangling
  // operand is a mistake for. The words and their symbol spellings both count; the unary NOT
  // and the arithmetic operators, which the pipe forms lean on, do not.

  function padEvalComparison ( $token ) {

    $compare = [ 'LT', 'LE', 'GT', 'GE', 'EQ', 'NE', 'AND', 'OR', 'XOR' ];

    if ( $token [1] == 'other' and in_array ( strtoupper ( $token [0] ), $compare ) ) return TRUE;
    if ( $token [1] == 'other' and isset ( padEval_alt [ $token [0] ] )             ) return TRUE;
    if ( $token [1] == 'OPR'   and in_array ( $token [0], $compare )                ) return TRUE;

    return FALSE;

  }

  function padEvalValidateError ( $why, $text ) {

    padError ( "Expression error: $why  ->  $text" );

    return FALSE;

  }

?>