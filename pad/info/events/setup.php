<?php
  
  $padDefault [$pad] = TRUE;
  $padDouble  [$pad] = '';
 
  if ( padTrace ) include pad . 'info/types/trace/level/start.php';
  if ( padXml   ) include pad . 'info/types/xml/level/start.php';  
  if ( padXref  ) include pad . 'info/types/xref/level/start.php';  
  if ( padXref  ) include pad . 'info/types/xref/events/tag.php';
  if ( padXapp  ) include pad . 'info/types/xapp/events/tag.php';

?>