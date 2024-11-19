<?php

  if ( $padRestart )
    return include PAD . 'start/enter/restart.php';    
    
  $padEnd [$pad] = strpos ( $padOut [$pad], '}' );

  if ( $padEnd [$pad] === FALSE )
    return include PAD . 'level/end.php';

  $padStart [$pad] = strrpos ( $padOut [$pad], '{', $padEnd [$pad] - strlen($padOut [$pad]) );
  
  if ( $padStart [$pad] === FALSE ) {
    $padOut [$pad] = substr_replace ( $padOut [$pad], '&close;', $padEnd [$pad], 1 );
    return;
  }

  $padBetween = substr ( $padOut [$pad], $padStart [$pad] + 1, $padEnd [$pad] - $padStart [$pad] - 1 );
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