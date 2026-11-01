<?php

  // Where the page stands - the breadcrumb widget: a <nav> named 'Breadcrumb' around an
  // ordered list of steps, each a link but the last, the page itself (aria-current="page").
  //
  //   {breadcrumb/}                                 the page's own path
  //   {breadcrumb}
  //     Home | ?index
  //     Kettles | ?products/kettles
  //     Aurora
  //   {/breadcrumb}
  //   {breadcrumb data='trail'/}                    rows with a label and a link
  //
  // Alone it follows the file tree, as the application does: Home, then every directory of
  // the page's path - a link to its index page where it has one - and the page last, each
  // named by its file name made readable (order_list is Order list). A page asked for by a
  // route is named as it was asked for, products/42. A pair takes the steps from its lines
  // as they rendered, a label and, after a bar, its link; data= from the rows of a store, the
  // page's array or a _data file - a row's label (or text, name) and link (or href, url).
  // The separators are drawn by the CSS, never read out. No script.
  //
  // What the trail writes is protected as a whole (padProtect): a label of a row is a value,
  // and the pair hands the trail back through $padContent, which its parent scans.
  //
  // padBreadcrumbItems  the steps: [ label as markup, link escaped or '' ] - of the rows, of
  //                     the lines of a pair, or of the page's path
  // padBreadcrumbPath   the steps of the page's path
  // padBreadcrumb       the trail
  // padBreadcrumbStyle  the CSS, www/assets/widgets/breadcrumb.css, once per page

  function padBreadcrumbItems ( $pair, $source ) {

    global $padCheckSyntax;

    $rows  = padWidgetRows ( 'breadcrumb' );
    $items = [];

    if ( $rows !== NULL ) {

      foreach ( (array) $rows as $row ) {
        if ( is_object ( $row ) )
          $row = padToArray ( $row );
        if ( ! is_array ( $row ) )
          $row = [ 'label' => $row ];
        $label = $row ['label'] ?? $row ['text'] ?? $row ['name'] ?? reset ( $row );
        $link  = $row ['link']  ?? $row ['href'] ?? $row ['url']  ?? '';
        if ( is_scalar ( $label ) and trim ( (string) $label ) !== '' )
          $items [] = [ padWidgetAttr ( trim ( (string) $label ) ), is_scalar ( $link ) ? padWidgetLink ( $link ) : '' ];
      }

    } elseif ( $pair ) {

      foreach ( trim ( $source ) === '' ? [] : preg_split ( '/\R/', trim ( $source ) ) as $line ) {
        $parts = explode ( '|', $line, 2 );
        $label = trim ( $parts [0] );
        if ( $label !== '' )
          $items [] = [ htmlspecialchars ( $label, ENT_NOQUOTES | ENT_SUBSTITUTE, 'UTF-8', FALSE ),
                        isset ( $parts [1] ) ? padWidgetLink ( $parts [1], TRUE ) : '' ];
      }

    } else

      $items = padBreadcrumbPath ();

    if ( ! $items and $padCheckSyntax )
      padError ( "the {breadcrumb} has no steps - its lines, Home | ?index, or rows by data=" );

    return $items;

  }

  function padBreadcrumbPath () {

    global $padGo, $padPage, $padPageAsked;

    $page  = trim ( ( $padPageAsked ?? '' ) !== '' ? (string) $padPageAsked : (string) $padPage, '/' );
    $parts = ( $page === '' ) ? [] : explode ( '/', $page );

    if ( end ( $parts ) === 'index' )
      array_pop ( $parts );

    $home  = padWidgetText ( 'home', 'Home' ) ?: 'Home';
    $items = [ [ padWidgetAttr ( $home ), padWidgetLink ( $padGo . 'index' ) ] ];
    $path  = '';

    // A directory links to its index page where it has one, and is a step without a link
    // where it has none; the page itself, the last step, links to itself. A link writes
    // every segment encoded (as lib/api.php does): a routed page asked as products/a%23b
    // binds a#b, and linked as it stands it took the visitor to products/a with a fragment.

    foreach ( $parts as $at => $part ) {
      $path   .= ( $path === '' ? '' : '/' ) . $part;
      $go      = $padGo . implode ( '/', array_map ( 'rawurlencode', explode ( '/', $path ) ) );
      $link    = ( $at == count ( $parts ) - 1 ) ? $go : ( padCheck ( APP . "$path/index" ) ? "$go/index" : '' );
      $items [] = [ padWidgetAttr ( ucfirst ( strtolower ( padStrHeadline ( $part ) ) ) ), $link !== '' ? padWidgetLink ( $link ) : '' ];
    }

    return $items;

  }

  function padBreadcrumb ( $items, $label ) {

    $last = count ( $items ) - 1;
    $out  = '';

    foreach ( $items as $index => [ $text, $link ] ) {

      if ( $index == $last )
        $step = ( $link !== '' ) ? "<a href=\"$link\" aria-current=\"page\">$text</a>" : "<span aria-current=\"page\">$text</span>";
      else
        $step = ( $link !== '' ) ? "<a href=\"$link\">$text</a>" : "<span>$text</span>";

      $out .= "<li class=\"pad-breadcrumb-item\">$step</li>";

    }

    return padBreadcrumbStyle ()
         . padProtect ( '<nav class="pad-breadcrumb" aria-label="' . padWidgetAttr ( $label ) . '"><ol class="pad-breadcrumb-list">' )
         . padProtect ( $out . '</ol></nav>' );

  }

  function padBreadcrumbStyle () {

    return padWidgetStyle ( 'breadcrumb',
      [ 'text'  => [ '#1f1f1d', '#ecebe6' ],
        'muted' => [ '#62615c', '#a9a8a0' ],
        'link'  => [ '#1f64b8', '#7fb2f0' ],
        'line'  => [ '#a9a8a0', '#62615c' ] ],
      padWidgetAsset ( 'breadcrumb.css' ) );

  }

?>
