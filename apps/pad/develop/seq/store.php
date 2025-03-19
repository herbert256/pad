<?php
  
  if ( $parm  or $type == 'random' or $type == 'get' or $build == 'fixed' or $build == 'order' )
    return;

  $seq = ucfirst( $type );

  $one = "{table}\n\n"
       . "{demo}{sequence '5..2',   name='one'}{/demo}\n\n"
       . "{demo}{sequence '11..14', name='two'}{/demo}\n\n"
       . "{demo}{sequence one}\n  {\$sequence}\n{/sequence}{/demo}\n\n"
       . "{demo}{sequence two}\n  {\$sequence}\n{/sequence}{/demo}\n\n"
       . "{demo}{sequence one, store$seq='two'}{/demo}\n\n"
       . "{demo}{sequence two}\n  {\$sequence}\n{/sequence}{/demo}\n\n"
       . "{/table}";    

  file_put_contents ( APP . "seq/store/plays/$type.pad", $one );
  file_put_contents ( "seq/types/$type/flags/storeDouble", 1 );
  file_put_contents ( "seq/store/plays/$type",  1 );
  
?>