<?php

  $main = ( $type == 'even') ? 'odd' : 'even';

  $space1 = str_repeat ( ' ', strlen ( $main ) );
  $space2 = str_repeat ( ' ', strlen ( "$type$parm" ) );

  $one = "{table}\n\n"
       . "{demo}{sequence $main, $space2  rows=5}  {\$sequence} {/sequence}{/demo}\n\n"
       . "{demo}{sequence $type$parm,  $space1 rows=10} {\$sequence} {/sequence}{/demo}\n\n"
       . "{demo}{sequence $main, $type$parm, rows=5}  {\$sequence} {/sequence}{/demo}\n\n"
       . "{/table}";

  file_put_contents ( PT . "$type/flags/playSingle", 1, LOCK_EX );
  file_put_contents ( APPS . "sequence/play/single/{$type}.pad", "$one\n", LOCK_EX );

?>
