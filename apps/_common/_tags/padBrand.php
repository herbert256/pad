<?php

  // The brand every PAD application shows top left: the {} logo, a link to the PAD home, and
  // beside it the application's name - PAD charts - which opens the pull-down of the PAD
  // applications, grouped, the one shown marked. One tag, so every application has the same
  // corner whatever its own bar looks like:
  //
  //   <header class="topbar">{common:padBrand/} <nav>...</nav></header>
  //
  // The common: prefix reaches it from an application that switched $padCommon off - most
  // of them. Its stylesheet and script are www/_common/brand.css and brand.js, linked by the
  // tag itself, with a version from their contents as {pad:asset} writes it; the frame
  // (padFrame) links them in its <head> and writes the brand with the option frame, which
  // leaves the links out. The colours are the brand's own --pad-brand-* properties, light or
  // dark as the page's color-scheme says - the frame's light, a theme's, or what data-theme on
  // <html> says; the name takes the colour of the bar it stands in.
  //
  // Offline - an application packed by pad pack (padWasm) - there is neither a PAD home nor
  // another application to go to, nor www/_common: the logo is no link, the name links the
  // application's own home, and the stylesheet is written into the page.

  $brandGroups = include COMMON . '_data/padApps.php';

  $brandName = basename ( $padApp );
  $brandMenu = [];

  // Keep these groups together when the menu wraps from four columns to two or one.
  $brandColumns = include COMMON . '_data/padAppColumns.php';

  foreach ( $brandGroups as $brandHead => $brandEntries ) {

    $brandRows = [];

    foreach ( $brandEntries as $brandApp => [ $brandLabel, $brandIcon, $brandAbout ] ) {

      if ( $brandApp == $padApp )
        $brandName = $brandLabel;

      $brandRows [] = [ 'name'    => $brandLabel,
                        'href'    => "$padRoot$brandApp/",
                        'icon'    => $brandIcon,
                        'about'   => $brandAbout,
                        'current' => ( $brandApp == $padApp ) ? 1 : 0 ];

    }

    $brandMenu [$brandHead] = [ 'head' => $brandHead, 'brandApps' => $brandRows ];

  }

  foreach ( $brandColumns as &$brandColumn )
    $brandColumn = [ 'brandGroups' => array_map ( fn ( $head ) => $brandMenu [$head], $brandColumn ) ];

  unset ( $brandColumn );

  $brandHome    = "{$padRoot}pad/";
  $brandAppHome = "$padRoot$padApp/";
  $brandFrame   = padTagParm ( 'frame' ) ? 1 : 0;
  $brandOffline = padWasm () ? 1 : 0;

  // www/_common/brand.css and brand.js, with the version {pad:asset} gives a file of an
  // application's own - or, offline, the stylesheet itself, for a <style> of the page.

  $brandCss = $brandJs = $brandStyle = '';

  foreach ( [ 'brandCss' => 'brand.css', 'brandJs' => 'brand.js' ] as $brandVar => $brandFile ) {
    $brandAt     = dirname ( APPS ) . "/www/_common/$brandFile";
    $brandAlgo   = in_array ( 'xxh128', hash_algos (), TRUE ) ? 'xxh128' : 'sha256';
    $brandHash   = is_file ( $brandAt ) ? '?v=' . substr ( (string) hash_file ( $brandAlgo, $brandAt ), 0, 10 ) : '';
    ${$brandVar} = "{$padRoot}_common/$brandFile$brandHash";
  }

  if ( $brandOffline and is_file ( dirname ( APPS ) . '/www/_common/brand.css' ) )
    $brandStyle = '<style>' . file_get_contents ( dirname ( APPS ) . '/www/_common/brand.css' ) . '</style>';

  unset ( $brandGroups, $brandHead, $brandEntries, $brandRows, $brandApp, $brandLabel, $brandIcon,
          $brandAbout, $brandVar, $brandFile, $brandAt, $brandAlgo, $brandHash );

  return TRUE;

?>
