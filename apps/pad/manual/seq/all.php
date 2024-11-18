<?php
 
  include_once APP . 'develop/_lib/lib.php';

  echo "{table}";

  foreach ( padSeqTypes () as $type ) {

    include APP . 'develop/seq/parm.php';

    if     ( $type == 'get'       ) continue;
    elseif ( $type == 'oeis'      ) $gox = "oeis=87, rows=15";
    elseif ( $type == 'list'      ) $gox = "list='1;8;5;2;9;66'";
    elseif ( $type == 'range'     ) $gox = "range='1..15'";
    elseif ( $type == 'eval'      ) $gox = "eval='@ * 10 | @ - 1', rows=15";
    elseif ( $type == 'random'    ) $gox = 'random, min=100, max=199, rows=15';
    elseif ( $type == 'sylvester' ) $gox = 'sylvester, rows=10';
    else                            $gox = "$type$parm, rows=15";

    echo "{demo}{seq $gox}\n  {\$seq}\n{/seq}{/demo}";

  }
  
  echo "{/table}";

?>