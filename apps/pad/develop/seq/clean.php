<?php
   
  foreach ( glob ( APP . 'seq/basic/*'            ) as $file ) unlink($file);
  foreach ( glob ( APP . 'seq/type/*'             ) as $file ) unlink($file);
  foreach ( glob ( APP . 'seq/keepRemove/*'       ) as $file ) unlink($file);
  foreach ( glob ( APP . 'seq/make/*'             ) as $file ) unlink($file);
  foreach ( glob ( APP . 'seq/one/*'              ) as $file ) unlink($file);

  foreach ( glob ( APP . 'seq/play/single/*' ) as $file ) unlink($file);
  foreach ( glob ( APP . 'seq/play/double/*' ) as $file ) unlink($file);

  foreach ( glob ( APP . 'seq/store/plays/*' ) as $file ) unlink($file);
 
  foreach ( glob ( 'seq/actions/double/*'   ) as $file ) unlink($file);
  foreach ( glob ( 'seq/actions/single/*'   ) as $file ) unlink($file);

  foreach ( glob ( 'seq/store/actions/*'    ) as $file ) unlink($file);
  foreach ( glob ( 'seq/store/plays/*' ) as $file ) unlink($file);

?>