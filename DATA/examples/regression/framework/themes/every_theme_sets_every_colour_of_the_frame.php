<?php

  // Every theme of pad/subs/themes/types/ sets each of the 24 colours padThemeCss writes - the
  // page's palette, the side's, the menu's and the status pairs - as a #rrggbb value and
  // nothing else in its colours, has a name, a scheme that is light or dark and, when it
  // declares one, a contrast target of 4.5 or more: a theme that missed a colour wrote an empty
  // custom property, and the frame's own value showed through in that one place.

  include_once PAD . 'subs/themes/_lib/theme.php';

  $roles = [ 'bg', 'panel', 'text', 'soft', 'dim', 'line', 'accent', 'accent-text', 'accent-soft', 'head', 'code',
             'side', 'side-text', 'side-dim', 'side-hover', 'side-line', 'side-strong', 'menu',
             'ok', 'ok-bg', 'warn', 'warn-bg', 'bad', 'bad-bg' ];

  $sets = [];

  foreach ( padThemeTypes () as $name ) {

    $theme  = padThemeData ( $name );
    $colors = $theme ['colors'] ?? [];
    $faults = [];

    if ( ! is_string ( $theme ['name'] ?? NULL ) or trim ( $theme ['name'] ) === '' )
      $faults [] = 'no name';

    if ( ! in_array ( $theme ['scheme'] ?? '', [ 'light', 'dark' ], TRUE ) )
      $faults [] = 'no scheme';

    if ( isset ( $theme ['needs'] ) and ( ! is_numeric ( $theme ['needs'] ) or $theme ['needs'] < 4.5 ) )
      $faults [] = 'needs below AA';

    foreach ( array_diff ( $roles, array_keys ( $colors ) ) as $role )
      $faults [] = "missing $role";

    foreach ( array_diff ( array_keys ( $colors ), $roles ) as $role )
      $faults [] = "unknown $role";

    foreach ( $colors as $role => $value )
      if ( ! preg_match ( '/\A#[0-9a-f]{6}\z/', (string) $value ) )
        $faults [] = "$role is no #rrggbb";

    $sets [] = [ 'name' => $name, 'faults' => $faults ? implode ( ', ', $faults ) : 'complete' ];

  }

?>
