<?php

  // The login flush/login asks for below its {flush}; on its own the page logs in as any
  // page does.

  $done = padLogin ( [ 'id' => '7', 'name' => 'Ann' ] ) ? 'logged in' : 'refused';

?>
