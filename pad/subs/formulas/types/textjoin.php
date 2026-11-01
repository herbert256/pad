<?php

  // textjoin - TEXTJOIN(delimiter, ignore_empty, text, ...) joins texts with a delimiter between
  // them - every cell of a range in its order; with ignore_empty TRUE the empty ones are left out.
  //
  //   =TEXTJOIN(", ", TRUE, A2:A4)       Apples, Pears, Cherries
  //   =TEXTJOIN("-", FALSE, "a", "", "b")  a--b

  return [
    'group' => 'Text',
    'args'  => [ 3, 254 ],
    'call'  => function ( $args, $env ) {
      $glue  = padFormulaArgText ( $args [0], $env );
      $empty = padFormulaArgBool ( $args [1], $env );
      if ( $error = padFormulaFirstError ( $glue, $empty ) )
        return $error;
      $parts = [];
      foreach ( array_slice ( $args, 2 ) as $arg )
        foreach ( padFormulaIsRange ( $arg ) ? padFormulaFlat ( $arg ) : [ $arg ] as $value ) {
          $part = padFormulaText ( $value );
          if ( padFormulaIsError ( $part ) )
            return $part;
          if ( $part !== '' or ! $empty )
            $parts [] = $part;
        }
      $text = implode ( $glue, $parts );
      return mb_strlen ( $text ) > 32767 ? padFormulaError ( '#VALUE!' ) : $text;
    }
  ];

?>
