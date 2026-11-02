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
      // A text past 32767 characters is #VALUE!, found as it grows: never more is built.
      $text  = '';
      $count = 0;
      $first = TRUE;
      $space = mb_strlen ( $glue );
      foreach ( array_slice ( $args, 2 ) as $arg )
        foreach ( padFormulaIsRange ( $arg ) ? padFormulaFlat ( $arg ) : [ $arg ] as $value ) {
          $part = padFormulaText ( $value );
          if ( padFormulaIsError ( $part ) )
            return $part;
          if ( $part === '' and $empty )
            continue;
          $count += ( $first ? 0 : $space ) + mb_strlen ( $part );
          if ( $count > padFormulaMaxText )
            return padFormulaError ( '#VALUE!' );
          $text .= ( $first ? '' : $glue ) . $part;
          $first = FALSE;
        }
      return $text;
    }
  ];

?>
