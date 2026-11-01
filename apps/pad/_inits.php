<?php

  // The side of the PAD home is the menu of the PAD applications: the groups, the entries
  // and the line about each that the pull-down of the brand top left shows (_common/_tags/
  // padBrand), read from the same list, _common/_data/padApps.php, so the two never part.
  // _inits.pad writes it in the frame's side slot; the home itself shows the Hello World
  // example, so this application's own two pages need no menu of their own.

  $homeMenu = [];

  foreach ( include COMMON . '_data/padApps.php' as $homeHead => $homeEntries ) {

    $homeRows = [];

    foreach ( $homeEntries as $homeApp => [ $homeLabel, $homeIcon, $homeAbout ] )
      $homeRows [] = [ 'name'    => $homeLabel,
                       'href'    => "$padRoot$homeApp/",
                       'icon'    => $homeIcon,
                       'about'   => $homeAbout,
                       'current' => ( $homeApp == $padApp ) ? 1 : 0 ];

    $homeMenu [] = [ 'head' => $homeHead, 'homeApps' => $homeRows ];

  }

  unset ( $homeHead, $homeEntries, $homeRows, $homeApp, $homeLabel, $homeIcon, $homeAbout );

?>
