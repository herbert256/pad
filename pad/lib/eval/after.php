<?php

  // Stage two of the evaluator: turn the raw tokens from padEvalParse into resolved ones.
  // Runs twice over &$result.
  //
  // First pass, over 'other' tokens that are not operator words: an explicit type:name
  // splits on the colon, a bare word is offered to padTypeFunction to see whether it names
  // a pad/app/common function or a tag. Either way the token becomes TYPE, with the kind
  // in [2] and a parameter-end marker in [3] for eval/type/ to fill in later.
  //
  // Second pass resolves everything still symbolic into a plain VAL: remaining 'other'
  // tokens become an operator (padEval_alt for symbols like <=, padEval_txt for words like
  // AND) or else a constant lookup; '$' asks padFieldValue, '&' padTagValue, '#'
  // padOptValue, and hex is decoded. After this only VAL, OPR, TYPE and the structural
  // tokens are left, which is all padEvalResult knows how to handle.

  function padEvalAfter ( &$result ) {

    global $padCheckSyntax;

    foreach ($result as $k => $one)

      if ( $one[1] == 'other' and ! in_array ( strtoupper ( $one[0] ), padEval_precedence ) ) {

        $exp = padExplode ($one[0], ':');

        if ( count($exp) == 2 ) {
          $type = $exp[0];
          $name = $exp[1];
        }
        else {
          $type = padTypeFunction ( $one[0] );
          $name = $one[0];
        }

        if ( padValid ($type) and padValid ($name) ) {
            $result[$k][0] = $name;
            $result[$k][1] = 'TYPE';
            $result[$k][2] = $type;
            $result[$k][3] = 0;
          }

      }

    // A field standing before ?? may be missing - that is what the operator is for.

    $keys = array_keys ( $result );
    $next = [];

    foreach ( $keys as $n => $k )
      $next [$k] = $result [ $keys [$n+1] ?? -1 ] ?? NULL;

    foreach ($result as $k => $one)

      if ( $one[1] == 'other' ) {

        if ( isset ( padEval_alt [$one[0]] ) ) {

          $result[$k][0] = padEval_alt [$one[0]];
          $result[$k][1] = 'OPR';

        } elseif ( in_array ( strtoupper($one[0]), padEval_txt ) ) {

          $result[$k][0] = strtoupper($one[0]);
          $result[$k][1] = 'OPR';

        } else {

          $result[$k][1] = 'VAL';
          $result[$k][0] = padConstant ( $one[0] );

        }

      } elseif ( $one[1] == '$' ) {

        // With the strict syntax check on, a field that does not exist is named rather
        // than read as empty - the discipline {$x} already keeps at level/var.php, here
        // inside an expression. padFieldCheck is the same existence test that form uses.

        $coalesce = ( ( $next [$k] [1] ?? '' ) == 'OPR' and ( $next [$k] [0] ?? '' ) == '??' );

        if ( $padCheckSyntax and ! $coalesce and ! padFieldCheck ( $one[0] ) )
          padError ( "Expression error: there is no field named '\${$one[0]}'" );

        $result[$k][1] = 'VAL';
        $result[$k][0] = padFieldValue ( $one[0] );

      } elseif ( $one[1] == 'prop' ) {

        $result[$k][1] = 'VAL';
        $result[$k][0] = padPropertyValue ( $one[0] );

      } elseif ( $one[1] == '&' ) {

        $result[$k][1] = 'VAL';
        $result[$k][0] = padTagValue ( $one[0], 1 );

      } elseif ( $one[1] == '#' ) {

        $result[$k][1] = 'VAL';
        $result[$k][0] = padOptValue ( $one[0], 1 );

      } elseif ( $one[1] == 'hex' ) {

        // An odd number of digits has its high nibble left out, as 0xABC is 0x0ABC: hex2bin
        // takes whole bytes only and threw "must have an even length" on it.

        $result[$k][1] = 'VAL';
        $result[$k][0] = hex2bin ( ( strlen ( $one[0] ) % 2 ? '0' : '' ) . $one[0] );

      }

  }

?>
