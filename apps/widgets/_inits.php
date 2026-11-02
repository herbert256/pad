<?php

  // The menu of the frame every PAD application shares (_common/_tags/padFrame.pad): the landing
  // page, then a page for each group - sections, effects, themes, palettes - with every one of
  // its kinds under it, as the engine has them (_lib/sections.php). The theme switcher stands in
  // the frame's bar, as in every application of the frame, so the choice is at hand on every page.

  $frameMenu = [ [ 'link' => '?index', 'label' => 'Landing page', 'icon' => 'home', 'current' => $padPage == 'index' ] ];

  foreach ( sectionsGroups () as $sectionsGroup => [ $sectionsHead, $sectionsDir, , $sectionsIcon ] ) {

    $sectionsPage = [ 'section' => 'sections', 'effect' => 'effects', 'theme' => 'themes', 'palette' => 'palettes' ] [$sectionsGroup];
    $frameMenu [] = [ 'head' => $sectionsHead ];
    $frameMenu [] = [ 'link' => "?$sectionsPage", 'label' => "All " . strtolower ( $sectionsHead ), 'icon' => $sectionsIcon, 'current' => $padPage == $sectionsPage ];

    foreach ( sectionsKinds ( $sectionsGroup ) as $sectionsOne )
      $frameMenu [] = [ 'link' => $sectionsOne ['link'], 'label' => $sectionsOne ['name'], 'icon' => '',
                        'current' => ( $padPage == '[group]/[kind]' and ( $group ?? '' ) === $sectionsGroup and ( $kind ?? '' ) === $sectionsOne ['kind'] ) ];

  }

  include_once COMMON . '_lib/showcaseExamples.php';
  $showcaseFamilies = showcaseExampleFamilies ();
  $frameMenu = array_merge ( $frameMenu, showcaseExampleMenu ( $showcaseFamilies, $padPage ) );

?>
