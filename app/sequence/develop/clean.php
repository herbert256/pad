<?php
   
  foreach ( glob ( APP . 'sequence/basic/*'            ) as $file ) unlink($file);
  foreach ( glob ( APP . 'sequence/keepRemoveFlag/*'   ) as $file ) unlink($file);

  foreach ( glob ( APP . 'sequence/play/single/*' ) as $file ) unlink($file);
  foreach ( glob ( APP . 'sequence/play/double/*' ) as $file ) unlink($file);
 
?>