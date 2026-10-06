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
  // a brace that travelled as an entity is counted as the one character it becomes - and in
  // characters, not bytes: after an é every position named was one too far. Inside a
  // string a backslash escapes the next character, so \' and \" do not close it; a quote of
  // the other kind is an ordinary character there and is left alone.
  //
  // Returns TRUE when the expression is sound, and FALSE when it has reported the first
  // fault it found - eval/eval.php then stops and the expression yields ''.

  function padEvalValidate ( $eval ) {

    if ( padEvalFormat ( $eval ) )
      return TRUE;

    $text = padUnescape ( $eval );
    $len  = strlen ( $text );

    // A position is counted in bytes on the way and named in characters (padEvalValidateAt).

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
          return padEvalValidateError ( "the $one at position " . padEvalValidateAt ( $text, $at ) . " closes nothing that was opened", $text );

        list ( $was, $wasAt ) = array_pop ( $stack );

        if ( $was != $open )
          return padEvalValidateError ( "the $one at position " . padEvalValidateAt ( $text, $at ) . " does not match the $was opened at position " . padEvalValidateAt ( $text, $wasAt ), $text );

        continue;

      }

    }

    if ( $quote )
      return padEvalValidateError ( "the string opened with $quote at position " . padEvalValidateAt ( $text, $quoteAt ) . " is never closed", $text );

    if ( $stack ) {
      list ( $was, $wasAt ) = end ( $stack );
      return padEvalValidateError ( "the $was opened at position " . padEvalValidateAt ( $text, $wasAt ) . " is never closed", $text );
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

      // A pipe with nothing between it and the next - or nothing behind it - is skipped
      // silently by the walk. Strict mode names the hole. In a pipe body the head
      // segment counts too: {$x | | upper} arrives here as an empty head.

      if ( count ( $tokens ) == 0 and ( $idx > 0 or $pipe ) )
        return padEvalValidateError ( "an empty pipe segment", $eval );

      if ( $idx == 0 and ! $pipe       ) continue;   // in a general expression the head is a value
      if ( count ( $tokens ) != 1     ) continue;   // an expression judges itself in the stages after

      // A signed number standing alone after a | is the missing-space form of the
      // arithmetic pipe: {echo $x | +1} replaces the value with the literal +1 where
      // {echo $x | + 1} adds one. It can only be that mistake, so it is named as one.

      if ( preg_match ( '/^[+\-]\d/', (string) ( $tokens [0] [0] ?? '' ) ) )
        return padEvalValidateError ( "a pipe operator needs a space before its operand - write '| + 1', not '| " . $tokens [0] [0] . "'", $eval );

      if ( $tokens [0] [1] != 'other' ) continue;   // a quoted string, number, $field or @ is a value

      $word = $tokens [0] [0];

      if ( padEvalWordKnown ( $word ) ) continue;

      if ( function_exists ( $word ) )
        return padEvalValidateError ( "the PHP function '$word' is not allowed by \$padPhpFunctions", $eval );

      return padEvalValidateError ( "there is no pipe function named '$word'", $eval );

    }

    // A sign the tokeniser read as NEG or POS - after an operator, a bracket or a comma - has
    // a value of its own to work on and never borrows the piped one: with nothing behind it
    // it is a mistake, {echo $a + -}, which answered 5 + 0 without a word since the sign
    // became an operator of its own.

    $tokens = array_values ( $result );

    foreach ( $tokens as $n => $token )
      if ( $token [1] == 'OPR' and in_array ( $token [0], [ 'NEG', 'POS' ] )
           and in_array ( $tokens [$n+1] [1] ?? 'end', [ 'end', 'pipe', 'close', 'a-close' ] ) )
        return padEvalValidateError ( "the operator '" . ( $token [2] ?? $token [0] ) . "' has nothing on its right", $eval );

    // A word called like a function - zzzq(2) - must name one, wherever it stands. Taken for
    // the word itself it was joined to its arguments: {echo $x | zzzq(2)} printed zzzq2 and
    // {if zzzq(1) eq 1} compared that, where {echo $x | zzzq} is named as no pipe function.

    $tokens = array_values ( $result );

    foreach ( $tokens as $n => $token )
      if ( $token [1] == 'other' and ( $tokens [$n+1] [1] ?? '' ) == 'open' and ! padEvalWordKnown ( $token [0] ) )
        if ( function_exists ( $token [0] ) )
          return padEvalValidateError ( "the PHP function '{$token[0]}' is not allowed by \$padPhpFunctions", $eval );
        else
          return padEvalValidateError ( "there is no function named '{$token[0]}'", $eval );

    // A comparison or logical operator needs a value on both sides. When one is missing the
    // evaluator borrows the pipe value for it, which is the point of {echo $x | + 1} - so
    // the check is made only where there is no pipe value to borrow: a single-segment
    // expression that is not itself a pipe body. There a leading or trailing eq, ne, and,
    // or the rest is a mistake - {if $x eq} - not a shorthand, and is named as one.

    if ( ! $pipe and count ( $segments ) == 1 and $segments [0] ) {

      $tokens = array_values ( $segments [0] );
      $first  = $tokens [0];
      $last   = $tokens [ count ( $tokens ) - 1 ];

      // Named as the template wrote it: $x >= named the operator GE.

      if ( padEvalComparison ( $first ) )
        return padEvalValidateError ( "the operator '" . ( $first [2] ?? $first [0] ) . "' has nothing on its left", $eval );

      if ( count ( $tokens ) > 1 and padEvalComparison ( $last ) )
        return padEvalValidateError ( "the operator '" . ( $last [2] ?? $last [0] ) . "' has nothing on its right", $eval );

    }

    return TRUE;

  }

  // Whether a bare word reads as something: an operator word (eq, and, or ... the unary
  // operator form), a prefixed name - which reports itself where it resolves - a real
  // function or a tag applied as one, or a defined constant.

  function padEvalWordKnown ( $word ) {

    $up = strtoupper ( $word );

    if ( in_array ( $up, padEval_txt )        ) return TRUE;
    if ( in_array ( $up, padEval_precedence ) ) return TRUE;
    if ( isset ( padEval_alt [$word] )        ) return TRUE;
    if ( str_contains ( $word, ':' )          ) return TRUE;
    if ( padTypeFunction ( $word )            ) return TRUE;
    if ( defined ( $word )                    ) return TRUE;

    return FALSE;

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

  // The one-based character position of the one-based byte position $at in $text.

  function padEvalValidateAt ( $text, $at ) {

    return mb_strlen ( substr ( $text, 0, $at - 1 ), 'UTF-8' ) + 1;

  }

  function padEvalValidateError ( $why, $text ) {

    global $padCheckSyntax;

    // Reported under the strict syntax check; either way FALSE goes back, and the
    // expression yields '' - the lenient contract for what cannot be evaluated.

    if ( $padCheckSyntax )
      padError ( "Expression error: $why  ->  $text" );

    return FALSE;

  }

?>
