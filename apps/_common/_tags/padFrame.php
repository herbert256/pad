<?php

  // The frame the PAD applications share - every one that uses _common, and admin, edit and
  // tags, which reach it with the common: prefix: a side with the brand every application
  // shows - the logo and the application's name (padBrand) - its menu under it, and the bar
  // with the page's title and the theme switcher above the page. The frame is light -
  // <html data-theme="light">, whatever the system prefers - unless a theme is chosen, Dark
  // among them; a page with a light/dark setting of its own, the editor, sets data-theme
  // through www/_common/pad.js. The bar's light/dark switch is gone: Default is light.
  // _common has no wrapper of its own: each application's _inits.pad opens the frame with
  // this tag, the parts of its own as slots, and closes it after the page with the
  // padFrameEnd snippet:
  //
  //   {common:padFrame}
  //     {slot 'head'}{asset 'admin.css', tag}{/slot}    in <head>
  //     {slot 'foot'}...{/slot}                           at the bottom of the side
  //     {slot 'tools'}...{/slot}                          in the bar, before the theme switcher
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
  // and pad.js - and the brand's brand.css and brand.js - with a version from their contents
  // as {asset} writes it.
  //
  // The theme switcher - {themes/}, the themes of pad/subs/themes/ - stands in the bar of
  // every page, at the end of the bar after the tools slot, so the visitor's
  // choice is at hand in every application; $padTheme = FALSE, which turns the themes off,
  // takes it out of the bar.
  //
  // Offline - an application packed by pad pack (padWasm) - there is no www/_common to link:
  // the stylesheet and the script are written into the page, as the brand writes its own.
  //
  // The brand top left - the {} logo, a link to the PAD home, and the application's name,
  // the pull-down of the PAD applications - is the one every application shows, the tag
  // padBrand; the frame writes it with the option frame and links its stylesheet and script
  // here, in <head>. The application's name in <title> comes from the same list.

  $frameName = basename ( $padApp );

  foreach ( include COMMON . '_data/padApps.php' as $frameEntries )
    if ( isset ( $frameEntries [$padApp] ) )
      $frameName = $frameEntries [$padApp] [0];

  $frameClass = 'app-' . str_replace ( '/', '-', $padApp );
  $frameFill  = padTagParm ( 'fill' ) ? 1 : 0;

  $frameThemes  = ( ( $GLOBALS ['padTheme'] ?? NULL ) !== FALSE ) ? 1 : 0;
  $frameOffline = padWasm () ? 1 : 0;

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

  $frameCss = $frameJs = $frameBrandCss = $frameBrandJs = '';

  foreach ( [ 'frameCss' => 'pad.css', 'frameJs' => 'pad.js', 'frameBrandCss' => 'brand.css', 'frameBrandJs' => 'brand.js' ] as $frameVar => $frameFile ) {
    $frameAt     = dirname ( APPS ) . "/www/_common/$frameFile";
    $frameAlgo   = in_array ( 'xxh128', hash_algos (), TRUE ) ? 'xxh128' : 'sha256';
    $frameHash   = is_file ( $frameAt ) ? '?v=' . substr ( (string) hash_file ( $frameAlgo, $frameAt ), 0, 10 ) : '';
    ${$frameVar} = "{$padRoot}_common/$frameFile$frameHash";
  }

  // Offline: pad.css and pad.js themselves, for a <style> and a <script> of the page.

  $frameStyle = '';

  if ( $frameOffline )
    foreach ( [ 'pad.css' => 'style', 'pad.js' => 'script' ] as $frameFile => $frameElement )
      if ( is_file ( dirname ( APPS ) . "/www/_common/$frameFile" ) )
        $frameStyle .= "<$frameElement>" . file_get_contents ( dirname ( APPS ) . "/www/_common/$frameFile" ) . "</$frameElement>";

  unset ( $frameElement, $frameEntries, $frameRows, $frameRow, $frameVar, $frameFile, $frameAt, $frameAlgo, $frameHash );

  return TRUE;

?>
