<?php

  // The sections application: the four subsystems of the engine that dress a page - the
  // sections (pad/subs/sections/), the effects (pad/subs/effects/), the themes
  // (pad/subs/themes/) and the palettes (pad/subs/palettes/) - each read as the engine reads
  // it: its kinds are the files of its types/ (padKinds), and what a kind is the comment at
  // the top of its file. A kind added to the engine is here without a change to the
  // application.
  //
  // The examples of a section or an effect are the files of _examples/<group>/: <kind>.pad,
  // then <kind>-2.pad and on, each a template shown beside what it renders. A theme and a
  // palette are shown by templates made here, the same for every one of them.

  // The groups: [ heading, the subsystem's directory, the line of its page, the icon of the menu ].

  function sectionsGroups () {

    return [
      'section' => [ 'Sections', 'sections', 'Blocks a landing page is built from - a hero, features, prices, questions, a footer - in pure CSS, coloured by the theme.', 'layout' ],
      'effect'  => [ 'Effects',  'effects',  'Motion and decoration, CSS first - still for whoever asks for reduced motion, and read whole by a screen reader.', 'sparkle' ],
      'theme'   => [ 'Themes',   'themes',   'The colours of the frame swapped for another set - for a page, an application or the visitor\'s own choice.', 'droplet' ],
      'palette' => [ 'Palettes', 'palettes', 'Colour scales for charts and anything else - perceptual, colour-blind safe, sequential and diverging.', 'bar-chart' ],
    ];

  }

  // The kinds of a group, each [ group, kind, name, line, text, link ]: the comment at the top
  // of its file, the kind's name and the dash before it left off - its line the first
  // sentence of it, its name the kind's own, or a palette's and a theme's as its file says.

  function sectionsKinds ( $group ) {

    static $kinds = [];

    if ( isset ( $kinds [$group] ) )
      return $kinds [$group];

    $dir  = sectionsGroups () [$group] [1];
    $list = [];

    foreach ( padKinds ( $dir ) as $kind ) {

      $php     = (string) padFileGet ( PAD . "subs/$dir/types/$kind.php" );
      $comment = [];

      if ( preg_match ( '/\A<\?php\s*\n((?:[ \t]*\/\/[^\n]*\n)+)/', $php, $match ) )
        foreach ( explode ( "\n", rtrim ( $match [1] ) ) as $line ) {
          $line = preg_replace ( '/^\s*\/\/ ?/', '', $line );
          if ( trim ( $line ) === '' )
            break;
          $comment [] = trim ( $line );
        }

      $text = implode ( ' ', $comment );

      // A kind written with its syntax first - {section:hero ...} - is described from the dash
      // after the syntax on; every other one from the dash after its name.

      if ( str_starts_with ( $text, '{' ) and ( $at = strpos ( $text, '} - ' ) ) !== FALSE )
        $text = substr ( $text, $at + 4 );
      else
        $text = preg_replace ( '/^' . preg_quote ( $kind, '/' ) . '\s+-\s+/', '', $text );

      $text = ucfirst ( $text );
      $line = preg_split ( '/(?<=[.!?])\s/', $text, 2 ) [0];

      if ( preg_match ( '/^(.{24,}?)(?:: | - |; )/', $line, $cut ) )
        $line = $cut [1] . '.';

      $name = preg_match ( "/'name'\s*=>\s*'([^']+)'/", $php, $found ) ? $found [1] : ucfirst ( $kind );

      $list [] = [ 'group' => $group, 'kind' => $kind, 'name' => $name, 'line' => $line, 'text' => $text,
                   'link' => "?$group/$kind" ];

    }

    return $kinds [$group] = $list;

  }

  function sectionsKind ( $group, $kind ) {

    foreach ( isset ( sectionsGroups () [$group] ) ? sectionsKinds ( $group ) : [] as $one )
      if ( $one ['kind'] === $kind )
        return $one;

    return NULL;

  }

  // The examples of a kind: [ template, source, play ] each - the template run where it is
  // shown, its source coloured, the icons that take it to the playground and the live
  // application.

  function sectionsExamples ( $group, $kind ) {

    include_once COMMON . '_lib/playground.php';

    $templates = [];

    if ( $group == 'theme' )
      $templates [] = sectionsThemeTemplate ( $kind );
    elseif ( $group == 'palette' )
      $templates = sectionsPaletteTemplates ( $kind );
    else
      foreach ( array_merge ( [ "$kind.pad" ], array_map ( fn ( $n ) => "$kind-$n.pad", range ( 2, 9 ) ) ) as $file )
        if ( is_file ( APP . "_examples/$group/$file" ) )
          $templates [] = trim ( (string) padFileGet ( APP . "_examples/$group/$file" ) );

    $examples = [];

    foreach ( $templates as $template )
      $examples [] = [ 'template' => $template,
                       'source'   => padHighlightTokens ( $template, 'pad' ),
                       'play'     => padPlayLink ( "$template\n" ) ];

    return $examples;

  }

  // A group's page: [ title, line, wide, kinds ] - the kinds each with their examples.

  function sectionsGroupPage ( $group ) {

    $kinds = sectionsKinds ( $group );

    foreach ( $kinds as $i => $one )
      $kinds [$i] ['examples'] = sectionsExamples ( $group, $one ['kind'] );

    return [ sectionsGroups () [$group] [0], sectionsGroups () [$group] [2], in_array ( $group, [ 'section', 'palette' ] ), $kinds ];

  }

  // The contrast of a theme's text on its backgrounds, a row a pair: [ fore, back, ratio ].

  function sectionsContrasts ( $theme ) {

    include_once PAD . 'subs/themes/_lib/theme.php';

    $rows = [];

    foreach ( padThemeContrasts ( $theme ) as $pair => $ratio ) {
      [ $fore, $back ] = explode ( ' on ', $pair );
      $rows [] = [ 'fore' => $fore, 'back' => $back, 'ratio' => number_format ( $ratio, 2 ) ];
    }

    return $rows;

  }

  // A theme shown in a box of its own beside the page: {theme 'x'}...{/theme} around a
  // heading, a paragraph with a link, a note, a table and a button - the parts of a page
  // whose colours the theme sets.

  function sectionsThemeTemplate ( $theme ) {

    return <<<PAD
{theme '$theme'}
  <h3>Spring catalogue</h3>
  <p>Our new range is in. Read the <a href="#notes">release notes</a> or browse
     the <code>2026</code> collection below.</p>
  <table class="sx-table">
    <tr><th>Item</th><th>Stock</th></tr>
    <tr><td>Linen shirt</td><td>124</td></tr>
    <tr><td>Canvas bag</td><td>58</td></tr>
  </table>
  <p class="sx-note">Free delivery on orders over 50.</p>
  <button type="button" class="sx-button">Shop now</button>
{/theme}
PAD;

  }

  // A palette shown as its swatches and in charts: the bars of several series and, for a
  // scale - sequential or diverging - a heatmap, for a categorical palette a pie.

  function sectionsPaletteTemplates ( $palette ) {

    include_once PAD . 'subs/palettes/_lib/palette.php';

    $type = padPaletteData ( $palette ) ['type'];

    $templates = [ "{palette '$palette'/}" ];

    $templates [] = <<<PAD
{data 'sales'}
quarter,north,east,south,west
Q1,42,31,25,18
Q2,48,35,29,22
Q3,51,39,27,26
Q4,57,44,33,30
{/data}
{chart 'bar', data='sales', label='quarter', value='north, east, south, west', palette='$palette'/}
PAD;

    if ( $type == 'categorical' )
      $templates [] = <<<PAD
{data 'traffic'}
source,visits
Search,4200
Direct,2600
Social,1800
Mail,900
Other,500
{/data}
{chart 'donut', data='traffic', label='source', value='visits', palette='$palette'/}
PAD;
    else
      $templates [] = <<<PAD
{data 'temps'}
month,day,change
Jan,Mon,-4
Jan,Wed,-1
Jan,Fri,2
Apr,Mon,1
Apr,Wed,4
Apr,Fri,6
Jul,Mon,8
Jul,Wed,5
Jul,Fri,9
Oct,Mon,3
Oct,Wed,0
Oct,Fri,-2
{/data}
{chart 'heatmap', data='temps', x='month', y='day', value='change', palette='$palette'/}
PAD;

    return $templates;

  }

?>
