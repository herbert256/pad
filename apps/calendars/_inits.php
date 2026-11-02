<?php

  $calendarViews = array_values ( calendarsCatalog () );
  $frameMenu = [ [ 'link' => '?index', 'label' => 'All calendars', 'icon' => 'calendar', 'current' => $padPage == 'index' ],
                 [ 'head' => 'Views' ] ];
  foreach ( $calendarViews as $view )
    $frameMenu [] = [ 'link' => '?' . $view ['kind'], 'label' => $view ['name'], 'icon' => 'calendar',
                     'current' => $padPage == '[kind]' && ( $kind ?? '' ) == $view ['kind'] ];

  include_once COMMON . '_lib/showcaseExamples.php';
  $showcaseFamilies = showcaseExampleFamilies ();
  $frameMenu = array_merge ( $frameMenu, showcaseExampleMenu ( $showcaseFamilies, $padPage ) );

?>
