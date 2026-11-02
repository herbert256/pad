<?php

  if ( ! $parm )
    return;

  $one = "{common:table}\n\n"
       . "{common:demo}{sequence:range '11..14', push='one'/}{/common:demo}\n\n"
       . "{common:demo}{sequence:range '5..2',   push='two'/}{/common:demo}\n\n"
       . "{common:demo}{sequence:auto one}\n  {\$sequence}\n{/sequence:auto}{/common:demo}\n\n"
       . "{common:demo}{sequence:auto two}\n  {\$sequence}\n{/sequence:auto}{/common:demo}\n\n"
       . "{common:demo}{sequence:auto one, $type='two'}\n  {\$sequence}\n{/sequence:auto}{/common:demo}\n\n"
       . "{/common:table}";

  file_put_contents ( PT . "$type/flags/playDouble", 1, LOCK_EX );
  file_put_contents ( APPS . "sequence/play/double/{$type}.pad", "$one\n", LOCK_EX );

?>
