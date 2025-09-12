<?php
 
   if     ( $type == 'range') $go = "='5..15'";
   elseif ( $type == 'list' ) $go = "='5;10;15;20'";
   elseif ( $parm           ) $go = $parm;
   else                       $go = '';

  $space = str_repeat ( ' ', strlen ( $go ) );

  $one = "{table}\n\n"
       . "{demo}{sequence 25, push='mySequence'} {/demo}\n\n"
       . "{demo}{pull mySequence, make,   $type$go} {\$sequence} {/pull}{/demo}\n\n"
       . "{demo}{pull mySequence, keep,   $type$go} {\$sequence} {/pull}{/demo}\n\n"
       . "{demo}{pull mySequence, remove, $type$go} {\$sequence} {/pull}{/demo}\n\n"
       . "{demo}{pull mySequence, flag,   $type$go} {\$sequence} {/pull}{/demo}\n\n"
       . "{/table}";

  file_put_contents ( APP . "sequence/keepRemoveFlag/{$type}.pad", $one );

?>