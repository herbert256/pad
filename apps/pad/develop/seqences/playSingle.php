<?php
 
  if ( $type == 'random' or $type == 'get' or $build == 'fixed' or $build == 'order' )
    return;

  $main = ( $type == 'even') ? 'odd' : 'even';

  $one = "{table}\n\n"
       . "{demo}{sequence $main, rows=5}\n  {\$sequence}\n{/sequence}{/demo}\n\n"
       . "{demo}{sequence $main, $type$parm, rows=5}\n  {\$sequence}\n{/sequence}{/demo}\n\n"
       . "{/table}";

  file_put_contents ( "sequence/types/$type/flags/playSingle", 1 );
  file_put_contents ( APP . "sequence/play/single/{$type}.pad", $one );

?>