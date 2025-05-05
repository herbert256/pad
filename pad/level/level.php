<?php

  if ( $padRestart )
    return include 'start/enter/restart.php';    
    
  padLevelEnd ();
  if ( $padEnd [$pad] === FALSE )
    return include 'level/end.php';

  padLevelStart ();
  if ( $padStart [$pad] === FALSE ) 
    return padLevelNoOpen ();

  padLevelBetween ();
  include 'level/between.php';

  if ( $padFirst == '/' ) 
    return padError ( "Closing tag found without an open tag: {" . $padBetween . "}" );

  if ( ctype_space ( $padFirst ) ) 
    return padLevelNoSingle ();
  
  if ( $pad and $padLvlFun [$pad-1] )
    include 'level/function.php';
     
  if ( in_array ( $padFirst, ['$','!','#','&','?','@'] ) ) 
    return include 'level/var.php';

  include 'level/type.php';
  include 'level/tag.php';

  if ( ! $padTypeResult and in_array ( 'optional', $padPrmParse ) )
    if ( padValidTag ($padWords [0]) )
      return include 'options/optional.php';

  if ( ! $padTypeResult ) 
    if ( $padPairSet ) return padLevelNoPair   ();
    else               return padLevelNoSingle ();

  include 'level/start.php';
 
?>