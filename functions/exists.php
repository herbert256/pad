<?php

  $padExists = APP  . $value;

  return ( file_exists ($padExists) ) ? '1' : '0';

?>