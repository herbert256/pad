<?php

  $base    = 'hoi';
  $md5     = md5($base);
  $xmd5    = pad_md5($base);
  $unpack  = pad_md5_unpack ($xmd5);

  dump();
  
?>