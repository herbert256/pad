<?php
 
  if ( $type == 'random' or $type == 'get' or $build == 'fixed' or $build == 'order' )
    return;

  $main = ( $type == 'even') ? 'odd' : 'even';

  $one = "{table}\n\n"
       . "{demo}{seq $main, rows=5}\n  {\$seq}\n{/seq}{/demo}\n\n"
       . "{demo}{seq $main, $type$parm, rows=5}\n  {\$seq}\n{/seq}{/demo}\n\n"
       . "{/table}";

  file_put_contents ( PAD . "seq/types/$type/flags/operationSingle", 1 );
  file_put_contents ( APP . "seq/operation/single/{$type}.pad", $one );

?>