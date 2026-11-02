<?php

  // The login flush/login asks for below its {pad:flush}; on its own the page logs in as any
  // page does.

  $done = padLogin ( [ 'id' => '7', 'name' => 'Ann' ] ) ? 'logged in' : 'refused';

?>
