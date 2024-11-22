<?php

  $padHome = dirname ( __FILE__ );

  if ( ! defined ( 'APP' ) ) include "$padHome/start/app.php";
  if ( ! defined ( 'DAT' ) ) include "$padHome/start/dat.php";
 
  include "$padHome/pad/pad.php";

?>