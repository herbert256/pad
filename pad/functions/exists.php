<?php

  $pad_exists = APP  . $value;

  return ( file_exists ($pad_exists) ) ? '1' : '0';

?>