<?php

  // search - SEARCH(find, within, [start]) is FIND in any case and with wildcards: ? one character,
  // * any run of them, ~ before either for the character itself.
  //
  //   =SEARCH("B", "abc")           2
  //   =SEARCH("c?", "abcd")         3
  //   =SEARCH("x", "abc")           #VALUE!

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
      if ( $find === '' )
        return (float) $start;
      $pattern = substr ( padFormulaWild ( $find ), 2, -5 );
      $rest    = mb_substr ( $within, $start - 1 );
      if ( ! preg_match ( '/' . $pattern . '/isu', $rest, $match, PREG_OFFSET_CAPTURE ) )
        return padFormulaError ( '#VALUE!' );
      return (float) ( $start + mb_strlen ( substr ( $rest, 0, $match [0] [1] ) ) );
    }
  ];

?>
