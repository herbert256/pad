<?php

  // concat - CONCAT(text, ...) joins texts into one - every cell of a range in its order, a number
  // as it reads in text, TRUE and FALSE in capitals, an empty cell as nothing. The & operator does
  // the same for two values.
  //
  //   =CONCAT("a", 1, TRUE)         a1TRUE
  //   =CONCAT(A2, " x ", B2)        Apples x 10

  return [
    'group' => 'Text',
    'args'  => [ 1, 255 ],
    'call'  => function ( $args ) {
      $text = '';
      foreach ( $args as $arg )
        foreach ( padFormulaIsRange ( $arg ) ? padFormulaFlat ( $arg ) : [ $arg ] as $value ) {
          $part = padFormulaText ( $value );
          if ( padFormulaIsError ( $part ) )
            return $part;
          $text .= $part;
        }
      return mb_strlen ( $text ) > 32767 ? padFormulaError ( '#VALUE!' ) : $text;
    }
  ];

?>
