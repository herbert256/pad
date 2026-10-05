<?php

  // Pipe function lower: lower-cases the value letter by letter in UTF-8, so ÉCOLE becomes
  // école. It used strtolower, which knows ASCII only.

  return mb_strtolower ( (string) $value, 'UTF-8' );

?>
