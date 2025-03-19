<?php
 
   if ( $parm or $type == 'get' or $build == 'fixed' or $build == 'order' )
    return;

  if ( ! file_exists ("seq/types/$type/bool.php") and 
       ! file_exists ("seq/types/$type/generated.php") and
       ! file_exists ("seq/types/$type/fixed.php") )
    return; 
 
   if ( in_array ( $type, ['loop','not','negatation'] ) )
     return;

  $one = "{table}\n\n"
       . "{demo}{sequence 10}\n  {\$sequence}\n{/sequence}{/demo}\n\n"
       . "{demo}{sequence 10, keep, $type}\n  {\$sequence}\n{/sequence}{/demo}\n\n"
       . "{demo}{sequence 10, remove, $type}\n  {\$sequence}\n{/sequence}{/demo}\n\n"
       . "{/table}";

  file_put_contents ( "seq/types/$type/flags/keepRemove", 1 );
  file_put_contents ( APP . "seq/keepRemove/{$type}.pad", $one );

?>