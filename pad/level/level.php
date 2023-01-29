<?php

  if ( $padRestart )
    include PAD . 'pad/inits/restart.php';    
    
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

  if     ( $padFirst == '!' ) return padHtml ( include PAD . 'pad/var/raw.php' );
  elseif ( $padFirst == '$' ) return padHtml ( include PAD . 'pad/var/opt.php' );
  elseif ( $padFirst == '%' ) return padHtml ( include PAD . 'pad/var/parm.php' );

  include 'setup.php';    
  include 'between.php';

  if     ( ! ctype_alpha ( $padFirst )      ) return padIgnore ('ctype_alpha');
  elseif ( ! padValid    ( $padTag [$pad] ) ) return padIgnore ('padValid');

  $padPair [$pad] = include 'pair.php';
  $padType [$pad] = include 'type.php';

  if ( $padPair  [$pad] === NULL  ) return padIgnore ('pair');
  if ( $padType  [$pad] === FALSE ) return padIgnore ('type');

  include 'split.php';
  include 'start.php';

?>