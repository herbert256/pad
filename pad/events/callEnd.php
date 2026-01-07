<?php

  global $padAppTime;

  if ( $padCall [0] == '/' )
    $padAppTime += hrtime ( TRUE ) - $padCallStart;

 ?>