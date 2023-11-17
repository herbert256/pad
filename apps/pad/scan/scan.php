<?php
    

  if ( ! isset ($file ) )
    return;
  
  $zzz [$file] = FALSE; 

  $end = strpos ( $source, '}' );

  if ( $end === FALSE ) {
    $source = '';
    return;
  }

  $start = strrpos ( $source, '{', $end - strlen($source) );
  
  if ( $start === FALSE ) {
    $source = '';
    return;
  }

  $between = substr ( $source, $start + 1, $end - $start - 1 );

  if ( ! trim ($between) ) {
    $source = substr ( $source, $end );
    return;
  }

  $first   = substr ( $between , 0, 1 );
  $words   = preg_split ("/[\s]+/", $between, 2, PREG_SPLIT_NO_EMPTY);

  $explode = padExplode ($words [0], ':') ;

  if ( ! count ($explode) ) {
    $tag  = '';
    $type = FALSE;
  } elseif ( count ($explode) == 1 ) {
    $tag  = $explode [0];
    $type = padTypeGet( $tag );
  } else {
    $tag  = $explode [1];       
    $type = padTypeCheck ( $explode [0], $tag ); 
  } 

  if ( in_array ( $first, ['$','!','#','&'] )  
      or ! padValidFirstChar ( $first ) 
      or ! padValidTag ( $words [0] ) 
      or $type <> 'pad'
      or ! $type ) {
    $source = substr ( $source, $end );
    return;
  }

  $tags [$tag] [$file] = FALSE;

  $parse = padParseOptions ( trim ( $words [1] ?? '' ) );

  foreach ( $parse as $k => $v ) {

    list ($name, $value ) = padSplit ('=', trim($v));

    if ( padValidVar ($name) ) {
      $options [$name] [$file] = FALSE;
      continue;
    }

  }

  $source = substr ( $source, $end );

?>