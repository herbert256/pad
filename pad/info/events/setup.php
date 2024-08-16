<?php
  
  $padDefault [$pad] = TRUE;
  $padDouble  [$pad] = '';
 
  if ( $GLOBALS ['padInfo'] ) include '/pad/info/types/trace/level/start.php';
  if ( padXml   ) include '/pad/info/types/xml/level/start.php';  
  if ( $GLOBALS ['padInfo']  ) include '/pad/info/types/xref/level/start.php';  
  if ( $GLOBALS ['padInfo']  ) include '/pad/info/events/tag.php';
  if ( $GLOBALS ['padInfo']  ) include '/pad/info/events/tag.php';

?>