<?php

  // The landing page: built from the sections and the effects it shows, the theme switcher in
  // the bar above it. The numbers are the kinds the engine has (_lib/sections.php) - of the
  // sections those that are a block of their own, not the item of the sections that list.

  $title = 'Sections, effects, themes and palettes';

  $sectionCount = count ( array_filter ( sectionsKinds ( 'section' ), fn ( $one ) => $one ['kind'] != 'item' ) );
  $effectCount  = count ( sectionsKinds ( 'effect' ) );
  $themeCount   = count ( sectionsKinds ( 'theme' ) );
  $paletteCount = count ( sectionsKinds ( 'palette' ) );

  $spread = [ [ 'part' => 'Sections', 'kinds' => $sectionCount ], [ 'part' => 'Effects',  'kinds' => $effectCount  ],
              [ 'part' => 'Themes',   'kinds' => $themeCount   ], [ 'part' => 'Palettes', 'kinds' => $paletteCount ] ];

?>
