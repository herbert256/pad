<?php

  // The menu of the frame every PAD application shares (_common/_tags/padFrame.pad): the landing
  // page, then a page for each group - sections, effects, themes, palettes - with every one of
  // its kinds under it, as the engine has them (_lib/sections.php). The theme switcher stands in
  // the frame's bar ({themes/}, the 'tools' slot), so the choice is at hand on every page.

  $frameMenu = [ [ 'link' => '?index', 'label' => 'Landing page', 'icon' => 'home', 'current' => $padPage == 'index' ] ];

  foreach ( sectionsGroups () as $sectionsGroup => [ $sectionsHead, $sectionsDir, , $sectionsIcon ] ) {

    $frameMenu [] = [ 'head' => $sectionsHead ];
    $frameMenu [] = [ 'link' => "?$sectionsDir", 'label' => "All $sectionsDir", 'icon' => $sectionsIcon, 'current' => $padPage == $sectionsDir ];

    foreach ( sectionsKinds ( $sectionsGroup ) as $sectionsOne )
      $frameMenu [] = [ 'link' => $sectionsOne ['link'], 'label' => $sectionsOne ['name'], 'icon' => '',
                        'current' => ( $padPage == '[group]/[kind]' and ( $group ?? '' ) === $sectionsGroup and ( $kind ?? '' ) === $sectionsOne ['kind'] ) ];

  }

?>
