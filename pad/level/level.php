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
  $padFirst   = substr ( $padBetween , 0, 1 );
  $padWords   = preg_split ("/[\s]+/", $padBetween, 2, PREG_SPLIT_NO_EMPTY);

  if ( padValidFieldName ( $padBetween ) )
    return include 'var.php';

  if ( ! ctype_alpha ( $padFirst )    ) return padIgnore ('ctype_alpha');
  if ( ! padValidTag ( $padWords[0] ) ) return padIgnore ('padValid');

  $padTypeCheck  = trim ( $padWords[0] ?? '' );
  $padTypeResult = include 'type.php';
  if ( $padTypeResult === FALSE ) 
    return padIgnore ('type');

  include 'setup.php';    
  include 'parms.php';

  $padPair [$pad] = include 'pair.php';
  if ( $padPair [$pad] === NULL ) 
    return padIgnore ('pair', 1);

  include 'split.php';
  include 'start.php';

?>