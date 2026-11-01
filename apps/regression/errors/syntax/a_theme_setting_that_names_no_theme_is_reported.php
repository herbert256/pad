<?php

  // $padTheme naming no theme is reported when the theme of the page is decided
  // (padThemeChosen, subs/themes/_lib/theme.php) - on the way out, where an include request
  // like this one never asks, so it is asked here.

  $padTheme = 'neon';

  include_once PAD . 'subs/themes/_lib/theme.php';

  padThemeChosen ();

?>
