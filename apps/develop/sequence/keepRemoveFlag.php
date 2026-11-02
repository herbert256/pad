<?php

   if     ( $type == 'range') $go = "='5..15'";
   elseif ( $type == 'list' ) $go = "='5;10;15;20'";
   elseif ( $parm           ) $go = $parm;
   else                       $go = '';

  $space = str_repeat ( ' ', strlen ( $go ) );

  $one = "{common:table}\n\n"
       . "{common:demo}{sequence:loop 25, push='mySequence'/} {/common:demo}\n\n"
       . "{common:demo}{pad:pull mySequence, make,   $type$go} {\$sequence} {/pad:pull}{/common:demo}\n\n"
       . "{common:demo}{pad:pull mySequence, keep,   $type$go} {\$sequence} {/pad:pull}{/common:demo}\n\n"
       . "{common:demo}{pad:pull mySequence, remove, $type$go} {\$sequence} {/pad:pull}{/common:demo}\n\n"
       . "{common:demo}{pad:pull mySequence, flag,   $type$go} {\$sequence} {/pad:pull}{/common:demo}\n\n"
       . "{/common:table}";

  file_put_contents ( APPS . "sequence/keepRemoveFlag/{$type}.pad", "$one\n", LOCK_EX );

?>
