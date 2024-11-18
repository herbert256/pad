<?php

  if ( $padRestart )
    return include PAD . 'start/enter/restart.php';    
    
  $padEnd [$pad] = strpos ( $padPad [$pad], '}' );

  if ( $padEnd [$pad] === FALSE )
    return include PAD . 'level/end.php';

  $padStart [$pad] = strrpos ( $padPad [$pad], '{', $padEnd [$pad] - strlen($padPad [$pad]) );
  
  if ( $padStart [$pad] === FALSE ) {
    $padPad [$pad] = substr_replace ( $padPad [$pad], '&close;', $padEnd [$pad], 1 );
    return;
  }

  $padBetween = substr ( $padPad [$pad], $padStart [$pad] + 1, $padEnd [$pad] - $padStart [$pad] - 1 );
  include PAD . 'level/between.php';

  if ( ctype_space ( $padFirst ) ) 
    return padIgnore ('first char');
  
  if ( $pad and $padLvlFun [$pad-1] )
    include PAD . 'level/function.php';
     
  if ( in_array ( $padFirst, ['$','!','#','&','?','@'] ) ) 
    return include PAD . 'catch/var.php';

  include PAD . 'level/type.php';
  include PAD . 'level/tag.php';

  if ( ! $padTypeResult and isset ( $padOptionsSingle ['optional'] ) )
    if ( padValidTag ($padWords [0]) )
      return include PAD . 'options/optional.php';

  if ( ! $padTypeResult ) 
    return padIgnore ('type');

  include PAD . 'level/start.php';
 
?>