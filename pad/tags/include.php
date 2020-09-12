<?php

  $pad_include_php  = PAD_APP . "includes/$pad_parm.php";
  $pad_include_html = PAD_APP . "includes/$pad_parm.html";

  $pad_add = '';

  if ($pad_mode == 'isolate')
    $pad_add .= "{isolate}";

  $pad_add .= "{build 'call' | '$pad_include_php'}";
  $pad_add .= pad_get_html ($pad_include_html);
  $pad_add .= "{/build}";

  if ( $pad_mode == 'isolate' )
    $pad_add .= "{/isolate}";
          
  return $pad_add;

?>