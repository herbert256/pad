<?php

  include '/pad/catch/catch/_catch.php';

  if ( function_exists ( 'padErrorGo' ) )
    padErrorGo ( $padCatchError, $padCatchFile, $padCatchLine );
  else
    padBootStop ( $padCatchError, $padCatchFile, $padCatchLine );

  padStop ( 500 );

?>