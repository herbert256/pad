<?php

  if ( $padRestart )
    include pad . 'start/enter/restart.php';    
    
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

  if ( isset ( $GLOBALS ['padInFunction'] ) )
    $GLOBALS ['padInFunctionVars'] = get_defined_vars ();
  
  if ( in_array ( $padFirst, ['$','!','#','&','?'] ) ) 
    return include pad . 'level/var.php';

  if ( ! ctype_alpha ( $padFirst ) and ! str_contains ( $padTagCheck, '@') ) 
    return padIgnore ('first char');

  $padTypeParse = include pad . 'level/type.php';

  if ( ! $padTypeParse )
    return padIgnore ('type');

  include pad . 'level/tag.php';
  include pad . 'level/start.php';
 
?>