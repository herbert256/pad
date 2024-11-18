<?php
   
  foreach ( glob ( APP . 'seq/basic/*'            ) as $file ) unlink($file);
  foreach ( glob ( APP . 'seq/keepRemove/*'       ) as $file ) unlink($file);
  foreach ( glob ( APP . 'seq/make/*'             ) as $file ) unlink($file);
  foreach ( glob ( APP . 'seq/one/*'              ) as $file ) unlink($file);

  foreach ( glob ( APP . 'seq/operation/single/*' ) as $file ) unlink($file);
  foreach ( glob ( APP . 'seq/operation/double/*' ) as $file ) unlink($file);

  foreach ( glob ( APP . 'seq/store/operations/*' ) as $file ) unlink($file);
 
  foreach ( glob ( PAD . 'seq/actions/double/*'   ) as $file ) unlink($file);
  foreach ( glob ( PAD . 'seq/actions/single/*'   ) as $file ) unlink($file);

  foreach ( glob ( PAD . 'seq/store/actions/*'    ) as $file ) unlink($file);
  foreach ( glob ( PAD . 'seq/store/operations/*' ) as $file ) unlink($file);

?>