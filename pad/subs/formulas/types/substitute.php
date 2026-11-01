<?php

  // substitute - SUBSTITUTE(text, old, new, [instance]) puts new where old stands in a text -
  // everywhere, or only at the instance-th time it stands there; in the same case only. An
  // instance below 1 is #VALUE!.
  //
  //   =SUBSTITUTE("a-b-c", "-", "+")       a+b+c
  //   =SUBSTITUTE("a-b-c", "-", "+", 2)    a-b+c

  return [
    'group' => 'Text',
    'args'  => [ 3, 4 ],
    'call'  => function ( $args, $env ) {
      $text     = padFormulaArgText ( $args [0], $env );
      $old      = padFormulaArgText ( $args [1], $env );
      $new      = padFormulaArgText ( $args [2], $env );
      $instance = isset ( $args [3] ) ? padFormulaArgInt ( $args [3], $env ) : 0;
      if ( $error = padFormulaFirstError ( $text, $old, $new, $instance ) )
        return $error;
      if ( isset ( $args [3] ) and $instance < 1 )
        return padFormulaError ( '#VALUE!' );
      if ( $old === '' )
        return $text;
      // The length it comes to is worked out first: a text past 32767 characters is #VALUE!,
      // and is never built - SUBSTITUTEs in each other grew a text tenfold each.
      $times = isset ( $args [3] ) ? 1 : substr_count ( $text, $old );
      if ( $times and mb_strlen ( $text ) + $times * ( mb_strlen ( $new ) - mb_strlen ( $old ) ) > padFormulaMaxText )
        return padFormulaError ( '#VALUE!' );
      if ( ! isset ( $args [3] ) )
        return str_replace ( $old, $new, $text );
      $at = -1;
      for ( $seen = 0; $seen < $instance; $seen++ ) {
        $at = strpos ( $text, $old, $at + ( $seen ? strlen ( $old ) : 1 ) );
        if ( $at === FALSE )
          return $text;
      }
      return substr_replace ( $text, $new, $at, strlen ( $old ) );
    }
  ];

?>
