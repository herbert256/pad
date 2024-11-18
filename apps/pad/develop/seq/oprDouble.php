<?php
 
  if ( $type == 'random' or $type == 'get' or $build == 'fixed' or $build == 'order' )
    return;

  if ( ! $parm ) 
    return;

  $one = "{table}\n\n"
       . "{demo}{seq '11..14', set='one'}{/demo}\n\n"
       . "{demo}{seq '5..2',   set='two'}{/demo}\n\n"
       . "{demo}{seq one}\n  {\$seq}\n{/seq}{/demo}\n\n"
       . "{demo}{seq two}\n  {\$seq}\n{/seq}{/demo}\n\n"
       . "{demo}{seq one, $type='two'}\n  {\$seq}\n{/seq}{/demo}\n\n"
       . "{/table}";    

  file_put_contents ( PAD . "seq/types/$type/flags/operationDouble", 1 );
  file_put_contents ( APP . "seq/operation/double/{$type}.pad", $one );

?>