<?php
 
  if ( $type == 'random' or $type == 'get' or $build == 'fixed' or $build == 'order' )
    return;

  if ( ! $parm ) 
    return;

  $one = "{table}\n\n"
       . "{demo}{sequence '11..14', set='one'}{/demo}\n\n"
       . "{demo}{sequence '5..2',   set='two'}{/demo}\n\n"
       . "{demo}{sequence one}\n  {\$sequence}\n{/sequence}{/demo}\n\n"
       . "{demo}{sequence two}\n  {\$sequence}\n{/sequence}{/demo}\n\n"
       . "{demo}{sequence one, $type='two'}\n  {\$sequence}\n{/sequence}{/demo}\n\n"
       . "{/table}";    

  file_put_contents ( "sequence/types/$type/flags/playDouble", 1 );
  file_put_contents ( APP . "sequence/play/double/{$type}.pad", $one );

?>