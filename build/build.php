<?php

  include '/pad/build/dirs.php';

  $padBuildLib  = include '/pad/build/_lib.php';  
  $padBuildBase = include '/pad/build/base.php';
  $padBuildPage = include '/pad/build/page.php';

  $padBase [$pad] = $padBuildLib . str_replace ( '@pad@', $padBuildPage, $padBuildBase );

  if ( $GLOBALS ['padInfo'] )
    include '/pad/events/build.php';

  $padInfoOccur = 'build'; 
  include '/pad/occurrence/start.php';

?>