<?php

  if ( $padRestart )
    include PAD . 'inits/restart.php';    
    
  $padEnd [$pad] = strpos ( $padHtml [$pad], '}' );

  if ( $padEnd [$pad] === FALSE )
    return include 'end.php';

  $padStart [$pad] = strrpos ( $padHtml [$pad], '{', $padEnd [$pad] - strlen($padHtml [$pad]) );
  
  if ( $padStart [$pad] === FALSE ) {
    $padHtml [$pad] = substr_replace ( $padHtml [$pad], '&close;', $padEnd [$pad], 1 );
    return;
  }

  $padBetween = substr ( $padHtml [$pad], $padStart [$pad] + 1, $padEnd [$pad] - $padStart [$pad] - 1 ) ;
  $padWords   = preg_split ("/[\s]+/", $padBetween, 2, PREG_SPLIT_NO_EMPTY);

  if ( padValidFieldName ( $padBetween ) )
    return include 'var.php';

  if ( ! ctype_alpha ( $padBetween [0] ) ) return padIgnore ('ctype_alpha');
  if ( ! padValidTag ( $padWords[0]    ) ) return padIgnore ('padValid');

  if ( ! include 'type.php'  ) 
    return;

  include 'setup.php';    
  include 'parms.php';

  if ( ! include 'pair.php'  ) 
    return;

  include 'split.php';
  include 'start.php';

?>