<?php

  $padPagePage    = $padParm;
  $padPageInclude = padTagParm ( 'include' );
  $padPageType    = padTagParm ( 'type', 'get' );

  return include pad . "start/page/_lib/page.php";

?>