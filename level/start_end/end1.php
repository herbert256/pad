<?php

  if ( $GLOBALS ['padInfo'] )  
    include '/pad/events/end.php';
  
  list ( $padBase [$pad], $padEndBase [$pad] ) = explode ( '@end@', $padBase[$pad], 2 );

?>