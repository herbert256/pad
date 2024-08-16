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

  if ( ctype_space ( $padFirst ) ) 
    return padIgnore ('first char');
  
  if ( $padSyntax and $padFirst == '/' and padValidTag ( substr ($padBetween, 1) ) )
    return padError ( "Closing tag without opening tag found: {{$padBetween}}");
  
  if ( $pad and $padLvlFun [$pad-1] )
    include pad . 'level/function.php';
     
  if ( in_array ( $padFirst, ['$','!','#','&','?','@'] ) ) 
    return include pad . 'catch/var.php';

  include pad . 'level/type.php';
  include pad . 'level/tag.php';

  if ( ! $padTypeResult and isset ( $padStartOptions ['optional'] ) )
    if ( padValidTag ($padWords [0]) )
      return include pad . 'options/optional.php';

  if ( $padSyntax and ! $padTypeResult and padValidTag ( $padWords [0] ) )
    return padError ( "Tag not Found: {{$padBetween}}");
  
  if ( ! $padTypeResult ) 
    return padIgnore ('type');

  include pad . 'level/start.php';
 
?>