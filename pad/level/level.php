<?php

  if ( $padRestart )
    return include pad . 'start/enter/restart.php';    
    
  $padEnd [$pad] = strpos ( $padPad [$pad], '}' );

  if ( $padEnd [$pad] === FALSE )
    return include pad . 'level/end.php';

  $padStart [$pad] = strrpos ( $padPad [$pad], '{', $padEnd [$pad] - strlen($padPad [$pad]) );
  
  if ( $padStart [$pad] === FALSE ) {
    $padPad [$pad] = substr_replace ( $padPad [$pad], '&close;', $padEnd [$pad], 1 );
    return;
  }

  $padBetween = substr ( $padPad [$pad], $padStart [$pad] + 1, $padEnd [$pad] - $padStart [$pad] - 1 );
  include pad . 'level/between.php';

  if ( ctype_space ($padFirst ) ) 
    return padIgnore ('first char');
  
  if ( $pad and $padLvlFun [$pad-1] )
    include pad . 'level/function.php';
     
  if ( in_array ( $padFirst, ['$','!','#','&','?','@'] ) ) 
    return include pad . 'level/var.php';

  include pad . 'level/type.php';
  include pad . 'level/tag.php';

  if ( ! $padTypeResult and isset ( $padStartOptions ['optional'] ) )
    if ( padValidTag ($padWords [0]) or padAtCheck ($padWords [0]) )
      return include pad . 'options/optional.php';
  
  if ( ! $padTypeResult ) 
      return padIgnore ('type');

  include pad . 'level/start.php';
 
?>