<?php

  // choose - CHOOSE(index, value, ...) is the index-th of the values, from 1; an index below 1 or
  // past the values is #VALUE!.
  //
  //   =CHOOSE(2, "a", "b", "c")     b
  //   =CHOOSE(4, "a", "b", "c")     #VALUE!

  return [
    'group' => 'Lookup',
    'args'  => [ 2, 255 ],
    'call'  => function ( $args, $env ) {
      $index = padFormulaArgInt ( $args [0], $env );
      if ( padFormulaIsError ( $index ) )
        return $index;
      return ( $index < 1 or $index >= count ( $args ) ) ? padFormulaError ( '#VALUE!' ) : $args [$index];
    }
  ];

?>
