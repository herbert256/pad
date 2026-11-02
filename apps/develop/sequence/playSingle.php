<?php

  $main = ( $type == 'even') ? 'odd' : 'even';

  $space1 = str_repeat ( ' ', strlen ( $main ) );
  $space2 = str_repeat ( ' ', strlen ( "$type$parm" ) );

  $one = "{common:table}\n\n"
       . "{common:demo}{sequence:auto $main, $space2  rows=5}  {\$sequence} {/sequence:auto}{/common:demo}\n\n"
       . "{common:demo}{sequence:auto $type$parm,  $space1 rows=10} {\$sequence} {/sequence:auto}{/common:demo}\n\n"
       . "{common:demo}{sequence:auto $main, $type$parm, rows=5}  {\$sequence} {/sequence:auto}{/common:demo}\n\n"
       . "{/common:table}";

  file_put_contents ( PT . "$type/flags/playSingle", 1, LOCK_EX );
  file_put_contents ( APPS . "sequence/play/single/{$type}.pad", "$one\n", LOCK_EX );

?>
