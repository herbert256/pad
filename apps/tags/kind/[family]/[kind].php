<?php

  if ( ! is_string ( $family ) or ! is_string ( $kind ) or ! isset ( appSubsystemCatalog () ["$family:$kind"] ) )
    padAbort ( 404 );

  $kindInfo = appSubsystemCatalog () ["$family:$kind"];
  $familyInfo = appSubsystemFamilies () [$family];
  $title = $kindInfo ['label'];
  $kindLabel = $kindInfo ['label'];
  $familyLabel = $familyInfo ['label'];
  $familyAbout = $familyInfo ['about'];
  $kindAbout = $kindInfo ['about'];
  $kindGroup = $kindInfo ['group'];
  $kindParent = $familyInfo ['tag'];
  $kindExample = $kindInfo ['example'];
  $kindSourceHtml = padHighlightTokens ( $kindExample ['source'], $kindExample ['language'] );
  $kindParagraphs = array_map ( fn ( $text ) => [ 'text' => trim ( $text ) ],
                                preg_split ( '/\n\s*\n/', $kindInfo ['text'], -1, PREG_SPLIT_NO_EMPTY ) );
  $kindSiblings = array_map ( fn ( $name ) => [ 'name' => $name, 'link' => "kind/$family/$name" ], padKinds ( $familyInfo ['directory'] ) );

?>
