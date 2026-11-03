<?php

  // The page of a type, by the name of its address - ?type/pad, ?type/field: its line and
  // syntax, its required prefix, its section of TYPES.md and the
  // comment of its handler, the tags it reaches, its examples, the types beside it. A name that
  // is no type is a page not found.

  if ( is_string ( $typeName ) and ( isset ( padSubsystems [$typeName] ) or isset ( padSubsystemKinds [$typeName] ) ) )
    padRedirect ( 'subsystem/' . $typeName, [], 'subsystems' );

  if ( ! is_string ( $typeName ) or ! isset ( typesCatalog () [$typeName] ) )
    padAbort ( 404 );

  $typeInfo       = typesCatalog () [$typeName];
  $title          = $typeInfo ['key'];
  $typeKey        = $typeInfo ['key'];
  $typeAbout      = $typeInfo ['about'];
  $typeGroupKey   = $typeInfo ['group'];
  $typeGroupLabel = typesGroups () [$typeGroupKey] [0];
  $typeSyntaxHtml = padHighlightTokens ( $typeInfo ['syntax'], $typeInfo ['language'] );
  $typeOrder      = $typeInfo ['order'];
  $typeBare       = $typeInfo ['bare'];
  $typeHasPrefix  = $typeInfo ['prefix'];
  $typeOrderCount = count ( typesOrder () );
  $typeDocHtml    = $typeHasPrefix ? typesDoc ( $typeName ) : '<p>' . htmlspecialchars ( $typeInfo ['about'] ) . ' Choose a kind below for its description and usage example.</p>';
  $typeComment    = preg_replace ( '/\s+/', ' ', $typeInfo ['comment'] );
  $typeExamples   = typesExamples ( $typeName );

  // Each example that runs gets the icons that take it to the WASM editor and the live
  // application, at the right of its result's head; one shown but not run here the live icon
  // alone, at the right of its template's head (_common/_lib/playground.php).

  include_once COMMON . '_lib/playground.php';

  foreach ( $typeExamples as $i => $one ) {
    $typeExamples [$i] ['number'] = $i + 1;
    $typeExamples [$i] ['play']   = $one ['page'] === '' ? '' : ( $one ['run']
                                  ? padPlayPage ( APP, $one ['page'], TRUE )
                                  : padPlayLink ( trim ( preg_replace ( '/\A\{pad:meta [^\n]*\}\R?/', '', (string) padFileGet ( APP . $one ['page'] ) ) ) . "\n", '', '', TRUE, 'live' ) );
  }

  $typeMembers     = array_map ( fn ( $one ) => [ 'key' => $one [0], 'link' => $one [1] ], typesMembers ( $typeName ) );
  $typeMemberCount = count ( $typeMembers );

  $typeNames = array_keys ( typesCatalog () );
  $typeHere  = array_search ( $typeName, $typeNames );
  $typePrev  = $typeNames [ ( $typeHere + count ( $typeNames ) - 1 ) % count ( $typeNames ) ];
  $typeNext  = $typeNames [ ( $typeHere + 1 ) % count ( $typeNames ) ];
  $typePrevLabel = typesCatalog () [$typePrev] ['key'];
  $typeNextLabel = typesCatalog () [$typeNext] ['key'];

?>
