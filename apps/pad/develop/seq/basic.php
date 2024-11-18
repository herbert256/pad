<?php
 
  if     ( $type == 'get'       ) return;
  elseif ( $type == 'loop'      ) $go = "loop, rows=15";
  elseif ( $type == 'oeis'      ) $go = "oeis=87, rows=15";
  elseif ( $type == 'list'      ) $go = "list='1;8;5;2;9;66'";
  elseif ( $type == 'range'     ) $go = "range='1..10'";
  elseif ( $type == 'eval'      ) $go = "eval='@ * 10 | @ - 1', rows=15";
  elseif ( $type == 'random'    ) $go = 'random, min=100, max=199, rows=15';
  else                            $go = "$type$parm, rows=15";

  $one  = "{table}\n\n";
  $one .= "{demo}{seq $go}\n  {\$seq}\n{/seq}{/demo}\n\n";
  $one .= "{/table}";

  file_put_contents ( APP . "seq/basic/{$type}.pad", $one );

?>