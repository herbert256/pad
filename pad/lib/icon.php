<?php

  // Icons - the {icon} tag. A set of common interface icons, drawn for PAD on a 24 by 24
  // grid as plain lines, circles and rectangles, written as inline SVG: no icon font, no
  // sprite file, no request.
  //
  //   {icon 'arrow-right'}
  //   {icon 'trash', size=16, label='Delete'}
  //
  // padIcon         the SVG of an icon, or '' when there is no icon of that name
  // padIconNames    the names, in the order of PAD_ICONS
  // padIconSimilar  the names that look like one that is not there - for the error message
  //
  // The lines are stroke="currentColor", so an icon takes the colour of the text around it,
  // and 2 units wide with round ends - stroke= changes the width. An icon is decoration -
  // aria-hidden, out of the reading order - unless label= names it: then it is role="img"
  // with that name, for an icon that stands alone in a button or a link.

  const PAD_ICONS = [

    // Arrows and chevrons

    'arrow-right'     => '<path d="M5 12h14M13 6l6 6-6 6"/>',
    'arrow-left'      => '<path d="M19 12H5M11 6l-6 6 6 6"/>',
    'arrow-up'        => '<path d="M12 19V5M6 11l6-6 6 6"/>',
    'arrow-down'      => '<path d="M12 5v14M6 13l6 6 6-6"/>',
    'chevron-right'   => '<path d="M9.5 6l6 6-6 6"/>',
    'chevron-left'    => '<path d="M14.5 6l-6 6 6 6"/>',
    'chevron-up'      => '<path d="M6 14.5l6-6 6 6"/>',
    'chevron-down'    => '<path d="M6 9.5l6 6 6-6"/>',
    'external'        => '<path d="M14 4h6v6M20 4l-9 9M18 14v5a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h5"/>',
    'refresh'         => '<path d="M19.5 12a7.5 7.5 0 1 1-2.2-5.3M17.3 2.7v4h-4"/>',
    'sort'            => '<path d="M8 4v16M4.5 16.5L8 20l3.5-3.5M16 20V4M12.5 7.5L16 4l3.5 3.5"/>',
    'maximize'        => '<path d="M4 9V4h5M20 9V4h-5M4 15v5h5M20 15v5h-5"/>',
    'minimize'        => '<path d="M9 4v5H4M15 4v5h5M9 20v-5H4M15 20v-5h5"/>',

    // Marks

    'check'           => '<path d="M5 12.5l4.5 4.5L19 7.5"/>',
    'x'               => '<path d="M6 6l12 12M18 6L6 18"/>',
    'plus'            => '<path d="M12 5v14M5 12h14"/>',
    'minus'           => '<path d="M5 12h14"/>',
    'check-circle'    => '<circle cx="12" cy="12" r="9"/><path d="M8 12.3l2.7 2.7L16 9.7"/>',
    'x-circle'        => '<circle cx="12" cy="12" r="9"/><path d="M9 9l6 6M15 9l-6 6"/>',
    'info'            => '<circle cx="12" cy="12" r="9"/><path d="M12 11v5.5M12 7.5v.01"/>',
    'alert'           => '<path d="M12 3.5L21.5 20h-19Z"/><path d="M12 10v4.5M12 17.3v.01"/>',
    'help'            => '<circle cx="12" cy="12" r="9"/><path d="M9.5 9.5a2.5 2.5 0 1 1 3.5 2.3c-.6.3-1 .8-1 1.5v.7M12 17v.01"/>',
    'star'            => '<path d="M12 3L14.47 9.2L21.13 9.63L15.99 13.9L17.64 20.37L12 16.8L6.36 20.37L8.01 13.9L2.87 9.63L9.53 9.2Z"/>',
    'heart'           => '<path d="M12 20C7.2 16.4 3.2 13 3.2 8.9 3.2 6.3 5.2 4.3 7.7 4.3c1.8 0 3.3 1 4.3 2.5 1-1.5 2.5-2.5 4.3-2.5 2.5 0 4.5 2 4.5 4.6 0 4.1-4 7.5-8.8 11.1Z"/>',
    'bookmark'        => '<path d="M6.5 3.5h11v17l-5.5-4-5.5 4Z"/>',
    'flag'            => '<path d="M5 21V4M5 4.5h12l-2.5 4 2.5 4H5"/>',
    'tag'             => '<path d="M3.5 4.5v6.3a1 1 0 0 0 .3.7l8.7 8.7a1 1 0 0 0 1.4 0l6.3-6.3a1 1 0 0 0 0-1.4L11.5 3.8a1 1 0 0 0-.7-.3H4.5a1 1 0 0 0-1 1Z"/><path d="M8 8v.01"/>',
    'zap'             => '<path d="M13.5 2.5L5 13.5h6.5l-1 8 8.5-11h-6.5Z"/>',

    // Navigation and layout

    'home'            => '<path d="M4 11l8-7 8 7M6 9.5V20h12V9.5M10 20v-6h4v6"/>',
    'menu'            => '<path d="M4 7h16M4 12h16M4 17h16"/>',
    'grid'            => '<rect x="4" y="4" width="6.5" height="6.5" rx="1"/><rect x="13.5" y="4" width="6.5" height="6.5" rx="1"/><rect x="4" y="13.5" width="6.5" height="6.5" rx="1"/><rect x="13.5" y="13.5" width="6.5" height="6.5" rx="1"/>',
    'list'            => '<path d="M9 6h11M9 12h11M9 18h11M4.5 6v.01M4.5 12v.01M4.5 18v.01"/>',
    'more-horizontal' => '<circle cx="5.5" cy="12" r="1"/><circle cx="12" cy="12" r="1"/><circle cx="18.5" cy="12" r="1"/>',
    'more-vertical'   => '<circle cx="12" cy="5.5" r="1"/><circle cx="12" cy="12" r="1"/><circle cx="12" cy="18.5" r="1"/>',
    'search'          => '<circle cx="10.5" cy="10.5" r="6.5"/><path d="M15.5 15.5L20 20"/>',
    'filter'          => '<path d="M4 5h16l-6 7.5V19l-4 1.5v-8Z"/>',
    'settings'        => '<path d="M4 7h3M11 7h9M4 12h9M17 12h3M4 17h1M9 17h11"/><circle cx="9" cy="7" r="2"/><circle cx="15" cy="12" r="2"/><circle cx="7" cy="17" r="2"/>',
    'log-in'          => '<path d="M14.5 3.5H19a1 1 0 0 1 1 1v15a1 1 0 0 1-1 1h-4.5M4 12h10M10 8l4 4-4 4"/>',
    'log-out'         => '<path d="M9.5 20.5H5a1 1 0 0 1-1-1v-15a1 1 0 0 1 1-1h4.5M10 12h10M16 8l4 4-4 4"/>',
    'link'            => '<path d="M10 14a4 4 0 0 0 5.7 0l3-3a4 4 0 0 0-5.7-5.7l-1.2 1.2M14 10a4 4 0 0 0-5.7 0l-3 3a4 4 0 0 0 5.7 5.7l1.2-1.2"/>',

    // People and messages

    'user'            => '<circle cx="12" cy="8" r="4"/><path d="M4.5 20c0-4 3.4-6.5 7.5-6.5s7.5 2.5 7.5 6.5"/>',
    'users'           => '<circle cx="9" cy="8.5" r="3.5"/><path d="M3 20c0-3.5 2.7-5.5 6-5.5s6 2 6 5.5M15.5 5.2a3.5 3.5 0 0 1 0 6.6M17.5 14.7c2.1.6 3.5 2.4 3.5 5.3"/>',
    'mail'            => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3.5 6.5l8.5 6.5 8.5-6.5"/>',
    'message'         => '<path d="M4 5h16a1 1 0 0 1 1 1v10a1 1 0 0 1-1 1h-9l-5 4v-4H4a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1Z"/>',
    'send'            => '<path d="M21 3L10 14M21 3l-6.5 18-4.5-7-7-4.5Z"/>',
    'share'           => '<circle cx="18" cy="5.5" r="2.5"/><circle cx="6" cy="12" r="2.5"/><circle cx="18" cy="18.5" r="2.5"/><path d="M8.2 10.8l7.6-4.1M8.2 13.2l7.6 4.1"/>',
    'phone'           => '<rect x="7" y="3" width="10" height="18" rx="2"/><path d="M11 18h2"/>',
    'bell'            => '<path d="M6 16.5V11a6 6 0 0 1 12 0v5.5l1.5 1.5h-15Z"/><path d="M10 20.5h4"/>',

    // Time and places

    'calendar'        => '<rect x="3.5" y="5" width="17" height="15" rx="2"/><path d="M3.5 10h17M8 3v4M16 3v4"/>',
    'clock'           => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3.5 2"/>',
    'globe'           => '<circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3c2.5 2.5 3.8 5.5 3.8 9s-1.3 6.5-3.8 9c-2.5-2.5-3.8-5.5-3.8-9S9.5 5.5 12 3Z"/>',
    'map-pin'         => '<path d="M12 21s-6.5-6-6.5-11a6.5 6.5 0 0 1 13 0c0 5-6.5 11-6.5 11Z"/><circle cx="12" cy="10" r="2.3"/>',
    'sun'             => '<circle cx="12" cy="12" r="4"/><path d="M18.5 12L21 12M16.6 16.6L18.36 18.36M12 18.5L12 21M7.4 16.6L5.64 18.36M5.5 12L3 12M7.4 7.4L5.64 5.64M12 5.5L12 3M16.6 7.4L18.36 5.64"/>',
    'moon'            => '<path d="M19.5 14.5A8 8 0 1 1 9.5 4.5a6.5 6.5 0 0 0 10 10Z"/>',
    'cloud'           => '<path d="M7 18.5a4 4 0 0 1-.6-8A5.5 5.5 0 0 1 17 9a3.8 3.8 0 0 1 .5 9.5Z"/>',

    // Files and actions

    'file'            => '<path d="M6 3h8l5 5v12a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1Z"/><path d="M14 3v5h5"/>',
    'folder'          => '<path d="M3 7a1 1 0 0 1 1-1h5l2 2h9a1 1 0 0 1 1 1v9a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1Z"/>',
    'copy'            => '<rect x="8.5" y="8.5" width="12" height="12" rx="2"/><path d="M15.5 8.5V5.5a2 2 0 0 0-2-2h-8a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h3"/>',
    'edit'            => '<path d="M4 20l1.2-4.6L15.6 5a2 2 0 0 1 2.9 0l.5.5a2 2 0 0 1 0 2.9L8.6 18.8Z"/><path d="M14 6.5l3.5 3.5"/>',
    'trash'           => '<path d="M4 7h16M9 7V4.5h6V7M6.5 7l1 13h9l1-13M10 11v5M14 11v5"/>',
    'download'        => '<path d="M12 4v11M7 10.5l5 5 5-5M5 20h14"/>',
    'upload'          => '<path d="M12 16V5M7 9.5l5-5 5 5M5 20h14"/>',
    'printer'         => '<path d="M7 9V3.5h10V9M7 17H4.5a1 1 0 0 1-1-1v-6a1 1 0 0 1 1-1h15a1 1 0 0 1 1 1v6a1 1 0 0 1-1 1H17"/><rect x="7" y="14" width="10" height="6.5"/>',
    'image'           => '<rect x="3" y="4.5" width="18" height="15" rx="2"/><circle cx="8.5" cy="9.5" r="1.5"/><path d="M4 18l5-5 4 4 2.5-2.5L20 18"/>',
    'camera'          => '<path d="M4 8h3l1.5-2.5h7L17 8h3a1 1 0 0 1 1 1v10a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V9a1 1 0 0 1 1-1Z"/><circle cx="12" cy="13.5" r="3.5"/>',
    'cart'            => '<path d="M3 4h2.5l2.2 11h10.6l2-8H6.5"/><circle cx="9" cy="19.5" r="1.3"/><circle cx="17" cy="19.5" r="1.3"/>',

    // Media

    'play'            => '<path d="M8 5v14l11-7Z"/>',
    'pause'           => '<path d="M8 5v14M16 5v14"/>',
    'stop'            => '<rect x="6" y="6" width="12" height="12" rx="1.5"/>',

    // Security

    'lock'            => '<rect x="5" y="11" width="14" height="10" rx="2"/><path d="M8 11V8a4 4 0 0 1 8 0v3M12 15v2"/>',
    'unlock'          => '<rect x="5" y="11" width="14" height="10" rx="2"/><path d="M8 11V8a4 4 0 0 1 7.7-1.5M12 15v2"/>',
    'key'             => '<circle cx="8" cy="15.5" r="4.5"/><path d="M11.2 12.3L20 3.5M16.5 7l3 3M14 9.5l2 2"/>',
    'eye'             => '<path d="M2.5 12S6 5.5 12 5.5 21.5 12 21.5 12 18 18.5 12 18.5 2.5 12 2.5 12Z"/><circle cx="12" cy="12" r="3"/>',
    'eye-off'         => '<path d="M2.5 12S6 5.5 12 5.5 21.5 12 21.5 12 18 18.5 12 18.5 2.5 12 2.5 12Z"/><circle cx="12" cy="12" r="3"/><path d="M4 4l16 16"/>',
    'power'           => '<path d="M12 3v9M7 5.8a8 8 0 1 0 10 0"/>',

    // Code and data

    'code'            => '<path d="M8.5 7L3.5 12l5 5M15.5 7l5 5-5 5M13.5 5l-3 14"/>',
    'terminal'        => '<rect x="3" y="4.5" width="18" height="15" rx="2"/><path d="M7 9.5l3 2.5-3 2.5M12.5 15h4.5"/>',
    'database'        => '<ellipse cx="12" cy="6" rx="7" ry="2.5"/><path d="M5 6v12c0 1.4 3.1 2.5 7 2.5s7-1.1 7-2.5V6M5 12c0 1.4 3.1 2.5 7 2.5s7-1.1 7-2.5"/>',
    'bar-chart'       => '<path d="M4 20h16M7 20v-6M12 20V6M17 20v-9"/>' ];

  function padIcon ( $name, $size = 20, $label = '', $stroke = 2 ) {

    if ( ! isset ( PAD_ICONS [$name] ) )
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
         . $title . PAD_ICONS [$name] . '</svg>';

  }

  function padIconNames () {

    return array_keys ( PAD_ICONS );

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

    return htmlspecialchars ( (string) $text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8' );

  }

?>
