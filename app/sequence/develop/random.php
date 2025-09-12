<?php
 
     if ( $type == 'oeis'      ) $gox = "oeis=87";
 elseif ( $type == 'list'      ) $gox = "list='1;8;5;2;9;66'";
 elseif ( $type == 'range'     ) $gox = "range='1..10'";
 elseif ( $type == 'eval'      ) return;
 elseif ( $type == 'random'    ) $gox = 'random, maximal=99';
 else                            $gox = "$type$parm";

  $space = str_repeat ( ' ', strlen ( $go ) );

  $one = "{table}\n\n"
       . "{demo}{sequence $gox, rows=10, random} {\$sequence} {/sequence} {/demo}\n\n"
       . "{demo}{sequence $gox, rows=10, randomly} {\$sequence} {/sequence} {/demo}\n\n"
       . "{/table}";

  unlink ( APP . "sequence/random/{$type}.pad");

?>