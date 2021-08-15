<?php

  $pad_exists = PAD_APP  . $value;

  return ( pad_file_exists ($pad_exists) ) ? '1' : '0';

?>