<?php

  include 'inits.php';

  $pad_opts = [];

  foreach ( $pad_data_default_start as $pad_v )
    $pad_opts [] = $pad_v;

  foreach ( $pad_expl as $pad_v )
    $pad_opts [] = trim($pad_v);

  foreach ( $pad_data_default_end as $pad_v )
    $pad_opts [] = $pad_v;

  $pad_val = pad_var_opts ($pad_val, $pad_opts);

  return include 'exits.php';

?>