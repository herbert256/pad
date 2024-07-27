<?php

  $padPagePage    = $padParm;
  $padPageInclude = padTagParm ( 'include' );
  $padPageType    = padTagParm ( 'type', 'get' );

  return include pad . "pad/page/_lib/page.php";

?>