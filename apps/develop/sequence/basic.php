<?php

  if     ( $type == 'oeis'   ) $go = "oeis=87";
  elseif ( $type == 'list'   ) $go = "list='1;8;5;2;9;66'";
  elseif ( $type == 'range'  ) $go = "range='1..10'";
  elseif ( $type == 'eval'   ) $go = "eval='@ * 10 | @ - 1'";
  elseif ( $type == 'random' ) $go = 'random, minimal=100, maximal=199';
  else                         $go = "$type$parm";

  $one  = "{common:table}\n\n";
  $one .= "{common:demo}{sequence:auto $go}\n  {\$sequence}\n{/sequence:auto}{/common:demo}\n\n";
  $one .= "{/common:table}";

  file_put_contents ( APPS . "sequence/basic/{$type}.pad", "$one\n", LOCK_EX );

?>
