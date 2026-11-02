<?php

  // xlookup - XLOOKUP(value, lookup_array, return_array, [if_not_found], [match_mode],
  // [search_mode]) finds a value in a row or a column and answers what stands at that place in
  // another of the same length. match_mode 0 is exact (the default), -1 exact or else the next
  // smaller, 1 exact or else the next larger, 2 with the wildcards * and ?; search_mode 1 from the
  // first (the default), -1 from the last - 2 and -2, a binary search in Excel, search the same
  // way as 1 and -1. Not found is if_not_found, else #N/A.
  //
  //   =XLOOKUP("Plums", A2:A7, C2:C7)               2.25
  //   =XLOOKUP("Kiwis", A2:A7, C2:C7, "none")       none
  //   =XLOOKUP(12, B2:B7, A2:A7, , 1)               Apples
  //   =XLOOKUP("Apples", A2:A7, B2:B7, , 0, -1)     15

  return [
    'group' => 'Lookup',
    'args'  => [ 3, 6 ],
    'call'  => function ( $args, $env ) {
      $wanted = padFormulaScalar ( $args [0], $env );
      $match  = ( ( $args [4] ?? NULL ) !== NULL ) ? padFormulaArgInt ( $args [4], $env ) : 0;
      $search = ( ( $args [5] ?? NULL ) !== NULL ) ? padFormulaArgInt ( $args [5], $env ) : 1;
      if ( $error = padFormulaFirstError ( $wanted, $match, $search ) )
        return $error;
      [ $lookup, $return ] = [ $args [1], $args [2] ];
      if ( ! padFormulaIsRange ( $lookup ) or ! padFormulaIsRange ( $return )
           or ! in_array ( $match, [ -1, 0, 1, 2 ], TRUE ) or ! in_array ( $search, [ -2, -1, 1, 2 ], TRUE ) )
        return padFormulaError ( '#VALUE!' );
      $down = ( $lookup ['w'] == 1 );
      if ( ! $down and $lookup ['h'] != 1 )
        return padFormulaError ( '#VALUE!' );
      if ( $down ? $return ['h'] != $lookup ['h'] : $return ['w'] != $lookup ['w'] )
        return padFormulaError ( '#VALUE!' );
      $list   = padFormulaVector ( $lookup );
      $places = array_keys ( $list );
      if ( $search < 0 )
        $places = array_reverse ( $places );
      $found = FALSE;
      $best  = FALSE;
      foreach ( $places as $place ) {
        if ( padFormulaSame ( $wanted, $list [$place], $match == 2 ) ) {
          $found = $place;
          break;
        }
        if ( $match == 1 or $match == -1 ) {
          $order = padFormulaLookupOrder ( $list [$place], $wanted );
          if ( $order !== NULL and $order == $match
               and ( $best === FALSE or padFormulaLookupOrder ( $list [$place], $list [$best] ) == -$match ) )
            $best = $place;
        }
      }
      $found = ( $found === FALSE ) ? $best : $found;
      if ( $found === FALSE )
        return ( ( $args [3] ?? NULL ) !== NULL ) ? $args [3] : padFormulaError ( '#N/A' );
      return $down ? padFormulaPart ( $return, $found, 0, 1, $return ['w'], $env )
                   : padFormulaPart ( $return, 0, $found, $return ['h'], 1, $env );
    }
  ];

?>
