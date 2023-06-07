<?php

  if ( $padRestart )
    include pad . 'inits/restart.php';    
    
  $padEnd [$pad] = strpos ( $padHtml [$pad], '}' );

  if ( $padEnd [$pad] === FALSE )
    return include pad . 'level/end.php';

  $padStart [$pad] = strrpos ( $padHtml [$pad], '{', $padEnd [$pad] - strlen($padHtml [$pad]) );
  
  if ( $padStart [$pad] === FALSE ) {
    $padHtml [$pad] = substr_replace ( $padHtml [$pad], '&close;', $padEnd [$pad], 1 );
    return;
  }

  $padBetween = substr ( $padHtml [$pad], $padStart [$pad] + 1, $padEnd [$pad] - $padStart [$pad] - 1 );
  $padFirst   = substr ( $padBetween , 0, 1 );
  $padWords   = preg_split ("/[\s]+/", $padBetween, 2, PREG_SPLIT_NO_EMPTY);

  $padBusy       = "$padPage --> {" . $padBetween . '}';
  $padHistory [] = "Busy: $padBusy";

  if ( str_contains ( $padWords [0], '@' )       ) return include pad . 'level/at.php';
  if ( in_array ( $padFirst, ['$','!','#','&'] ) ) return include pad . 'level/var.php';
  if ( ! ctype_alpha ( $padBetween [0] )         ) return padIgnore ('ctype_alpha');
  if ( ! padValidTag ( $padWords [0]   )         ) return padIgnore ('padValidTag');
  if ( ! include pad . 'level/type.php'          ) return padIgnore ('type');
  if ( ! include pad . 'level/pair.php'          ) return padIgnore ('pair');;

  include pad . 'level/start.php';

?>