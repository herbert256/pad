<?php

  // hlookup - HLOOKUP(value, table, row, [approximate]) is VLOOKUP turned on its side: it finds a
  // value in the first row of a table and answers the cell of that column in the row-th row.
  //
  //   =HLOOKUP("Price", A1:E7, 3, FALSE)      0.8
  //   =HLOOKUP("Size", A1:E7, 2, FALSE)       #N/A

  return [
    'group' => 'Lookup',
    'args'  => [ 3, 4 ],
    'call'  => function ( $args, $env ) {
      $wanted = padFormulaScalar ( $args [0], $env );
      $row    = padFormulaArgInt ( $args [2], $env );
      $rough  = array_key_exists ( 3, $args ) ? padFormulaArgBool ( $args [3], $env ) : TRUE;
      if ( $error = padFormulaFirstError ( $wanted, $row, $rough ) )
        return $error;
      $table = $args [1];
      if ( ! padFormulaIsRange ( $table ) )
        return padFormulaError ( '#VALUE!' );
      if ( $row < 1 )
        return padFormulaError ( '#VALUE!' );
      if ( $row > $table ['h'] )
        return padFormulaError ( '#REF!' );
      if ( $wanted === NULL )
        return padFormulaError ( '#N/A' );
      $place = padFormulaLookup ( $wanted, $table ['range'] [0] ?? [], $rough ? 1 : 0 );
      return $place === FALSE ? padFormulaError ( '#N/A' ) : padFormulaPart ( $table, $row - 1, $place, 1, 1, $env );
    }
  ];

?>
