<?php

  if ( ! isset ( $xref ) ) $xref = 'unit';
  if ( ! isset ( $item ) ) $item = 'length';

  $entry = referenceEntry ( $xref, $item );

  if ( ! $entry or ! $entry ['files'] )
    return padRefuse ( 404, 'There is no such reference' );

  $type = $entry ['type'];
  $title = "Reference - $type - $item";
  $entryDescription = '';
  $entryUsage = '';
  $entryAbout = '';
  $entryDocumentation = '';
  $entrySources = [];

  foreach ( $entry ['files'] as $file => $source ) {
    if ( $entryDescription === '' and preg_match ( '/\A<\?php\s*\n((?:[ \t]*\/\/[^\n]*\n|[ \t]*\n)+)/', $source, $match ) )
      $entryDescription = trim ( preg_replace ( '/^[ \t]*\/\/ ?/m', '', $match [1] ) );
    $language = [ 'php' => 'php', 'pad' => 'pad', 'svg' => 'html' ] [pathinfo ( $file, PATHINFO_EXTENSION )];
    $entrySources [] = [ 'file' => $file, 'source' => padHighlightTokens ( $source, $language ) ];
  }

  if ( isset ( padSubsystems [$xref] ) or isset ( padSubsystemKinds [$xref] ) ) {
    include_once COMMON . '_lib/subsystemCatalog.php';
    $entryDocumentation = $padRoot . 'tags/?' . ( isset ( padSubsystems [$xref] ) ? 'tag/' : 'kind/' ) . "$xref/$item";
    $example = appSubsystemExample ( $xref, $item );
    $entryUsage = padHighlightTokens ( $example ['source'], $example ['language'] );
    $entryAbout = $example ['about'];
    if ( $entryDescription === '' and isset ( padSubsystemKinds [$xref] ) )
      $entryDescription = appSubsystemComment ( $xref, $item );
  }

  // The shared showcase fragments are deliberately a pair, with the page's data prepared
  // by showcaseExamplePage. Running either fragment by itself produces an incomplete box.
  if ( $xref === 'tag/common' and in_array ( $item, [ 'showcaseExampleStart', 'showcaseExampleEnd' ], TRUE ) ) {
    $entryDescription = 'Opens or closes the shared example display, including source panels, a result and previous/next links. Prepare the demo variables with showcaseExamplePage and use both fragments around the rendered example.';
    $entryUsage = padHighlightTokens ( "{common:showcaseExampleStart/}\n  <p>The rendered example goes here.</p>\n{common:showcaseExampleEnd/}", 'pad' );
  }

  if ( $xref === 'config/outputType' and $item === 'format' )
    $entryUsage = padHighlightTokens ( "\$padOutputType = 'format';\n\$padOutputFormat = 'toml';\n\$padExpose = ['message'];\n\$message = 'Hello from PAD';", 'php' );

  if ( $xref === 'config/info' and $item === 'trace' )
    $entryUsage = padHighlightTokens ( "{pad:trace}\n  {pad:echo 'Trace this part of the page.'/}\n{/pad:trace}", 'pad' );

  if ( $entryDescription === '' )
    $entryDescription = "$type: $item. The implementation below defines this entry.";

  $entryDescriptionHtml = padMarkdown ( $entryDescription );

?>
