<?php
 
  echo "{table}";

  echo str_pad ( "{demo}{sequence}", 38) . "{\$sequence} {/sequence} {/demo}";

  foreach ( pqTypes () as $type ) {

    include APP . 'sequence/develop/parm.php';

        if ( $type == 'oeis'      ) $gox = "oeis=87";
    elseif ( $type == 'list'      ) $gox = "list='1;8;5;2;9;66'";
    elseif ( $type == 'range'     ) $gox = "range='1..10'";
    elseif ( $type == 'eval'      ) $gox = "eval='@ * 10 | @ - 1'";
    elseif ( $type == 'random'    ) $gox = 'random, maximal=99';
    else                            $gox = "$type$parm";

    echo str_pad ( "{demo}{sequence $gox}", 38) . "{\$sequence} {/sequence} {/demo}";

  }
  
  echo "{/table}";

?>