<?php

  $pExists = APP  . $value;

  return ( file_exists ($pExists) ) ? '1' : '0';

?>