<?php

  // countif - COUNTIF(range, criteria) counts the cells of a range that meet the criteria: a
  // value - 5, "Apples", TRUE - or a text starting with = <> < > <= or >=: ">5", "<>Apples",
  // "<=2024-03-31". A number matches numbers and texts that read as that number; a text matches in
  // any case, * standing for any characters and ? for one (~* for a *); "" matches empty cells,
  // "<>" every cell that is not empty.
  //
  //   =COUNTIF(B2:B7, ">5")         4
  //   =COUNTIF(A2:A7, "Apples")     2
  //   =COUNTIF(A2:A7, "?e*")        1
  //   =COUNTIF(A1:A8, "")           1

  return [
    'group' => 'Statistics',
    'args'  => [ 2, 2 ],
    'call'  => function ( $args, $env ) {
      $hits = padFormulaHits ( [ [ $args [0], $args [1] ] ], $env );
      return padFormulaIsError ( $hits ) ? $hits : (float) ( count ( $hits [0] ) + $hits [1] );
    }
  ];

?>
