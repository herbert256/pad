<?php

  include pad . 'build/dirs.php';

  $padBuildLib  = include pad . 'build/lib.php';  
  $padBuildBase = include pad . 'build/base.php';
  $padBuildPage = include pad . 'build/page.php';

  $padBase [$pad] = $padBuildLib . str_replace ( '@pad@', $padBuildPage, $padBuildBase );

  if ( $padTraceActive )
    include pad . 'trace/lines/build.php';

  include pad . 'occurrence/start.php';

?>