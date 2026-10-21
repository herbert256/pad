<?php

  // The frame the PAD applications share - every one that uses _common, and admin, edit and
  // tags, which reach it with the common: prefix: a side with the logo and the application's
  // name, its menu under it, and the bar with the page's title and the light/dark switch
  // above the page.
  // _common has no wrapper of its own: each application's _inits.pad opens the frame with
  // this tag, the parts of its own as slots, and closes it after the page with the
  // padFrameEnd snippet:
  //
  //   {common:padFrame}
  //     {slot 'head'}{asset 'admin.css', tag}{/slot}    in <head>
  //     {slot 'foot'}...{/slot}                           at the bottom of the side
  //     {slot 'tools'}...{/slot}                          in the bar, before the light/dark switch
  //   {/common:padFrame}
  //   @page@
  //   {common:padFrameEnd}
  //
  // and further {slot 'side'} - the side's room under the menu, the editor's files - and
  // {slot 'title'}, the bar's left half in place of the title. The page stays outside the
  // tag, at the top level of the wrapper, where {flush} and the rest that must stand there
  // can. The common: prefix reaches the tag from an application that switched $padCommon
  // off.
  //
  // What the frame reads from the application:
  //
  //   $title       the page's title - in the bar, and in <title>; $skipTitle leaves it out
  //                of the bar for a page that shows a heading of its own
  //   $frameMenu   the application's menu, rows of head (a heading - the rest empty), link,
  //                label, icon (a name of the icon tag, or '') and current - left out, it is
  //                whether the link names the page shown
  //
  // and the option fill - {common:padFrame fill} - for a page that fills the window
  // instead of scrolling: the editor. The stylesheet and the script are www/_common/pad.css
  // and pad.js, with a version from their contents as {asset} writes it.
  //
  // Clicking the logo opens the menu of the applications: the family above, under it the
  // other applications that belong with it.

  $frameFamily = [
    'pad'             => [ 'home',       'home',         'What PAD is, Hello World' ],
    'manual'          => [ 'manual',     'help',         'The manual, a page per subject' ],
    'tags'            => [ 'tags',       'tag',          'Every built-in tag, with examples' ],
    'reference'       => [ 'reference',  'list',         'Tags, functions and options, cross-referenced' ],
    'sequence'        => [ 'sequence',   'bar-chart',    'The sequence subsystem' ],
    'regression/main' => [ 'regression', 'check-circle', 'The regression suites' ],
    'admin'           => [ 'admin',      'settings',     'The console of this installation' ],
    'edit'            => [ 'edit',       'edit',         'The editor of the applications' ]
  ];

  $frameOthers = [
    'examples'      => [ 'examples',      'code',     'The examples of the manual and the suites' ],
    'develop'       => [ 'develop',       'terminal', 'Builds and tools for PAD itself' ],
    'apps'          => [ 'apps',          'grid',     'Every application of this installation' ],
    'classicModels' => [ 'classicModels', 'database', 'PAD Select over the Classic Models database' ],
    'structure'     => [ 'structure',     'folder',   'The _xxx directories at every level' ]
  ];

  $frameApps = $frameMore = [];

  foreach ( [ 'frameApps' => $frameFamily, 'frameMore' => $frameOthers ] as $frameList => $frameEntries )
    foreach ( $frameEntries as $frameApp => [ $frameLabel, $frameIcon, $frameAbout ] )
      ${$frameList} [] = [ 'name'    => $frameLabel,
                           'href'    => "$padRoot$frameApp/",
                           'icon'    => $frameIcon,
                           'about'   => $frameAbout,
                           'current' => ( $frameApp == $padApp ) ? 1 : 0 ];

  $frameName  = $frameFamily [$padApp] [0] ?? $frameOthers [$padApp] [0] ?? basename ( $padApp );
  $frameClass = 'app-' . str_replace ( '/', '-', $padApp );
  $frameFill  = padTagParm ( 'fill' ) ? 1 : 0;

  $frameTitle = ( isset ( $skipTitle ) or ! isset ( $title ) or ! is_scalar ( $title ) )
              ? '' : ucfirst ( str_replace ( '_', ' ', (string) $title ) );

  // The menu, every row with every field - a heading has no link, an entry no heading. An
  // entry that does not say whether it is the page shown is when its link names that page:
  // ?orders, or $padGo with orders.

  $frameRows = [];

  foreach ( ( isset ( $frameMenu ) and is_array ( $frameMenu ) ) ? $frameMenu : [] as $frameRow )
    $frameRows [] = [ 'head'    => (string) ( $frameRow ['head']  ?? '' ),
                      'link'    => (string) ( $frameRow ['link']  ?? '' ),
                      'label'   => (string) ( $frameRow ['label'] ?? '' ),
                      'icon'    => (string) ( $frameRow ['icon']  ?? '' ),
                      'current' => ( array_key_exists ( 'current', $frameRow )
                                     ? ! empty ( $frameRow ['current'] )
                                     : in_array ( (string) ( $frameRow ['link'] ?? '' ), [ "?$padPage", "$padGo$padPage" ], TRUE ) ) ? 1 : 0 ];

  $frameMenu = $frameRows;
  $frameNav  = $frameRows ? 1 : 0;

  // www/_common/ - the stylesheet and the script - with the version {asset} gives a file of
  // an application's own: ten hex digits of a hash of the contents.

  $frameCss = $frameJs = '';

  foreach ( [ 'frameCss' => 'pad.css', 'frameJs' => 'pad.js' ] as $frameVar => $frameFile ) {
    $frameAt     = dirname ( APPS ) . "/www/_common/$frameFile";
    $frameAlgo   = in_array ( 'xxh128', hash_algos (), TRUE ) ? 'xxh128' : 'sha256';
    $frameHash   = is_file ( $frameAt ) ? '?v=' . substr ( (string) hash_file ( $frameAlgo, $frameAt ), 0, 10 ) : '';
    ${$frameVar} = "{$padRoot}_common/$frameFile$frameHash";
  }

  unset ( $frameFamily, $frameOthers, $frameList, $frameEntries, $frameApp, $frameLabel, $frameIcon,
          $frameAbout, $frameRows, $frameRow, $frameVar, $frameFile, $frameAt, $frameAlgo, $frameHash );

  return TRUE;

?>
