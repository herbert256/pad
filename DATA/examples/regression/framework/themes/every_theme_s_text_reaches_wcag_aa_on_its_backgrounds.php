<?php

  // Every theme of pad/subs/themes/types/ with its scheme, the lowest contrast of its text on
  // its backgrounds - the pairs padThemePairs names: the text, the soft and dim greys and the
  // links on the page and on a panel, a button's text on the accent, the side's texts on the
  // side and the menu, the status colours on theirs - the pair it is, and what that reaches:
  // AA is 4.5 to 1, AAA 7 to 1, which a theme that declares 'needs' => 7 - contrast,
  // contrast-dark - has to reach (padThemeNeeds).

  include_once PAD . 'subs/themes/_lib/theme.php';

  $sets = [];

  foreach ( padThemeTypes () as $name ) {

    $ratios = padThemeContrasts ( $name );
    $lowest = min ( $ratios );

    $sets [] = [ 'name'   => $name,
                 'scheme' => padThemeData ( $name ) ['scheme'],
                 'lowest' => number_format ( $lowest, 2 ),
                 'pair'   => array_search ( $lowest, $ratios ),
                 'level'  => ( $lowest >= 7 ) ? 'AAA' : ( ( $lowest >= 4.5 ) ? 'AA' : 'FAILS' ),
                 'enough' => ( $lowest >= padThemeNeeds ( $name ) ) ? 'enough' : 'too low' ];

  }

?>
