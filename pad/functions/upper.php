<?php

  // Pipe function upper: upper-cases the value letter by letter in UTF-8, so Zoë becomes
  // ZOË. It used strtoupper, which knows ASCII only and left every accented and other
  // multibyte letter as it was.

  return mb_strtoupper ( (string) $value, 'UTF-8' );

?>
