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

    $after = [];

    foreach ( $keys as $n => $k ) {
      $next  [$k] = $result [ $keys [$n+1] ?? -1 ] ?? NULL;
      $after [$k] = $result [ $keys [$n+2] ?? -1 ] ?? NULL;
    }

    foreach ($result as $k => $one)

      if ( $one[1] == 'other' ) {

        // [2] keeps the operator as the template wrote it, for a message to name.

        if ( isset ( padEval_alt [$one[0]] ) ) {

          $result[$k][0] = padEval_alt [$one[0]];
          $result[$k][1] = 'OPR';
          $result[$k][2] = $one[0];

        } elseif ( in_array ( strtoupper($one[0]), padEval_txt ) ) {

          $result[$k][0] = strtoupper($one[0]);
          $result[$k][1] = 'OPR';
          $result[$k][2] = $one[0];

        } else {

          $result[$k][1] = 'VAL';
          $result[$k][0] = padConstant ( $one[0] );

        }

      } elseif ( $one[1] == '$' or $one[1] == '$$n' ) {

        // With the strict syntax check on, a field that does not exist is named rather
        // than read as empty - the discipline {$x} already keeps at level/var.php, here
        // inside an expression. padFieldCheck is the same existence test that form uses.

        // So may one piped into a fallback - | ?? '-', | default('-'), | optional - the heads
        // the {$x | ...} form lets through (level/var.php): {echo $x | default('-')} named the
        // field missing where {$x | default('-')} printed the dash.

        $coalesce = ( ( ( $next [$k] [1] ?? '' ) == 'OPR' and ( $next [$k] [0] ?? '' ) == '??' )
                      or ( ( $next [$k] [1] ?? '' ) == 'pipe'
                           and ( ( ( $after [$k] [1] ?? '' ) == 'OPR' and ( $after [$k] [0] ?? '' ) == '??' )
                                 or ( in_array ( $after [$k] [1] ?? '', [ 'TYPE', 'other' ], TRUE ) and in_array ( $after [$k] [0] ?? '', [ 'default', 'optional' ], TRUE ) ) ) ) );

        // $$x reads the field whose name the field x holds (parse.php). The name must be an
        // application field or a path into one, as {$$x} has it (padValueName, level/var.php):
        // a value naming one of the engine's globals reads nothing.

        $name = $one[0];
        $via  = '';

        if ( $one[1] == '$$n' ) {

          if ( $padCheckSyntax and ! $coalesce )
            padEvalFieldReport ( $one[0], '$' . $one[0] );

          $name = padFieldValue ( $one[0] );
          $name = ( is_scalar ( $name ) and ! is_bool ( $name ) ) ? (string) $name : '';
          $via  = " (the name \$\${$one[0]} holds)";

          if ( ! padValueName ( $name ) ) {

            if ( $padCheckSyntax and ! $coalesce and ! padStrHidden ( $one[0] ) )
              padError ( "Expression error: the name '" . padMakeSafe ( $name, 40 ) . "' that \$\${$one[0]} takes from a value is no application variable" );

            $result[$k][1] = 'VAL';
            $result[$k][0] = '';

            continue;

          }

        }

        // A field that holds a list is there, and is no value here - $x reads a scalar. It
        // was reported as a field that does not exist: {if $items eq 'a'} with $items set.

        if ( $padCheckSyntax and ! $coalesce )
          padEvalFieldReport ( $name, '$' . $name, $via );

        $result[$k][1] = 'VAL';
        $result[$k][0] = padFieldValue ( $name );

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

  // Under the strict check: a field that is no value named as what it is - an object, a
  // list, or not there - with the field as the template spelled it, $x or field:x, and $via
  // saying where the name came from. A name a sandbox hid misses quietly (padStrHidden).

  function padEvalFieldReport ( $name, $spelled, $via = '' ) {

    if ( padFieldCheck ( $name ) or padStrHidden ( $name ) )
      return;

    if ( padFieldObject ( $name ) )
      padError ( "Expression error: the field '$spelled'$via is an object, not a value" );
    elseif ( padArrayCheck ( $name ) )
      padError ( "Expression error: the field '$spelled'$via is a list, not a value" );
    else
      padError ( "Expression error: there is no field named '$spelled'$via" );

  }

  // Whether a field that is no value holds an object - a stdClass record, a model - so that
  // the report names it as what it is rather than as a field that does not exist. A date
  // object and one that writes itself as text are values (padField) and never get here.

  function padFieldObject ( $name ) {

    if ( str_contains ( $name, ':' ) )
      return FALSE;

    $raw = ( str_contains ( $name, '@' ) or str_contains ( $name, '.' ) ) ? padFieldAt ( $name, 0 ) : padFieldLevel ( $name, 2 );

    return is_object ( $raw );

  }

?>
