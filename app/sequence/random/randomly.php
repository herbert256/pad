<?php
 
  foreach ( pqTypes () as $type ) {

    include APP . 'sequence/develop/parm.php';

        if ( $type == 'oeis'      ) $gox = "oeis=87";
    elseif ( $type == 'list'      ) $gox = "list='1;8;5;2;9;66'";
    elseif ( $type == 'range'     ) $gox = "range='1..10'";
    elseif ( $type == 'eval'      ) continue;
    elseif ( $type == 'golomb'    ) continue;
    elseif ( $type == 'random'    ) $gox = 'random, maximal=99';
    else                            $gox = "$type$parm";

    $sequences [$type] ['gox']    = $gox;
    $sequences [$type] ['spaces'] = str_pad ( '', 19 - strlen ( $gox ) );

  }
  
?>