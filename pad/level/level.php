<?php

  if ( $padRestart )
    include pad . 'start/restart.php';    
    
  $padEnd [$pad] = strpos ( $padPad [$pad], '}' );

  if ( $padEnd [$pad] === FALSE )
    return include pad . 'level/end.php';

  $padStart [$pad] = strrpos ( $padPad [$pad], '{', $padEnd [$pad] - strlen($padPad [$pad]) );
  
  if ( $padStart [$pad] === FALSE ) {
    $padPad [$pad] = substr_replace ( $padPad [$pad], '&close;', $padEnd [$pad], 1 );
    return;
  }

  $padBetween = substr ( $padPad [$pad], $padStart [$pad] + 1, $padEnd [$pad] - $padStart [$pad] - 1 );
  $padFirst   = substr ( $padBetween , 0, 1 );
  $padWords   = preg_split ("/[\s]+/", $padBetween, 2, PREG_SPLIT_NO_EMPTY);

  if ( in_array ( $padFirst, ['$','!','#','&','?'] ) ) 
    return include pad . 'level/var.php';

  if ( ! ctype_alpha ( $padFirst ) ) 
    if ( ! padAtCheck ( $padBetween ) )
      return padIgnore ('first char');

  if ( ! include pad . 'level/type.php' ) 
    return padIgnore ('type');

  include pad . 'level/start.php';
 
?>