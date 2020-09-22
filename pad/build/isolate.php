<?php

  $pad_build = include PAD_HOME . 'build/demand.php';

  $pad_build = str_replace('{build',  ' {isolate}{build',    $pad_build);
  $pad_build = str_replace('{/build}', '{/build}{/isolate}', $pad_build);

  return $pad_build;

?>