<?php

  // Handles the where option: keeps the rows for which an expression holds, on any data -
  // {staff where='$salary gt 3000'}, {orders where='$status eq "open" and $total gt 100'}.
  //
  // The expression is written quoted, so it reaches this handler as text and is evaluated
  // once per row, with that row standing as the level's current occurrence: its fields come
  // before every other name, and the page's variables are there as usual. Written in
  // order with the other handling options - where before first= takes the first of the
  // rows that pass - and negative turns it into "every row that does not pass".
  //
  // A select table has a where of its own, SQL applied by the query, so its rows are left
  // alone here.

  if ( $padType [$pad] == 'select' )
    return;

  if ( $padHandParm === TRUE or trim ( (string) $padHandParm ) === '' ) {

    if ( $padCheckSyntax )
      padError ( "the where option needs an expression - where='\$field eq 1'" );

    return;

  }

  // The expression is the template's own text, a quoted string on the tag: where=$f took a
  // whole condition from a variable, and a request value with php:abs(-3) in it ran PHP. A
  // value goes into it as $name, which compares the value and stays one; a field spliced
  // into the quotes goes in as a string of the expression (padSpliceQuote, lib/level.php).

  if ( $padProtectValues ) {

    $padWhereOrg = (string) ( $padPrmOrg ?? '' );
    $padWhereOrg = trim ( str_contains ( $padWhereOrg, '=' ) ? substr ( $padWhereOrg, strpos ( $padWhereOrg, '=' ) + 1 ) : $padWhereOrg );

    if ( ! is_string ( padMetaLiteral ( $padWhereOrg ) ) )
      return padError ( "where= is an expression the template writes: give it as a quoted string, and put a value in it as \$name" );

  }

  // A value spliced into the expression where the guards of the splice do not see it - the
  // far part of a where= of more than 4 KB - is refused, as the eval sequence refuses it.

  if ( padSpliceInCode ( (string) $padHandParm ) )
    return padError ( "a value spliced into a where= is part of its code: put it in as \$name" );

  $padWhereKeep = $padCurrent [$pad] ?? [];

  foreach ( $padData [$pad] as $padWhereKey => $padWhereRow ) {

    $padCurrent [$pad] = is_array ( $padWhereRow ) ? $padWhereRow : [];

    if ( ! padEvalBool ( (string) $padHandParm ) )
      unset ( $padData [$pad] [$padWhereKey] );

  }

  $padCurrent [$pad] = $padWhereKeep;

?>
