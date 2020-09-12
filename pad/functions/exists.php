<?php

  $pad_exists = PAD_APP  . $value;

  return ( file_exists ($pad_exists) ) ? '1' : '0';

?>