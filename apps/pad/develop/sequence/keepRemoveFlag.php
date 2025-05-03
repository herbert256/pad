<?php
 
   if ( $type == 'range' or $type == 'list' )
     return;

   if ( $parm )
     $go = "$type$parm";
   else
     $go = "$type";

  $space = str_repeat ( ' ', strlen ( $go ) );

  $one = "{table}\n\n"
       . "{demo}{sequence 25, push='mySequence'} {/demo}\n\n"
       . "{demo}{pull mySequence, make,   $go} {\$sequence} {/pull}{/demo}\n\n"
       . "{demo}{pull mySequence, keep,   $go} {\$sequence} {/pull}{/demo}\n\n"
       . "{demo}{pull mySequence, remove, $go} {\$sequence} {/pull}{/demo}\n\n"
       . "{demo}{pull mySequence, flag,   $go} {\$sequence} {/pull}{/demo}\n\n"
       . "{/table}";

  file_put_contents ( "sequence/types/$type/flags/keepRemoveFlag", 1 );
  file_put_contents ( APP . "sequence/keepRemoveFlag/{$type}.pad", $one );

?>