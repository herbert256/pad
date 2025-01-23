<?php

  if ( $padInfo )  
    include 'events/end.php';
  
  list ( $padBase [$pad], $padEndBase [$pad] ) = explode ( '@end@', $padBase[$pad], 2 );

?>