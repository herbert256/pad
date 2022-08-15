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

  $padBetween = substr ( $padHtml [$pad], $padStart [$pad] + 1, $padEnd [$pad]-$padStart [$pad] - 1 ) ;
  $padFirst   = substr ( $padBetween , 0, 1 );

  if     ( $padFirst == '!' ) return pHtml ( include PAD . 'var/raw.php' );
  elseif ( $padFirst == '$' ) return pHtml ( include PAD . 'var/opt.php' );

  include 'setup.php';    
  include 'between.php';

  if     ( ! ctype_alpha ( $padFirst )    ) return pIgnore ('ctype_alpha');
  elseif ( ! pValid      ( $padTag [$pad] ) ) return pIgnore ('pValid');

  $padPair  [$pad] = include 'pair.php';
  $padType  [$pad] = include 'type_get.php';
  $padSplit [$pad] = include 'split.php';

  if ( $padPair  [$pad] === NULL  ) return pIgnore ('pair');
  if ( $padType  [$pad] === FALSE ) return pIgnore ('type_get');
  if ( $padSplit [$pad] === FALSE ) return pIgnore ('split');

  include 'start.php';

?>