<?php

  echo "{common:table}";

  echo str_pad ( "{common:demo}{sequence:loop}", 38) . "{\$sequence} {/sequence:loop} {/common:demo}";

  foreach ( types () as $type ) {

    $parm = pqParm ( $type );

        if ( $type == 'oeis'      ) $gox = "oeis=87";
    elseif ( $type == 'list'      ) $gox = "list='1;8;5;2;9;66'";
    elseif ( $type == 'range'     ) $gox = "range='1..10'";
    elseif ( $type == 'eval'      ) $gox = "eval='@ * 10 | @ - 1'";
    elseif ( $type == 'random'    ) $gox = 'random, maximal=99';
    else                            $gox = "$type$parm";

    echo str_pad ( "{common:demo}{sequence:auto $gox}", 38) . "{\$sequence} {/sequence:auto} {/common:demo}";

  }

  echo "{/common:table}";

?>
