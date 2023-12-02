<?php
     
  if ( padCloseWithPath () )
    include pad . 'walk/parse_opt.php';

  $padWalk [$pad] = 'next';
  
  include pad . "level/go.php"; 
  include pad . "level/flags.php";

  if ( $padWalk [$pad] ) {

    if     ( $padArray ) $padData [$pad] = $padTagResult;
    elseif ( $padText  ) $padBase [$pad] = $padTagResult;
    elseif ( $padElse  ) $padBase [$pad] = $padFalse [$pad];
 
    reset ( $padData [$pad] );

  }

?>