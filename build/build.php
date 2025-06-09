<?php

  include 'build/dirs.php';

  $padBuildLib  = include 'build/_lib.php';  
  $padBuildBase = include 'build/base.php';
  $padBuildPage = include 'build/page.php';

  $padBase [$pad] = $padBuildLib . str_replace ( '@pad@', $padBuildPage, $padBuildBase );

  if ( $padInfo )
    include 'events/build.php';

  include 'occurrence/occurrence.php';

?>