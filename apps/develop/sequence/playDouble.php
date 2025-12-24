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

  filePutFile ( "sequence/types/$type/flags/playDouble", 1 );
  filePutFile ( APP . "sequence/play/double/{$type}.pad", $one );

?>