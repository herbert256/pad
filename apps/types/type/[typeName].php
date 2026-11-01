<?php

  // The page of a type, by the name of its address - ?type/pad, ?type/chart: its line and
  // syntax, its place in the order a bare name is tried in, its section of TYPES.md and the
  // comment of its handler, the tags it reaches, its examples, the types beside it. A name that
  // is no type is a page not found.

  if ( ! is_string ( $typeName ) or ! isset ( typesCatalog () [$typeName] ) )
    padAbort ( 404 );

  $typeInfo       = typesCatalog () [$typeName];
  $title          = $typeInfo ['key'];
  $typeKey        = $typeInfo ['key'];
  $typeAbout      = $typeInfo ['about'];
  $typeGroupKey   = $typeInfo ['group'];
  $typeGroupLabel = typesGroups () [$typeGroupKey] [0];
  $typeSyntaxHtml = padHighlightTokens ( $typeInfo ['syntax'], 'pad' );
  $typeOrder      = $typeInfo ['order'];
  $typeBare       = $typeInfo ['bare'];
  $typeOrderCount = count ( typesOrder () );
  $typeDocHtml    = typesDoc ( $typeName );
  $typeComment    = preg_replace ( '/\s+/', ' ', $typeInfo ['comment'] );
  $typeExamples   = typesExamples ( $typeName );

  foreach ( $typeExamples as $i => $one )
    $typeExamples [$i] ['number'] = $i + 1;

  $typeMembers     = array_map ( fn ( $one ) => [ 'key' => $one [0], 'link' => $one [1] ], typesMembers ( $typeName ) );
  $typeMemberCount = count ( $typeMembers );

  $typeNames = array_keys ( typesCatalog () );
  $typeHere  = array_search ( $typeName, $typeNames );
  $typePrev  = $typeNames [ ( $typeHere + count ( $typeNames ) - 1 ) % count ( $typeNames ) ];
  $typeNext  = $typeNames [ ( $typeHere + 1 ) % count ( $typeNames ) ];

?>
