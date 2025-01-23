<?php

  $padBase [$pad] = $padStartBase [$pad];
  $padData [$pad] = $padStartData [$pad];

  $padStartBase [$pad] = '';
  $padStartData [$pad] = [];

  reset ( $padData [$pad] );
  
  include 'occurrence/occurence.php';

?>