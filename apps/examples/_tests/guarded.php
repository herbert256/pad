<?php

  // The harvest passes over every page a _guard.php stands over - in the page's own
  // directory or above it - and takes the others.

  $guarded = [];

  foreach ( [ [ 'manual', 'guarded/secret' ], [ 'regression/sitemap', 'admin/panel' ],
              [ 'manual', 'index' ], [ 'demo', 'todo' ], [ 'regression/sitemap', 'index' ] ] as [ $app, $item ] )
    $guarded [] = [ 'page' => "$app/$item", 'answer' => examplesGuarded ( $app, $item ) ? 'passed over' : 'harvested' ];

?>
