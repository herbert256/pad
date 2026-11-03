<?php

  // One registered subsystem: its syntax, documentation, kinds and examples.

  if ( ! is_string ( $subsystemName ) or ! isset ( subsystemsCatalog () [$subsystemName] ) )
    padAbort ( 404 );

  $subsystemInfo       = subsystemsCatalog () [$subsystemName];
  $title          = $subsystemInfo ['key'];
  $subsystemKey        = $subsystemInfo ['key'];
  $subsystemAbout      = $subsystemInfo ['about'];
  $subsystemGroupKey   = $subsystemInfo ['group'];
  $subsystemGroupLabel = subsystemsGroups () [$subsystemGroupKey] [0];
  $subsystemSyntaxHtml = padHighlightTokens ( $subsystemInfo ['syntax'], $subsystemInfo ['language'] );
  $subsystemHasPrefix  = $subsystemInfo ['prefix'];
  $subsystemDirectory = $subsystemInfo ['directory'];
  $subsystemDocHtml    = $subsystemHasPrefix ? appReferenceDoc ( $subsystemName ) : '<p>' . htmlspecialchars ( $subsystemInfo ['about'] ) . ' Choose a kind below for its description and usage example.</p>';
  $subsystemComment    = preg_replace ( '/\s+/', ' ', $subsystemInfo ['comment'] );
  $subsystemExamples   = subsystemsExamples ( $subsystemName );

  // Each example that runs gets the icons that take it to the WASM editor and the live
  // application, at the right of its result's head; one shown but not run here the live icon
  // alone, at the right of its template's head (_common/_lib/playground.php).

  include_once COMMON . '_lib/playground.php';

  foreach ( $subsystemExamples as $i => $one ) {
    $subsystemExamples [$i] ['number'] = $i + 1;
    $subsystemExamples [$i] ['play']   = $one ['page'] === '' ? '' : ( $one ['run']
                                  ? padPlayPage ( APP, $one ['page'], TRUE )
                                  : padPlayLink ( trim ( preg_replace ( '/\A\{pad:meta [^\n]*\}\R?/', '', (string) padFileGet ( APP . $one ['page'] ) ) ) . "\n", '', '', TRUE, 'live' ) );
  }

  $subsystemMembers     = array_map ( fn ( $one ) => [ 'key' => $one [0], 'link' => $one [1] ], subsystemsMembers ( $subsystemName ) );
  $subsystemMemberCount = count ( $subsystemMembers );

  $subsystemNames = array_keys ( subsystemsCatalog () );
  $subsystemHere  = array_search ( $subsystemName, $subsystemNames );
  $subsystemPrev  = $subsystemNames [ ( $subsystemHere + count ( $subsystemNames ) - 1 ) % count ( $subsystemNames ) ];
  $subsystemNext  = $subsystemNames [ ( $subsystemHere + 1 ) % count ( $subsystemNames ) ];
  $subsystemPrevLabel = subsystemsCatalog () [$subsystemPrev] ['key'];
  $subsystemNextLabel = subsystemsCatalog () [$subsystemNext] ['key'];

?>
