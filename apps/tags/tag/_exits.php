<?php

  // A tag page: its own tag/<name>.php has just run and set $tagAbout, $tagSyntax and the
  // rest; here they become what the frame around the description shows - the syntax
  // highlighted, the parameters and options as rows, the related tags with their line, the
  // examples with their sources - and the tags before and after it by name.

  if ( $tagsFrame !== $padPage )
    return;

  // A built-in tag's page is tag/<name>; a kind's is the route tag/[tagType]/[tagKind] -
  // ?tag/chart/pie - that binds the two names.

  $tagKey        = str_contains ( $padPage, '[' ) ? "$tagType:$tagKind" : 'pad:' . basename ( $padPage );
  $tagInfo       = tagsCatalog () [$tagKey];
  $tagName       = $tagInfo ['name'];
  $tagAbout      = $tagInfo ['about'];
  $tagForm       = $tagInfo ['form'];
  $title         = '{' . $tagKey . '}';
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
      $tagSeeRows [] = [ 'see' => $see, 'link' => tagsCatalog () [$see] ['link'], 'about' => tagsCatalog () [$see] ['about'] ];

  // The examples: a built-in tag's files of examples/<name>/, each run as a page of this
  // application; a kind's one example, a template run here or a page of the application
  // that has it (tagsKindExample).

  $tagExampleRows = [];

  // Each example that runs gets the icon that takes it to the playground, at the right of its
  // result's head (_common/_lib/playground.php).

  include_once COMMON . '_lib/playground.php';

  if ( $tagInfo ['type'] == 'pad' )

    foreach ( tagsExamples ( $tagName ) as $i => $example ) {

      $file = $example ['run'] ? $example ['page'] . '.pad' : $example ['page'];
      $php  = $example ['run'] && file_exists ( APP . $example ['page'] . '.php' ) ? $example ['page'] . '.php' : '';

      $tagExampleRows [] = $example + [
        'number'  => $i + 1,
        'file'    => $file,
        'padHtml' => tagsSource ( $file, 'pad' ),
        'php'     => $php,
        'phpHtml' => $php ? tagsSource ( $php, 'php' ) : '',
        'source'  => '',
        'app'     => '',
        'open'    => '?' . $example ['page'],
        'play'    => $example ['run'] ? padPlayPage ( APP, $example ['page'], TRUE ) : '' ];

    }

  else {

    $example = tagsKindExample ( $tagInfo ['type'], $tagName );
    $root    = $example ['app'] !== '' ? APPS . "{$example ['app']}/" : APP;
    $php     = ( $example ['page'] !== '' and is_file ( $root . $example ['page'] . '.php' ) ) ? $example ['page'] . '.php' : '';

    if ( $example ['page'] !== '' ) {
      $padHtml = padHighlightTokens ( trim ( preg_replace ( '/\A\{meta [^\n]*\}\R?/', '', padFileGet ( $root . $example ['page'] . '.pad' ) ) ), 'pad' );
      $file    = ( $example ['app'] !== '' ? "{$example ['app']}/" : '' ) . $example ['page'] . '.pad';
      $open    = $example ['app'] !== '' ? $padRoot . "{$example ['app']}/?{$example ['page']}" : "?{$example ['page']}";
    } else {
      $padHtml = padHighlightTokens ( $example ['source'], 'pad' );
      $file    = $open = '';
    }

    // A kind's example is shown by its own page, tag/[tagType]/[tagKind].pad, not by this
    // frame: a crawl renders a page without its frame (padReference) - the example a frame
    // showed was on no xref record, every kind on no page of the reference but its subsystem's.

    $tagKindExample = [ $example + [
      'file'    => $file,
      'padHtml' => $padHtml,
      'php'     => $php,
      'phpHtml' => $php ? padHighlightTokens ( trim ( padFileGet ( $root . $php ) ), 'php' ) : '',
      'open'    => $open,
      'play'    => ( $example ['page'] !== '' ) ? padPlayPage ( $root, $example ['page'], TRUE )
                                                 : padPlayLink ( $example ['source'] . "\n", '', '', TRUE ) ] ];

  }

  // A kind's page says how it is written and where it comes from; its file's comment, when
  // it has one, is the rest of what it says.

  $tagKindHtml = ( $tagInfo ['type'] == 'pad' ) ? '' : tagsKindHtml ( $tagInfo );

  $tagNames = array_keys ( tagsCatalog () );
  $tagHere  = array_search ( $tagKey, $tagNames );
  $tagPrev  = $tagNames [ ( $tagHere + count ( $tagNames ) - 1 ) % count ( $tagNames ) ];
  $tagNext  = $tagNames [ ( $tagHere + 1 ) % count ( $tagNames ) ];
  $tagPrevLink = tagsCatalog () [$tagPrev] ['link'];
  $tagNextLink = tagsCatalog () [$tagNext] ['link'];

?>
