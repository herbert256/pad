<?php

  // The options a declared select table takes on the tag itself: each section of the .pad
  // half names one, over the four-row staff table of the demo database.

  $padSelect ['staffSel'] = [ 'db' => 'staff', 'key' => 'name' ];
  $padSelect ['elsewhere'] = [ 'key' => 'name' ];
  $padSelect ['staffJim'] = [ 'db' => 'staff', 'where' => "name = 'jim'" ];
  $padSelect ['staffRaw'] = [ 'db' => 'staff' ];

?>