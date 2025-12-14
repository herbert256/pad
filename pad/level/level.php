<?php

  if ( $padRestart )
    return include PAD . 'start/enter/restart.php';

  padLevelEnd ();
  if ( $padEnd [$pad] === FALSE )
    return include PAD . 'level/end.php';

  padLevelStart ();
  if ( $padStart [$pad] === FALSE )
    return padLevelNoOpen ();

  padLevelBetween ();
  include PAD . 'level/pipes/start.php';
  include PAD . 'level/between.php';

  if ( padCommentCheck () )
    return padCommentGo ();

  if ( $padFirst == '/' )
    return padError ( "Closing tag found without an open tag: {" . $padBetween . "}" );

  if ( ctype_space ( $padFirst ) )
    return include PAD . 'level/eval.php';

  if ( $pad and $padLvlFun [$pad-1] )
    include PAD . 'level/function.php';

  if ( in_array ( $padFirst, ['$','!','#','&','?','@'] ) ) {
    $padTry = 'level/var';
    return include PAD . 'try/try.php';
  }

  include PAD . 'level/type.php';
  include PAD . 'level/tag.php';

  if ( ! $padTypeResult )
    return include PAD . 'level/no.php';

  include PAD . 'level/start.php';

?>