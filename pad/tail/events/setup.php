<?php
  
  $padDefault [$pad] = TRUE;
  $padDouble  [$pad] = '';
 
  if ( padTrace ) include pad . 'tail/types/trace/level/start.php';
  if ( padXml          ) include pad . 'tail/types/xml/level/start.php';  
  if ( padXref         ) include pad . 'tail/types/xref/items/tag.php';

?>