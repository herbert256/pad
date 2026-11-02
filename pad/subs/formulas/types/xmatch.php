<?php
  // XMATCH(value, lookup_array, [match_mode], [search_mode]): one-based position.
  return [ 'group' => 'Lookup', 'args' => [ 2, 4 ], 'call' => function ( $args, $env ) {
    $wanted = padFormulaScalar ( $args [0], $env );
    $match = isset ( $args [2] ) ? padFormulaArgInt ( $args [2], $env ) : 0;
    $search = isset ( $args [3] ) ? padFormulaArgInt ( $args [3], $env ) : 1;
    if ( $error = padFormulaFirstError ( $wanted, $match, $search ) ) return $error;
    $list = padFormulaVector ( $args [1] );
    if ( $list === FALSE or ! in_array ( $match, [-1,0,1,2], TRUE ) or ! in_array ( $search, [-2,-1,1,2], TRUE ) )
      return padFormulaError ( '#VALUE!' );
    // The same comparison/wildcard contracts as XLOOKUP. Binary modes require sorted input.
    if ( abs ( $search ) === 2 and $match !== 2 ) {
      $low = 0; $high = count ( $list ) - 1; $best = FALSE;
      while ( $low <= $high ) {
        $middle = intdiv ( $low + $high, 2 );
        if ( $error = padFormulaFirstError ( $list [$middle] ) ) return $error;
        if ( padFormulaSame ( $wanted, $list [$middle] ) ) return (float) ( $middle + 1 );
        $order = padFormulaLookupOrder ( $list [$middle], $wanted );
        if ( $order === NULL ) return padFormulaError ( '#VALUE!' );
        if ( $order === $match ) $best = $middle;
        if ( $order * ( $search > 0 ? 1 : -1 ) < 0 ) $low = $middle + 1;
        else $high = $middle - 1;
      }
      return $best === FALSE ? padFormulaError ( '#N/A' ) : (float) ( $best + 1 );
    }
    $places = array_keys ( $list );
    if ( $search < 0 ) $places = array_reverse ( $places );
    $best = FALSE;
    foreach ( $places as $place ) {
      if ( $error = padFormulaFirstError ( $list [$place] ) ) return $error;
      $same = ( $match === 2 and is_string ( $wanted ) and is_string ( $list [$place] ) )
        ? preg_match ( padFormulaWild ( $wanted ), $list [$place] ) === 1
        : padFormulaSame ( $wanted, $list [$place] );
      if ( $same ) return (float) ( $place + 1 );
      if ( $match === -1 or $match === 1 ) {
        $order = padFormulaLookupOrder ( $list [$place], $wanted );
        if ( $order === $match and ( $best === FALSE or padFormulaLookupOrder ( $list [$place], $list [$best] ) === -$match ) ) $best = $place;
      }
    }
    return $best === FALSE ? padFormulaError ( '#N/A' ) : (float) ( $best + 1 );
  } ];
?>
