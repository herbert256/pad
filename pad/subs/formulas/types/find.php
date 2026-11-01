<?php

  // find - FIND(find, within, [start]) is the place of the first character of one text in
  // another, from the start-th character on, the first being 1 - in the same case, without
  // wildcards. Not found, or a start past the text, is #VALUE!.
  //
  //   =FIND("b", "abcb")            2
  //   =FIND("b", "abcb", 3)         4
  //   =FIND("B", "abc")             #VALUE!

  return [
    'group' => 'Text',
    'args'  => [ 2, 3 ],
    'call'  => function ( $args, $env ) {
      $find   = padFormulaArgText ( $args [0], $env );
      $within = padFormulaArgText ( $args [1], $env );
      $start  = isset ( $args [2] ) ? padFormulaArgInt ( $args [2], $env ) : 1;
      if ( $error = padFormulaFirstError ( $find, $within, $start ) )
        return $error;
      if ( $start < 1 or $start > mb_strlen ( $within ) + ( $find === '' ? 1 : 0 ) )
        return padFormulaError ( '#VALUE!' );
      $at = mb_strpos ( $within, $find, $start - 1 );
      return $at === FALSE ? padFormulaError ( '#VALUE!' ) : (float) ( $at + 1 );
    }
  ];

?>
