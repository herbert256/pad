<?php

  // Starts the 'xref' info mode. Besides loading padInfoXref it reads the template source of the
  // requested page into $padInfoXrefSource, which is the filter the recorder uses: a name is only
  // cross-referenced when it literally occurs in the page the visitor asked for.
  //
  // inits/info.php turns this mode on by itself for a ?padReference request, which is how the
  // reference app collects its data.

  include_once PAD . 'info/types/xref/_lib.php';

  $padInfoXrefSource = padInfoGet ( APP . $padStartPage . '.pad' );

  // The configuration this application chose for itself, captured by inits/config.php:
  // recorded once per page, so the reference's configuration families can point at real
  // pages running under each value.

  foreach ( $padConfigSet ?? [] as $padInfoXrefFamily => $padInfoXrefValue )
    padInfoXref ( "config/$padInfoXrefFamily", $padInfoXrefValue );

?>