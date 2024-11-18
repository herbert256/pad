<?php
 
   if ( $parm or $type == 'get' or $build == 'fixed' or $build == 'order' )
    return;

  if ( ! file_exists (PAD . "seq/types/$type/bool.php") and 
       ! file_exists (PAD . "seq/types/$type/generated.php") and
       ! file_exists (PAD . "seq/types/$type/fixed.php") )
    return; 
 
   if ( in_array ( $type, ['loop','not','negatation'] ) )
     return;

  $one = "{table}\n\n"
       . "{demo}{seq 10}\n  {\$seq}\n{/seq}{/demo}\n\n"
       . "{demo}{seq 10, keep, $type}\n  {\$seq}\n{/seq}{/demo}\n\n"
       . "{demo}{seq 10, remove, $type}\n  {\$seq}\n{/seq}{/demo}\n\n"
       . "{/table}";

  file_put_contents ( PAD . "seq/types/$type/flags/keepRemove", 1 );
  file_put_contents ( APP . "seq/keepRemove/{$type}.pad", $one );

?>