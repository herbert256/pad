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

  $padWhereKeep = $padCurrent [$pad] ?? [];

  foreach ( $padData [$pad] as $padWhereKey => $padWhereRow ) {

    $padCurrent [$pad] = is_array ( $padWhereRow ) ? $padWhereRow : [];

    if ( ! padEvalBool ( (string) $padHandParm ) )
      unset ( $padData [$pad] [$padWhereKey] );

  }

  $padCurrent [$pad] = $padWhereKeep;

?>
