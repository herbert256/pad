<?php

  include PAD . 'build/dirs.php';

  $padBuildLib  = include PAD . 'build/_lib.php';
  $padBuildBase = include PAD . 'build/base.php';
  $padBuildPage = include PAD . 'build/page.php';

  $padBase [$pad] = $padBuildLib . str_replace ( '@pad@', $padBuildPage, $padBuildBase );

  if ( $padInfo )
    include PAD . 'events/build.php';

  include PAD . 'occurrence/occurrence.php';

?>
