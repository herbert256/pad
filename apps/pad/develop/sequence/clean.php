<?php
   
  foreach ( glob ( APP . 'sequence/basic/*'            ) as $file ) unlink($file);
  foreach ( glob ( APP . 'sequence/type/*'             ) as $file ) unlink($file);
  foreach ( glob ( APP . 'sequence/keepRemove/*'       ) as $file ) unlink($file);
  foreach ( glob ( APP . 'sequence/make/*'             ) as $file ) unlink($file);

  foreach ( glob ( APP . 'sequence/play/single/*' ) as $file ) unlink($file);
  foreach ( glob ( APP . 'sequence/play/double/*' ) as $file ) unlink($file);

  foreach ( glob ( APP . 'sequence/store/plays/*' ) as $file ) unlink($file);
 
  foreach ( glob ( 'sequence/actions/double/*'   ) as $file ) unlink($file);
  foreach ( glob ( 'sequence/actions/single/*'   ) as $file ) unlink($file);

  foreach ( glob ( 'sequence/store/actions/*'    ) as $file ) unlink($file);
  foreach ( glob ( 'sequence/store/plays/*' ) as $file ) unlink($file);

?>