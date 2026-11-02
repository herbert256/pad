<?php

  // The groups of the catalog with their examples, for the cards of the overview.

  $groups = [];

  foreach ( psGroups () as $group => $tagline ) {

    $examples = [];

    foreach ( $psCatalog [$group] as $page => [ $label, $about ] )
      $examples [] = [ 'page' => $page, 'label' => $label, 'about' => $about ];

    $groups [] = [ 'group' => $group, 'tagline' => $tagline, 'examples' => $examples ];

  }

?>
