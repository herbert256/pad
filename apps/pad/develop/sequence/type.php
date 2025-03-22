<?php
 
  if     ( $type == 'get'       ) return;
  elseif ( $type == 'loop'      ) $go = "rows=15";
  elseif ( $type == 'oeis'      ) $go = "87, rows=15";
  elseif ( $type == 'list'      ) $go = "'1;8;5;2;9;66'";
  elseif ( $type == 'range'     ) $go = "'1..10'";
  elseif ( $type == 'eval'      ) $go = "'@ * 10 | @ - 1', rows=15";
  elseif ( $type == 'random'    ) $go = 'min=100, max=199, rows=15';
  elseif ( $parm )                $go = "4, rows=15";
  else                            $go = "rows=15";

  $prefix = ( padTypeGet ( $type ) == 'seq' ) ? '' : 'seq:';

  $one  = "{table}\n\n";
  $one .= "{demo}{" . "$prefix$type $go" . "}\n  {\$" . $type . "}\n{/" . $prefix . $type . "}{/demo}\n\n";
  $one .= "{/table}";

  file_put_contents ( APP . "sequence/type/{$type}.pad", $one );

?>