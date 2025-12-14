<?php

  if ( $padInfo )
    include PAD . 'events/end.php';

  list ( $padBase [$pad], $padEndBase [$pad] ) = explode ( '@end@', $padBase[$pad], 2 );

?>