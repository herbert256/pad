<?php

  $demo  = $pad_parms_seq [0] ?? '';
  $title = $pad_parms_seq [1] ?? '';
  $intro = $pad_parms_seq [2] ?? '';

  $page_dir_pos = strrpos( $page, '/');
  $demo_dir_pos = strrpos( $demo, '/');

  $page_dir = substr($page, 0, $page_dir_pos);
  $demo_dir = ($demo_dir_pos) ? substr($demo, 0, $demo_dir_pos) : 0;

  if (!$demo_dir)
    $demo = "$page_dir/$demo";

  if ( ! $title )
    $title = substr($demo, strrpos( $demo, '/')+1);

  $file = PAD_APP . 'pages/' . $demo;

  $php  = ( file_exists("$file.php" ) ) ? pad_colors_file ("$file.php")  : '';
  $html = ( file_exists("$file.html") ) ? pad_colors_file ("$file.html") : '';

  $php_file  = ($php)  ? str_replace(PAD_APPS, '', $file) . '.php'  : '';
  $html_file = ($html) ? str_replace(PAD_APPS, '', $file) . '.html' : '';

  return TRUE;
  
?>