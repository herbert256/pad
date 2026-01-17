<?php

  include PAD . 'start/pad/start.php';
  include PAD . 'inits/level.php';
  include PAD . "start/pad/$padStrBld.php";
  include PAD . 'start/pad/level.php';
  include PAD . 'start/pad/end.php';

  return $padOut [$pad+1] ;

?>