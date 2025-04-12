<?php
 
   if ( $parm or $build == 'fixed' or $build == 'order' )
    return;

  if ( ! file_exists ("sequence/types/$type/bool.php") and 
       ! file_exists ("sequence/types/$type/generated.php") and
       ! file_exists ("sequence/types/$type/fixed.php") )
    return; 
 
   if ( in_array ( $type, ['loop','not','negatation'] ) )
     return;

  $space = str_repeat ( ' ', strlen ( $type ) );

  $one = "{table}\n\n"
       . "{demo}{sequence 25, push='mySequence'} {/demo}\n\n"
       . "{demo}{pull mySequence          $space} {\$sequence} {/pull}{/demo}\n\n"
       . "{demo}{pull mySequence, keep,   $type} {\$sequence} {/pull}{/demo}\n\n"
       . "{demo}{pull mySequence, remove, $type} {\$sequence} {/pull}{/demo}\n\n"
       . "{demo}{pull mySequence, flag,   $type} {\$sequence} {/pull}{/demo}\n\n"
       . "{/table}";

  file_put_contents ( "sequence/types/$type/flags/keepRemoveFlag", 1 );
  file_put_contents ( APP . "sequence/keepRemoveFlag/{$type}.pad", $one );

?>