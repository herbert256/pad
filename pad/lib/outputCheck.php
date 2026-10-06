<?php

  // Output checks in development: after a local request has rendered, its HTML is read back
  // and four mistakes no template check can see are named - a duplicate id, an <img> without
  // alt, a form field nothing labels, and a link to a page that does not exist.
  //
  //   $padCheckOutput = TRUE;          in _config/config.php - every local HTML response
  //   ?page&padCheckOutput             one request, asked for by this machine itself
  //
  // The findings come back as a panel in the page - before </body>, or at the end of a page
  // without one - and as a PAD-Output-Check header with their number, so curl sees them too.
  // A page with nothing to report is left exactly as it was. Only a local request is checked
  // (padLocal: the command line, or loopback with nothing forwarded): a remote visitor never
  // pays for the parse and never sees the panel. The page cache is left alone as well - a
  // stored page with a panel in it would go to the next visitor - and so is the answer to
  // a live event, the inside of one region, which the panel would land in.
  //
  // The link check is exact because PAD's routing is: a ?page link is broken when padPageCheck
  // finds no page for it, in this application or, for /<mount>/<app>/?page, in the one it
  // names - the same question the router asks when the link is followed.
  //
  // padOutputCheckOn      whether this response is checked
  // padOutputCheckPage    runs the checks over a page and returns it with the panel added
  // padOutputCheck        the findings for a piece of HTML, as a list of lines
  // padOutputCheckSource  the broken links written in a template's source, for a check of
  //                       every application at once (develop/?links)
  // padOutputCheckLink    why a link is broken, or '' when it is fine or no page link at all
  // padOutputCheckTarget  the application and page a link asks for, or NULL for another URL
  // padOutputCheckPanel   the findings as a <details> panel

  function padOutputCheckOn () {

    global $padCache, $padCheckOutput, $padContentType, $padOutputType;

    if ( ! ( $padCheckOutput ?? FALSE ) and ! padSelfSwitch ( 'padCheckOutput' ) ) return FALSE;
    if ( ! padLocal ()                                                          ) return FALSE;
    if ( ( $padOutputType ?? 'web' ) != 'web'                                    ) return FALSE;
    if ( $padCache                                                               ) return FALSE;
    if ( padLive () !== ''                                                       ) return FALSE;

    return str_starts_with ( strtolower ( trim ( $padContentType ?? '' ) ), 'text/html' );

  }

  function padOutputCheckPage ( $html ) {

    $findings = padOutputCheck ( $html );

    padHeader ( 'PAD-Output-Check: ' . count ( $findings ) );

    if ( ! $findings )
      return $html;

    $panel = padOutputCheckPanel ( $findings );
    $body  = strripos ( $html, '</body>' );

    if ( $body === FALSE )
      return $html . $panel;

    return substr ( $html, 0, $body ) . $panel . substr ( $html, $body );

  }

  function padOutputCheck ( $html ) {

    $doc = padOutputCheckDocument ( $html );

    if ( ! $doc )
      return [];

    $ids = $images = $fields = $links = $labelFor = [];

    foreach ( $doc->getElementsByTagName ( 'label' ) as $label )
      if ( $label->hasAttribute ( 'for' ) )
        $labelFor [ (string) $label->getAttribute ( 'for' ) ] = TRUE;

    foreach ( $doc->getElementsByTagName ( '*' ) as $node ) {

      $tag = strtolower ( $node->localName );

      $id  = (string) $node->getAttribute ( 'id' );

      if ( $id !== '' )
        $ids [$id] = ( $ids [$id] ?? 0 ) + 1;

      if ( $tag == 'img' and ! $node->hasAttribute ( 'alt' ) )
        $images [] = '<img' . padOutputCheckName ( $node, [ 'src' ] ) . '> has no alt text';

      if ( in_array ( $tag, [ 'input', 'select', 'textarea' ] ) and ! padOutputCheckLabelled ( $node, $labelFor ) )
        $fields [] = "<$tag" . padOutputCheckName ( $node, [ 'type', 'name', 'id' ] ) . '> has no label';

      foreach ( [ 'a' => 'href', 'area' => 'href', 'form' => 'action', 'iframe' => 'src' ] as $linkTag => $attr )
        if ( $tag == $linkTag and $node->hasAttribute ( $attr ) ) {
          $why = padOutputCheckLink ( (string) $node->getAttribute ( $attr ) );
          if ( $why !== '' )
            $links [] = "<$tag $attr=\"" . (string) $node->getAttribute ( $attr ) . "\"> is a broken link: $why";
        }

    }

    $findings = [];

    foreach ( $ids as $id => $count )
      if ( $count > 1 )
        $findings [] = "the id \"$id\" is used by $count elements";

    return array_merge ( $findings, $images, $fields, $links );

  }

  // The parser: PHP 8.4's HTML5 parser where it is there, which reads a page the way a
  // browser does, and the libxml HTML 4 parser before that. Either gets a fragment - a
  // padInclude answer - its html and body around it, and neither reports what it repairs.

  function padOutputCheckDocument ( $html ) {

    if ( trim ( $html ) === '' )
      return NULL;

    if ( class_exists ( 'Dom\HTMLDocument' ) )
      return Dom\HTMLDocument::createFromString ( $html, LIBXML_NOERROR, 'UTF-8' );

    $doc = new DOMDocument ();

    $errors = libxml_use_internal_errors ( TRUE );
    $doc->loadHTML ( '<?xml encoding="UTF-8">' . $html, LIBXML_NOERROR | LIBXML_NOWARNING );
    libxml_clear_errors ();
    libxml_use_internal_errors ( $errors );

    return $doc;

  }

  // The static half, for a whole application at once: every href, action and src written
  // literally in a template - one built from a field or a tag is left out, its value is only
  // known when the page runs - judged like a link in a rendered page, a ?page against the
  // application the template belongs to. Answers one line per broken link, with its line
  // number in the source.

  function padOutputCheckSource ( $source, $app ) {

    $broken = [];

    preg_match_all ( '/\b(href|action|src)\s*=\s*(["\'])(.*?)\2/is', $source, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE );

    foreach ( $matches as $match ) {

      $url = $match [3] [0];

      if ( str_contains ( $url, '{' ) or str_contains ( $url, '@' ) or str_contains ( $url, '&open;' ) )
        continue;

      $why = padOutputCheckLink ( html_entity_decode ( $url, ENT_QUOTES | ENT_HTML5, 'UTF-8' ), $app );

      if ( $why !== '' )
        $broken [] = 'line ' . ( substr_count ( $source, "\n", 0, $match [0] [1] ) + 1 ) . ": {$match [1] [0]}=\"$url\" - $why";

    }

    return $broken;

  }

  // A field needs no label when nobody types into it - a hidden field, a button - and has one
  // when a <label for> names its id, a <label> wraps it, or it carries its own accessible
  // name. A placeholder is not a label: it is gone as soon as something is typed.

  function padOutputCheckLabelled ( $node, $labelFor ) {

    $type = strtolower ( (string) $node->getAttribute ( 'type' ) );

    if ( strtolower ( $node->localName ) == 'input' and in_array ( $type, [ 'hidden', 'submit', 'button', 'reset', 'image' ] ) )
      return TRUE;

    foreach ( [ 'aria-label', 'aria-labelledby', 'title' ] as $attr )
      if ( trim ( (string) $node->getAttribute ( $attr ) ) !== '' )
        return TRUE;

    if ( (string) $node->getAttribute ( 'id' ) !== '' and isset ( $labelFor [ (string) $node->getAttribute ( 'id' ) ] ) )
      return TRUE;

    for ( $up = $node->parentNode; $up; $up = $up->parentNode )
      if ( isset ( $up->localName ) and strtolower ( $up->localName ) == 'label' )
        return TRUE;

    return FALSE;

  }

  function padOutputCheckName ( $node, $attrs ) {

    $name = '';

    foreach ( $attrs as $attr )
      if ( $node->hasAttribute ( $attr ) )
        $name .= " $attr=\"" . (string) $node->getAttribute ( $attr ) . '"';

    return $name;

  }

  function padOutputCheckLink ( $url, $app = '' ) {

    $target = padOutputCheckTarget ( $url, $app );

    if ( $target === NULL )
      return '';

    [ $app, $page ] = $target;

    if ( ! preg_match ( '/^[a-zA-Z0-9][a-zA-Z0-9_\/-]*$/D', $app ) or str_contains ( $app, '//' )
         or str_contains ( $app, '/_' ) or ! is_dir ( APPS . $app ) )
      return "there is no application '$app'";

    if ( padPageCheck ( $page, APPS . "$app/" ) )
      return '';

    if ( $app == ( $GLOBALS ['padApp'] ?? '' ) )
      return "there is no page '$page'";

    return "there is no page '$page' in the application '$app'";

  }

  // What the router would make of a URL: ?query in this application; the host and mount
  // prefix ($padHost, $padRoot) followed by <app>/, <app>/?query or <app>/index.php?query in
  // that one. The page is the first name of the query, as inits/page.php takes it from $_GET,
  // and index when there is none. Anything else - another site, a file, a #fragment, a
  // mailto: - is no page link and is not judged.
  //
  // A rendered page reached by a clean URL - /shop/products/42 - is the exception for its
  // ?query links: the browser keeps the path, and a query that does not start with a bare
  // name, ?sort=price, is a value of the page the path names (padRouteQuery), not a page
  // called sort. It was judged as one and every such link was reported broken.

  function padOutputCheckTarget ( $url, $app = '' ) {

    global $padHost, $padRoot;

    $url   = trim ( (string) $url );
    $url   = explode ( '#', $url, 2 ) [0];
    $clean = '';

    if ( $url === '' )
      return NULL;

    if ( $url [0] == '?' ) {
      $clean = ( $app === '' ) ? (string) ( $GLOBALS ['padRoutePath'] ?? '' ) : '';
      $app   = ( $app !== '' ) ? $app : $GLOBALS ['padApp'];
      $query = substr ( $url, 1 );
    } else {

      if     ( ( $padHost ?? '' ) !== '' and str_starts_with ( $url, $padHost ) ) $path = substr ( $url, strlen ( $padHost ) );
      elseif ( $url [0] == '/' and substr ( $url, 0, 2 ) != '//' and str_starts_with ( $url, $padRoot ?? '/' ) ) $path = substr ( $url, strlen ( $padRoot ?? '/' ) );
      else return NULL;

      [ $path, $query ] = array_pad ( explode ( '?', $path, 2 ), 2, '' );

      if     ( str_ends_with ( $path, '/index.php' ) ) $app = substr ( $path, 0, -10 );
      elseif ( str_ends_with ( $path, '/'          ) ) $app = substr ( $path, 0, -1  );
      else return NULL;

      if ( $app === '' )
        return NULL;

      // A directory of www/ that is no application's entry point - www/wasm/, the index.html
      // of www/regression/ - is the web server's to serve as it is, no page link: it was
      // taken for an application and reported broken.

      $www = dirname ( APPS ) . "/www/$app";

      if ( is_dir ( $www ) and ! file_exists ( "$www/index.php" ) )
        return NULL;

    }

    $page = ( $clean !== '' ) ? $clean : 'index';

    foreach ( explode ( '&', $query ) as $part )
      if ( $part !== '' ) {
        [ $name, $value ] = array_pad ( explode ( '=', $part, 2 ), 2, '' );
        if ( $clean === '' or urldecode ( $value ) === '' )
          $page = urldecode ( $name );
        break;
      }

    return [ $app, $page ];

  }

  function padOutputCheckPanel ( $findings ) {

    $count = count ( $findings );
    $items = '';

    foreach ( $findings as $finding )
      $items .= '<li>' . htmlspecialchars ( $finding ) . '</li>';

    return '<details class="pad-check" open style="margin:1em 0;padding:.5em 1em;border:2px solid #c33;background:#fff6f6;color:#000;font:13px/1.4 monospace">'
         . '<summary>PAD output check: ' . $count . ( $count == 1 ? ' finding' : ' findings' ) . '</summary>'
         . "<ul>$items</ul></details>";

  }

?>
