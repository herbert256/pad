<?php

  include pad . 'build/dirs.php';

  $padBuildLib  = include pad . 'build/lib.php';  
  $padBuildBase = include pad . 'build/base.php';
  $padBuildPage = include pad . 'build/page.php';

  $padBase [$pad] = $padBuildLib . str_replace ( '@pad@', $padBuildPage, $padBuildBase );

  if ( padXml )
    include pad . 'tail/types/xml/build.php';

  if ( padTrace )
    include pad . 'tail/events/build.php';
 
  $padOccurTypeSet = 'build';  
  include pad . 'occurrence/start.php';

?>