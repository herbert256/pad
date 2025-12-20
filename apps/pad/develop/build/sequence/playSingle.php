<?php

  $main = ( $type == 'even') ? 'odd' : 'even';

  $space1 = str_repeat ( ' ', strlen ( $main ) );
  $space2 = str_repeat ( ' ', strlen ( "$type$parm" ) );

  $one = "{table}\n\n"
       . "{demo}{sequence $main, $space2  rows=5}  {\$sequence} {/sequence}{/demo}\n\n"
       . "{demo}{sequence $type$parm,  $space1 rows=10} {\$sequence} {/sequence}{/demo}\n\n"
       . "{demo}{sequence $main, $type$parm, rows=5}  {\$sequence} {/sequence}{/demo}\n\n"
       . "{/table}";

  filePutFile ( "sequence/types/$type/flags/playSingle", 1 );
  filePutFile ( APP . "sequence/play/single/{$type}.pad", $one );

?>
