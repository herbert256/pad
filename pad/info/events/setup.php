<?php
  
  $padDefault [$pad] = TRUE;
  $padDouble  [$pad] = '';
 
  if ( padTrace ) include pad . 'info/types/trace/level/start.php';
  if ( padXml   ) include pad . 'info/types/xml/level/start.php';  
  if ( padXref  ) include pad . 'info/types/xref/level/start.php';  
  if ( padXref  ) include pad . 'info/types/xref/items/tag.php';
  if ( padXweb  ) include pad . 'info/types/reference/items/tag.php';

?>