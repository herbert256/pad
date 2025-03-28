<?php

  if ( $padRestart )
    return include 'start/enter/restart.php';    
    
  $padEnd [$pad] = strpos ( $padOut [$pad], '}' );

  if ( $padEnd [$pad] === FALSE )
    return include 'level/end.php';

  $padStart [$pad] = strrpos ( $padOut [$pad], '{', $padEnd [$pad] - strlen ( $padOut [$pad] ) );
  
  if ( $padStart [$pad] === FALSE ) 
    return include 'level/noOpen.php';

  $padBetween = substr ( $padOut [$pad], $padStart [$pad] + 1, $padEnd [$pad] - $padStart [$pad] - 1 );
  $padOrgSet  = $padBetween;
  include 'level/between.php';

  if ( ctype_space ( $padFirst ) ) 
    return padIgnore ('first char');
  
  if ( $pad and $padLvlFun [$pad-1] )
    include 'level/function.php';
     
  if ( in_array ( $padFirst, ['$','!','#','&','?','@'] ) ) 
    return include 'tryCatch/go/var.php';

  include 'level/type.php';
  include 'level/tag.php';

  if ( ! $padTypeResult and isset ( $padPrm [$pad] [$padPrmName] ['optional'] ) )
    if ( padValidTag ($padWords [0]) )
      return include 'options/optional.php';

  if ( ! $padTypeResult ) 
    return padIgnore ('type');

  include 'level/start.php';
 
?>