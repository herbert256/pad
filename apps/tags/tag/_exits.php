<?php

  // A tag page: its own tag/<name>.php has just run and set $tagAbout, $tagSyntax and the
  // rest; here they become what the frame around the description shows - the syntax
  // highlighted, the parameters and options as rows, the related tags with their line, the
  // examples with their sources - and the tags before and after it by name.

  if ( $tagsFrame !== $padPage )
    return;

  $tagName       = basename ( $padPage );
  $tagInfo       = tagsCatalog () [$tagName];
  $title         = '{' . $tagName . '}';
  $tagGroupKey   = $tagInfo ['group'];
  $tagGroupLabel = tagsGroups () [$tagGroupKey] [0];
  $tagFormLabel  = tagsForms  () [ $tagInfo ['form'] ] [0];
  $tagSyntaxHtml = padHighlightTokens ( $tagInfo ['syntax'], 'pad' );

  $tagParmRows = [];

  foreach ( $tagInfo ['parms'] as $parm => $meaning )
    $tagParmRows [] = [ 'parm' => $parm, 'meaning' => $meaning ];

  $tagOptionRows = [];

  foreach ( $tagInfo ['options'] as $option => $meaning )
    $tagOptionRows [] = [ 'option' => $option, 'meaning' => $meaning ];

  $tagSeeRows = [];

  foreach ( $tagInfo ['see'] as $see )
    if ( isset ( tagsCatalog () [$see] ) )
      $tagSeeRows [] = [ 'see' => $see, 'about' => tagsCatalog () [$see] ['about'] ];

  $tagExampleRows = [];

  foreach ( tagsExamples ( $tagName ) as $i => $example ) {

    $file = $example ['run'] ? $example ['page'] . '.pad' : $example ['page'];
    $php  = $example ['run'] && file_exists ( APP . $example ['page'] . '.php' ) ? $example ['page'] . '.php' : '';

    $tagExampleRows [] = $example + [
      'number'  => $i + 1,
      'file'    => $file,
      'padHtml' => tagsSource ( $file, 'pad' ),
      'php'     => $php,
      'phpHtml' => $php ? tagsSource ( $php, 'php' ) : '' ];

  }

  $tagNames = array_keys ( tagsCatalog () );
  $tagHere  = array_search ( $tagName, $tagNames );
  $tagPrev  = $tagNames [ ( $tagHere + count ( $tagNames ) - 1 ) % count ( $tagNames ) ];
  $tagNext  = $tagNames [ ( $tagHere + 1 ) % count ( $tagNames ) ];

?>
