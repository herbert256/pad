<?php

  $pad_build_mode  = $_REQUEST['mode']  ?? 'demand';
  $pad_build_merge = $_REQUEST['merge'] ?? 'content';
  
  if ($page == 'index')
    $pad_build_mode = 'include';

?>