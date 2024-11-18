<?php
  
  if ( $parm  or $type == 'random' or $type == 'get' or $build == 'fixed' or $build == 'order' )
    return;

  $seq = ucfirst( $type );

  $one = "{table}\n\n"
       . "{demo}{seq '5..2',   name='one'}{/demo}\n\n"
       . "{demo}{seq '11..14', name='two'}{/demo}\n\n"
       . "{demo}{seq one}\n  {\$seq}\n{/seq}{/demo}\n\n"
       . "{demo}{seq two}\n  {\$seq}\n{/seq}{/demo}\n\n"
       . "{demo}{seq one, store$seq='two'}{/demo}\n\n"
       . "{demo}{seq two}\n  {\$seq}\n{/seq}{/demo}\n\n"
       . "{/table}";    

  file_put_contents ( APP . "seq/store/operations/$type.pad", $one );
  file_put_contents ( PAD . "seq/types/$type/flags/storeDouble", 1 );
  file_put_contents ( PAD . "seq/store/operations/$type",  1 );
  
?>