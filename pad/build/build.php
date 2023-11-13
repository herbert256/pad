<?php

  include pad . 'build/dirs.php';

  $padBuildLib  = include pad . 'build/lib.php';  
  $padBuildBase = include pad . 'build/base.php';
  $padBuildPage = include pad . 'build/page.php';

  $padBase [$pad] = $padBuildLib . str_replace ( '@pad@', $padBuildPage, $padBuildBase );

  if ( $padTraceActive ) {
    include pad . 'trace/items/build.php';
    $padOccur [$pad-1] = 1;
    include pad . 'trace/level/start.php';
    include pad . 'trace/level/info.php';  
  }

  $padOccurTypeSet = 'build';  
  include pad . 'occurrence/start.php';

?>