<?php

  // Which module of an application imports which, and how many functions it uses.

  $imports = [
    'app'      => [ 'router' => 6, 'auth' => 4, 'views' => 9 ],
    'router'   => [ 'request' => 5, 'views' => 3 ],
    'auth'     => [ 'session' => 7, 'db' => 4, 'crypt' => 3 ],
    'views'    => [ 'template' => 12, 'i18n' => 2 ],
    'template' => [ 'cache' => 3, 'i18n' => 1 ],
    'session'  => [ 'cache' => 2, 'crypt' => 1 ],
    'db'       => [ 'cache' => 4, 'log' => 2 ],
    'request'  => [ 'log' => 1 ]
  ];

  $modules = [];

  foreach ( $imports as $module => $uses )
    foreach ( $uses as $used => $functions )
      $modules [] = [ 'module' => $module, 'imports' => $used, 'functions' => $functions ];

?>
