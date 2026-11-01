<?php

  // sumifs - SUMIFS(sum_range, criteria_range, criteria, ...) adds up the cells of sum_range where
  // every criteria_range meets its criteria - pairs of a range and a criterion, as many as needed,
  // each range the size of sum_range.
  //
  //   =SUMIFS(B2:B7, A2:A7, "Apples", C2:C7, ">1.15")   10

  return [
    'group' => 'Math',
    'args'  => [ 3, 255 ],
    'pairs' => 1,
    'takes' => 'a range to sum and pairs of a range and its criteria',
    'call'  => function ( $args, $env ) {
      $sum = $args [0];
      if ( ! padFormulaIsRange ( $sum ) )
        return padFormulaError ( '#VALUE!' );
      $pairs = [];
      for ( $at = 1; $at + 1 < count ( $args ); $at += 2 ) {
        if ( padFormulaIsRange ( $args [$at] ) and ( $args [$at] ['w'] != $sum ['w'] or $args [$at] ['h'] != $sum ['h'] ) )
          return padFormulaError ( '#VALUE!' );
        $pairs [] = [ $args [$at], $args [$at + 1] ];
      }
      $hits = padFormulaHits ( $pairs, $env );
      if ( padFormulaIsError ( $hits ) )
        return $hits;
      $total = 0.0;
      foreach ( $hits [0] as [ $row, $column ] ) {
        $value = padFormulaAt ( $sum, $row, $column, $env );
        if ( padFormulaIsError ( $value ) )
          return $value;
        if ( is_float ( $value ) or is_int ( $value ) )
          $total += $value;
      }
      return $total;
    }
  ];

?>
