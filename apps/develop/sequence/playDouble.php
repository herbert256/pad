<?php

  if ( ! $parm )
    return;

  $one = "{table}\n\n"
       . "{demo}{sequence '11..14', push='one'}{/demo}\n\n"
       . "{demo}{sequence '5..2',   push='two'}{/demo}\n\n"
       . "{demo}{sequence one}\n  {\$sequence}\n{/sequence}{/demo}\n\n"
       . "{demo}{sequence two}\n  {\$sequence}\n{/sequence}{/demo}\n\n"
       . "{demo}{sequence one, $type='two'}\n  {\$sequence}\n{/sequence}{/demo}\n\n"
       . "{/table}";

  file_put_contents ( PT . "$type/flags/playDouble", 1, LOCK_EX );
  file_put_contents ( APPS . "sequence/play/double/{$type}.pad", "$one\n", LOCK_EX );

?>
