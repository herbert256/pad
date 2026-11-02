<?php

  // The menu of the frame every PAD application shares (_common/_tags/padFrame.pad): the home
  // page and a page per notation - the notations the engine has, pad/subs/notations/types/. A
  // notation's page is its own file here, <kind>.pad, its introduction written out, with the
  // examples of _examples/<kind>/ under it; [kind].pad shows a notation that has no page yet.
  //
  // The catalog is a list, not keyed by the notations' names: a page's array with a key math
  // made a bare {notation:math} in the template that array's row instead of the notation.

  $notations = array_values ( notationsCatalog () );

  $frameMenu = [ [ 'link' => '?index', 'label' => 'All notations', 'icon' => 'grid', 'current' => $padPage == 'index' ],
                 [ 'head' => 'Notations' ] ];

  foreach ( $notations as $one )
    $frameMenu [] = [ 'link' => '?' . $one ['kind'], 'label' => $one ['name'], 'icon' => 'play',
                      'current' => ( $padPage == $one ['kind'] or ( $padPage == '[kind]' and ( $kind ?? '' ) == $one ['kind'] ) ) ];

  // The page's notation and its examples, shown under the page by _include/examples.pad.

  $notationKind     = isset ( notationsCatalog () [$padPage] ) ? $padPage : '';
  $notationExamples = ( $notationKind !== '' ) ? notationsExamples ( $notationKind ) : [];
  $notationText     = ( $notationKind !== '' ) ? notationsCatalog () [$notationKind] ['text'] : '';
  $notationTag      = ( $notationKind !== '' ) ? notationsCatalog () [$notationKind] ['tag'] : '';

?>
