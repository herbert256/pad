<?php

  if ( strpos($pad_parm, 'page=') !== FALSE) {
    $pad_include_call = pad_include ($pad_parm);
    return $pad_include_call ['data'];
  }

  $pad_one = PAD_APP . "includes/$pad_parm";

  return include PAD_HOME . 'build/one.php';

?>