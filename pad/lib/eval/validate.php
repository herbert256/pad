<?php

  // Stage zero of the evaluator: a structural check of the raw expression before the
  // tokeniser runs, so a mistake in the source is reported as itself rather than surfacing
  // later as a baffling "More than one result back" or a silently swallowed string.
  //
  // Three things are checked, each pinpointed by the position at which it goes wrong: that
  // every ' and " string is closed, and that ( ) and [ ] are balanced and correctly nested.
  // Nothing else is judged here - the tokeniser and the stages after it read the rest. A
  // pipe segment that is a % printf format is kept whole by the tokeniser and so is skipped
  // here as well.
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

    $text = padUnescape ( $eval );

    // A format segment is no expression - %'*10s pads with a quote - and is blanked, its
    // length kept so the positions named after it stay true.

    $segments = padEvalFormatSegments ( $text );

    if ( $segments !== FALSE ) {

      $text = '';

      foreach ( $segments as $n => list ( $part, $format ) )
        $text .= ( $n ? '|' : '' ) . ( $format ? str_repeat ( ' ', strlen ( $part ) ) : $part );

    }

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
  //
  // A clean answer is kept for the request when the text alone decides it - every bare word
  // in it an operator word, a prefixed name or a built-in function, which is one wherever the
  // expression stands (padEvalCheckPure): a loop judges the same text once per row. A word
  // that may name an application function, a tag or a constant is looked up where the
  // expression stands, so such an expression is judged every time, as one that failed is.

  function padEvalCheckPipes ( $result, $eval, $pipe = FALSE ) {

    static $clean = [];

    $key = ( $pipe ? 'p' : 'e' ) . $eval;

    if ( isset ( $clean [$key] ) )
      return TRUE;

    if ( ! padEvalCheckTokens ( $result, $eval, $pipe ) )
      return FALSE;

    if ( count ( $clean ) < 4000 and padEvalCheckPure ( $result ) )
      $clean [$key] = TRUE;

    return TRUE;

  }

  function padEvalCheckPure ( $result ) {

    foreach ( $result as $token )
      if ( $token [1] == 'other' and ! in_array ( strtoupper ( $token [0] ), padEval_txt ) and ! isset ( padEval_alt [ $token [0] ] )
           and ! str_contains ( $token [0], ':' )
           and ! ( preg_match ( '/^[a-zA-Z][a-zA-Z0-9_]*$/D', $token [0] ) and padFileExists ( PAD . "functions/{$token[0]}.php" ) ) )
        return FALSE;

    return TRUE;

  }

  function padEvalCheckTokens ( $result, $eval, $pipe ) {

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

      // A signed number standing alone after a | is the missing-space form of the
      // arithmetic pipe: {echo $x | +1} replaces the value with the literal +1 where
      // {echo $x | + 1} adds one. It can only be that mistake, so it is named as one.

      if ( count ( $tokens ) == 1 and preg_match ( '/^[+\-]\d/', (string) ( $tokens [0] [0] ?? '' ) ) )
        return padEvalValidateError ( "a pipe operator needs a space before its operand - write '| + 1', not '| " . $tokens [0] [0] . "'", $eval );

      if ( $tokens [0] [1] != 'other' ) continue;   // a quoted string, number, $field or @ is a value

      // The head word of a segment is judged however much follows it - {echo $s | lft 2}
      // printed lft2, {echo $s | uppr . 'x'} upprx. One called like a function is named by
      // the check of calls below; a segment that places the value with @ writes its words as
      // text around it - {echo 'x' | zz@yy} is zzxyy.

      if ( count ( $tokens ) > 1 and $tokens [1] [1] == 'open' ) continue;
      if ( in_array ( '$$', array_column ( $tokens, 1 ), TRUE ) ) continue;

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

    // A => pairs a key with its value inside a [ ] list (padEvalArrayItems); anywhere else it
    // has no list to go in, and 'a' => 1 was "More than one result back".

    $open = [];

    foreach ( $tokens as $token )
      if ( in_array ( $token [1], [ 'open', 'a-open' ], TRUE ) )
        $open [] = $token [1];
      elseif ( in_array ( $token [1], [ 'close', 'a-close' ], TRUE ) )
        array_pop ( $open );
      elseif ( $token [1] == 'arrow' and end ( $open ) !== 'a-open' )
        return padEvalValidateError ( "a => pairs a key with its value inside a [ ] list only", $eval );

    // Two operators that each want a value on both sides cannot stand side by side after a
    // value where there is no pipe value to borrow - one segment that is no pipe body, as for
    // a dangling operator below: the first was applied to the empty value alone and the rest
    // ran on as text, $n eq eq 3 printed 3 and $n + * 3 printed 30. In a pipe the first of
    // the two works on the piped value on both sides (eval/actions/alone.php) - {echo 50 |
    // '"' . . '"'} is "5050", a form regression/common misc/eval shows. === and !== are read
    // as == or != and then =, and are named as the PHP spellings they are wherever they stand.

    $alone = ( ! $pipe and count ( $segments ) == 1 );

    foreach ( $tokens as $n => $token )
      if ( $n and padEvalBinary ( $token ) and padEvalBinary ( $tokens [$n+1] ?? [ '', 'end' ] )
           and padEvalParseAfterValue ( $tokens, $n - 1 ) ) {

        $one = $token [2] ?? strtolower ( $token [0] );
        $two = $tokens [$n+1] [2] ?? strtolower ( $tokens [$n+1] [0] );

        if ( $two == '=' and in_array ( $one, [ '==', '!=' ], TRUE ) )
          return padEvalValidateError ( "'$one=' is no operator - " . ( $one == '==' ? 'eq (==)' : 'ne (!=)' )
                                        . " compares two values", $eval );

        if ( $alone )
          return padEvalValidateError ( "the operators '$one' and '$two' stand side by side - a value is missing between them", $eval );

      }

    // A word that is nothing but punctuation - & ~ ^ ; ` and the like - is no part of an
    // expression; it was read as text and joined to its neighbours: $n & 1 printed 3&1.

    foreach ( $tokens as $token )
      if ( $token [1] == 'other' and preg_match ( '/^[^a-zA-Z0-9_\x80-\xff]+$/', $token [0] ) )
        return padEvalValidateError ( "'{$token[0]}' is no operator or value", $eval );

    // A : belongs to a ? before it in its segment - the inline ternary's. On its own it was
    // left over beside the value: $a : 'b' was "More than one result back".

    $open  = 0;
    $depth = 0;

    foreach ( $tokens as $token )
      if ( in_array ( $token [1], [ 'open', 'a-open' ], TRUE ) )
        $depth++;
      elseif ( in_array ( $token [1], [ 'close', 'a-close' ], TRUE ) )
        $depth--;
      elseif ( $token [1] == 'pipe' and $depth <= 0 )
        $open = 0;
      elseif ( $token [1] == 'OPR' and $token [0] === '?' )
        $open++;
      elseif ( $token [1] == 'OPR' and $token [0] === ':' and ! $open-- )
        return padEvalValidateError ( "a : with no ? before it - the inline ternary is cond ? then : else", $eval );

    // A not after a value denies the test that follows it - $s not in ('a', 'b'), not like,
    // not matches, not range (eval/type/parms.php). Before anything else it is a mistake:
    // $a not $b ran the value and the denied one together as text, true whatever they held.

    foreach ( $tokens as $n => $token )
      if ( $token [1] == 'other' and strtoupper ( $token [0] ) == 'NOT' and $n > 0
           and padEvalParseAfterValue ( $tokens, $n - 1 )
           and ! ( ( $tokens [$n+1] [1] ?? '' ) == 'other' and in_array ( strtolower ( $tokens [$n+1] [0] ), padEval_tests ) ) )
        return padEvalValidateError ( "a 'not' after a value denies a test - write \$a not in (...), not like, not matches or not range, or put it in front: not \$a eq \$b", $eval );

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
  // operand is a mistake for. The words and their symbol spellings both count, and ?? - $x ??
  // answered $x and ?? $x the piped value or $x; the unary NOT and the arithmetic operators,
  // which the pipe forms lean on, do not.

  function padEvalComparison ( $token ) {

    $compare = [ 'LT', 'LE', 'GT', 'GE', 'EQ', 'NE', 'AND', 'OR', 'XOR', '??' ];

    if ( $token [1] == 'other' and in_array ( strtoupper ( $token [0] ), $compare ) ) return TRUE;
    if ( $token [1] == 'other' and isset ( padEval_alt [ $token [0] ] )             ) return TRUE;
    if ( $token [1] == 'OPR'   and in_array ( $token [0], $compare )                ) return TRUE;

    return FALSE;

  }

  // Whether a parse token is an operator with a value on either side: the arithmetic ones,
  // the concatenation, the comparisons and logical ones in words or symbols, ?? - not the
  // unary NOT, ! and signs, nor the ternary's ? and :.

  function padEvalBinary ( $token ) {

    if ( $token [1] == 'OPR' )
      return in_array ( $token [0], [ '**', '*', '/', '%', '+', '-', '.', 'LT', 'LE', 'GT', 'GE', 'EQ', 'NE', 'AND', 'XOR', 'OR', '??' ], TRUE );

    if ( $token [1] == 'other' )
      return in_array ( strtoupper ( $token [0] ), [ 'LT', 'LE', 'GT', 'GE', 'EQ', 'NE', 'AND', 'XOR', 'OR' ], TRUE );

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
