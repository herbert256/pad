<?php
 
  echo "{table}";

  foreach ( pqTypes () as $type ) {

    include APP . 'develop/sequence/parm.php';

        if ( $type == 'oeis'      ) $gox = "oeis=87";
    elseif ( $type == 'list'      ) $gox = "list='1;8;5;2;9;66'";
    elseif ( $type == 'range'     ) $gox = "range='1..10'";
    elseif ( $type == 'eval'      ) continue;
    elseif ( $type == 'random'    ) $gox = 'random, maximal=99';
    else                            $gox = "$type$parm";

    echo str_pad ( "{demo}{sequence $gox, rows=25, to=5000, randomly}", 38) . "{\$sequence} {/sequence} {/demo}";

  }
  
  echo "{/table}";

?>