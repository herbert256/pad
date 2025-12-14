<?php

  if ( $padInfo )
    include PAD . 'events/start.php';

  list ( $padBase [$pad], $padStartBase [$pad] ) = explode ( '@start@', $padBase [$pad], 2 );

  $padStartData [$pad] = $padData [$pad];
  $padData [$pad]      = padDefaultData ();

  reset ( $padData [$pad] );

  include PAD . 'occurrence/occurrence.php';

?>