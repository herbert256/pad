<?php

  // Icons - the icon subsystem: common interface icons, drawn for PAD on a 24 by 24 grid as
  // plain lines, circles and rectangles and written as inline SVG - no icon font, no sprite
  // file, no request. An icon is a file of icons/types/, <name>.svg: a whole SVG, which a
  // browser or an editor shows as it is, whose drawing is what stands inside its svg element.
  // The files there are the icons there are (padIconNames, lib/icon.php): an icon is added by
  // adding its file.
  //
  //   {icon 'arrow-right'/}
  //   {icon 'trash', size=16, label='Delete'/}
  //   {icon:star size=20/}
  //
  // padIcon         the SVG of an icon, or '' when there is no icon of that name
  // padIconBody     the drawing of an icon, read from its file
  // padIconSimilar  the names that look like one that is not there - for the error message
  //
  // The lines are stroke="currentColor", so an icon takes the colour of the text around it,
  // and 2 units wide with round ends - stroke= changes the width. An icon is decoration -
  // aria-hidden, out of the reading order - unless label= names it: then it is role="img"
  // with that name, for an icon that stands alone in a button or a link. The svg element
  // the tag writes is its own - the size, the stroke, the class and the role - so the
  // attributes of the file's own svg element are left where they are.

  function padIcon ( $name, $size = 20, $label = '', $stroke = 2 ) {

    $body = padIconBody ( $name );

    if ( $body === '' )
      return '';

    $size   = max ( 8, (int) $size );
    $stroke = round ( max ( 0.5, min ( 4, (float) $stroke ) ), 2 );
    $label  = trim ( (string) $label );

    // Named, the icon is an image with that name; unnamed, decoration a screen reader skips.

    $role = ( $label !== '' )
          ? ' role="img" aria-label="' . padIconAttr ( $label ) . '"'
          : ' aria-hidden="true" focusable="false"';

    $title = ( $label !== '' ) ? '<title>' . padIconAttr ( $label ) . '</title>' : '';

    return '<svg xmlns="http://www.w3.org/2000/svg" class="pad-icon pad-icon-' . $name . "\"$role width=\"$size\" height=\"$size\" viewBox=\"0 0 24 24\""
         . " fill=\"none\" stroke=\"currentColor\" stroke-width=\"$stroke\" stroke-linecap=\"round\" stroke-linejoin=\"round\">"
         . $title . $body . '</svg>';

  }

  // The drawing of an icon: what its file holds between the svg element's opening tag and
  // its end, the comments and the white space between the elements taken out - a file writes
  // an element to a line. '' for a name that is no file of icons/types/: the name is looked
  // up in the directory's listing (padKind), never put in a path. Each icon is read once per
  // request.

  function padIconBody ( $name ) {

    static $bodies = [];

    if ( isset ( $bodies [$name] ) )
      return $bodies [$name];

    if ( ! padKind ( 'icons', $name ) )
      return '';

    $svg   = (string) padFileGet ( PAD . "icons/types/$name.svg" );
    $at    = strpos ( $svg, '<svg' );
    $open  = ( $at === FALSE ) ? FALSE : strpos ( $svg, '>', $at );
    $close = strrpos ( $svg, '</svg>' );

    if ( $open === FALSE or $close === FALSE or $close < $open )
      return $bodies [$name] = '';

    $inner = preg_replace ( '/<!--.*?-->/s', '', substr ( $svg, $open + 1, $close - $open - 1 ) );

    return $bodies [$name] = preg_replace ( '/>\s+</', '><', trim ( $inner ) );

  }

  // The names close to one that is not there: a small edit distance, or a name that holds
  // the one asked for - 'arow-left' finds arrow-left, 'chevron' the chevrons. At most five
  // are named, the closest first.

  function padIconSimilar ( $name ) {

    $name   = strtolower ( (string) $name );
    $scored = [];

    foreach ( padIconNames () as $icon ) {
      $distance = levenshtein ( $name, $icon );
      if ( $name !== '' and ( str_starts_with ( $icon, $name ) or str_contains ( $icon, $name ) ) )
        $distance = min ( $distance, 1 );
      if ( $distance <= max ( 1, intdiv ( strlen ( $name ), 3 ) ) )
        $scored [$icon] = $distance;
    }

    asort ( $scored );

    return array_slice ( array_keys ( $scored ), 0, 5 );

  }

  function padIconAttr ( $text ) {

    return htmlspecialchars ( padUnprotectQuotes ( (string) $text ), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8' );

  }

?>
