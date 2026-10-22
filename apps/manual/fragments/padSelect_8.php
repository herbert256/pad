<?php

  $padSelect ['places']    = [ 'db' => 'offices',   'fields' => "city, 'office' as what",
                               'where' => "country = 'Australia'", 'union' => 'customerAt' ];
  $padSelect ['customerAt'] = [ 'db' => 'customers', 'fields' => "city, 'customer' as what",
                               'where' => "country = 'Australia'" ];

?>
