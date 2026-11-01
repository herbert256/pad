<?php

  // A kind of one of the four groups, by the name in the address - ?section/hero,
  // /sections/effect/typewriter, ?palette/viridis: one the engine has, else a page not found.
  // Its examples are shown beside the templates that make them; a theme's page lists the
  // contrast of its colours and has a button that puts every page in it, a palette's the
  // colours padPalette answers.

  $one = sectionsKind ( $group, $kind );

  if ( ! $one )
    padAbort ( 404 );

  $name     = $one ['name'];
  $title    = $name . ' ' . strtolower ( rtrim ( sectionsGroups () [$group] [0], 's' ) );
  $text     = $one ['text'];
  $wide     = in_array ( $group, [ 'section', 'palette' ] );
  $examples = sectionsExamples ( $group, $kind );

  $contrasts = ( $group == 'theme' ) ? sectionsContrasts ( $kind ) : [];
  $colors    = ( $group == 'palette' ) ? implode ( ' ', padPalette ( $kind ) ) : '';

?>
