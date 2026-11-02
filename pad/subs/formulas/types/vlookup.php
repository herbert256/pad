<?php

  // vlookup - VLOOKUP(value, table, column, [approximate]) finds a value in the first column of a
  // table and answers the cell of that row in the column-th column. Exact with approximate FALSE -
  // a text in any case, * and ? as wildcards; else, in a first column that rises, the last value
  // not above the one looked for. Not found is #N/A, a column below 1 #VALUE!, past the table
  // #REF!.
  //
  //   =VLOOKUP("Cherries", A2:C7, 3, FALSE)   3.5
  //   =VLOOKUP("pea*", A2:C7, 2, FALSE)       5
  //   =VLOOKUP("Kiwis", A2:C7, 2, FALSE)      #N/A

  return [
    'group' => 'Lookup',
    'args'  => [ 3, 4 ],
    'call'  => function ( $args, $env ) {
      $wanted = padFormulaScalar ( $args [0], $env );
      $column = padFormulaArgInt ( $args [2], $env );
      $rough  = array_key_exists ( 3, $args ) ? padFormulaArgBool ( $args [3], $env ) : TRUE;
      if ( $error = padFormulaFirstError ( $wanted, $column, $rough ) )
        return $error;
      $table = $args [1];
      if ( ! padFormulaIsRange ( $table ) )
        return padFormulaError ( '#VALUE!' );
      if ( $column < 1 )
        return padFormulaError ( '#VALUE!' );
      if ( $column > $table ['w'] )
        return padFormulaError ( '#REF!' );
      if ( $wanted === NULL )
        return padFormulaError ( '#N/A' );
      $list  = array_map ( fn ( $row ) => $row [0] ?? NULL, $table ['range'] );
      $place = padFormulaLookup ( $wanted, $list, $rough ? 1 : 0 );
      return $place === FALSE ? padFormulaError ( '#N/A' ) : padFormulaPart ( $table, $place, $column - 1, 1, 1, $env );
    }
  ];

?>
