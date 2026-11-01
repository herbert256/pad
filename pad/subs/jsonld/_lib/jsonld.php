<?php

  // JSON-LD - the structured data of a page: one <script type="application/ld+json"> holding
  // a Schema.org object, which search engines read for a rich result - an article's headline
  // and date, an event's place and tickets, a product's price, the questions of an FAQ, the
  // trail of a breadcrumb. A kind is a file of subs/jsonld/types/, which answers how its
  // fields map onto Schema.org:
  //
  //   return [ 'type'    => 'Event',
  //            'fields'  => [ 'name'   => [ 'name',            'text', TRUE ],
  //                           'starts' => [ 'startDate',       'date', TRUE ],
  //                           'place'  => [ 'location.name',   'text'       ],
  //                           'price'  => [ 'offers.price',    'price'      ] ],
  //            'objects' => [ 'location' => 'Place', 'offers' => 'Offer' ],
  //            'needs'   => [ 'price' => 'currency' ] ];
  //
  // A field is [ the Schema.org path, how its value is written, required, other names, a
  // default ]: a dotted path puts it in an object of the type 'objects' names, made when one
  // of its fields has a value - an author is { "@type": "Person", "name": ... }. A field is
  // read by its PAD name and by its other names - those the file lists, and the Schema.org
  // property itself where the path has no dot, startDate for starts - as an option of the tag
  // or a field of a row. 'needs' says a field that is no use without another: a price
  // without its currency. A kind that lists - faq, breadcrumb - has 'items' instead: its rows
  // are the items, each an object of its own under the property 'items' names, numbered
  // with a position when it says so; 'trail' says the kind without rows is the trail the
  // breadcrumb widget shows for the page.
  //
  // Only what a kind names is written: a row is never written whole, so a field of a row no
  // kind maps - a password hash, an e-mail address, a cost price - can never reach the page.
  //
  // How a value is written:
  //
  //   text          the text, trimmed
  //   url           an absolute address: one of http or https as it is, //host/x with the
  //                 scheme of the page, /x on this host, and ?page, #part or a path relative
  //                 to the application's address (padJsonLdUrl) - another scheme,
  //                 javascript: or data:, is no address and is left out, as a link of a
  //                 widget is (padWidgetLink)
  //   urls          a list of those - an array, or a text with a comma between them
  //   date          a date or a date and time: ISO 8601 passes as it is - 2026-11-20,
  //                 2026-11-20T19:30, 2026-11-20T19:30:00+01:00 - a text PHP's date_parse
  //                 reads in full as it is written in ISO 8601 (20 November 2026 19:30 is
  //                 2026-11-20T19:30:00, with its offset when it has a zone - the name of
  //                 its day beside it, Fri, 20 Nov 2026, as an RSS pubDate has it, when it
  //                 is the date's own), a date object with its offset, an integer a Unix
  //                 time in UTC; a relative date (tomorrow, +1 week, next friday) is none -
  //                 its value would change from day to day
  //   price         a number with a dot as written - 19.95 - kept as its text
  //   currency      a code of ISO 4217, three letters, EUR
  //   availability  one of the item availabilities of Schema.org - InStock, OutOfStock,
  //                 PreOrder ... in any case or spacing, in stock - as its address
  //   search        the address of the site's search, the term at its end - ?search&q= -
  //                 written as a SearchAction whose urlTemplate ends in {search_term_string},
  //                 braces that stay text; an address that holds {search_term_string}
  //                 itself, from a PHP value, keeps it where it stands
  //
  // An empty value - nothing, '', a boolean, a list without anything in it - is no value, and
  // the field is left out; 0 is a value, and a bare option, {jsonld:article headline/}, none.
  // A required field without a value is an error under the strict check naming the field and
  // the kind, and without it the object is left out - with it the whole script when it was
  // the only one. A value that cannot be written as its field asks - a date that is none - is
  // an error under the strict check, and left out without it.
  //
  // The JSON is encoded for the script element it stands in: JSON_HEX_TAG writes < and > as
  // \u003C and \u003E, so a value holding </script> or <!-- cannot end the element or change
  // how the browser reads it, and slashes and Unicode stay as they are. The values are taken
  // out of PAD's stand-ins first (padUnprotect): the script is the tag's value, its braces
  // and quotes carried as stand-ins and put back when the page is written out, so a stand-in
  // left inside a value would come out as a real quote and end its JSON string. No CSP nonce:
  // a script of another type than JavaScript is a data block, which the browser never runs,
  // and a policy's script-src is checked only for a script that runs.
  //
  // padJsonLdSpec   a kind's answer, its file read once a request
  // padJsonLdMake   the object of a kind from its rows and the fields given beside them
  // padJsonLdRows   the rows of a data= value, a row of fields made a list of one
  // padJsonLdPlain  the plain values of rows the engine made rows of one field
  // padJsonLdUrl    an address made absolute, '' for one that is none
  // padJsonLdHome   the application's own address
  // padJsonLdScript the script element of an object

  const padJsonLdAvailability = [ 'BackOrder', 'Discontinued', 'InStock', 'InStoreOnly', 'LimitedAvailability', 'MadeToOrder',
                                  'OnlineOnly', 'OutOfStock', 'PreOrder', 'PreSale', 'Reserved', 'SoldOut' ];

  function padJsonLdSpec ( $kind ) {

    static $specs = [];

    if ( isset ( $specs [$kind] ) )
      return $specs [$kind];

    $spec = include PAD . "subs/jsonld/types/$kind.php";

    $spec ['fields']  = padJsonLdFields ( $spec ['fields'] ?? [] );
    $spec ['objects'] = $spec ['objects'] ?? [];
    $spec ['needs']   = $spec ['needs']   ?? [];

    if ( isset ( $spec ['items'] ) )
      $spec ['items'] ['fields'] = padJsonLdFields ( $spec ['items'] ['fields'] ?? [] );

    return $specs [$kind] = $spec;

  }

  // The fields of a kind with every name each is read by: its PAD name, the names its file
  // lists, and the Schema.org property where the path has no dot - each name once.

  function padJsonLdFields ( $fields ) {

    $done = [];

    foreach ( $fields as $name => $field ) {
      $names = array_merge ( [ $name ], $field [3] ?? [] );
      if ( ! str_contains ( $field [0], '.' ) and ! isset ( $fields [ $field [0] ] ) )
        $names [] = $field [0];
      $done [$name] = [ 'path' => $field [0], 'how' => $field [1], 'required' => $field [2] ?? FALSE,
                        'names' => array_values ( array_unique ( $names ) ), 'default' => $field [4] ?? NULL ];
    }

    return $done;

  }

  // The fields given beside the rows - as options of the tag - read with padTagParm, every
  // name of each, so an option the kind does not take is left unread for the strict check to
  // name. A value is read as the characters it holds (padParmText): a field inside a quoted
  // option arrives escaped by its sanitize step.

  function padJsonLdOptions ( $spec ) {

    $given = [];

    foreach ( $spec ['fields'] as $name => $field )
      foreach ( $field ['names'] as $one ) {
        $value = padTagParm ( $one, NULL );
        if ( ! padJsonLdEmpty ( $value ) and ! isset ( $given [$name] ) )
          $given [$name] = padJsonLdText ( $value, TRUE );
      }

    return $given;

  }

  // A value as plain characters: out of PAD's stand-ins, and - from an option - out of the
  // HTML escaping a field's sanitize step gave it. A list the same, element by element; an
  // object as the array it holds; a date object and a number as they are.

  function padJsonLdText ( $value, $option = FALSE ) {

    if ( is_string ( $value ) )
      return $option ? padParmText ( $value ) : padUnprotect ( $value );

    if ( $value instanceof DateTimeInterface )
      return $value;

    if ( $value instanceof Stringable )
      return padJsonLdText ( (string) $value, $option );

    if ( is_object ( $value ) )
      $value = padToArray ( $value );

    if ( is_array ( $value ) )
      return array_map ( fn ( $one ) => padJsonLdText ( $one, $option ), $value );

    return $value;

  }

  function padJsonLdEmpty ( $value ) {

    if ( $value === NULL or is_bool ( $value ) )
      return TRUE;

    if ( is_string ( $value ) )
      return trim ( $value ) === '';

    if ( is_array ( $value ) ) {
      foreach ( $value as $one )
        if ( ! padJsonLdEmpty ( $one ) )
          return FALSE;
      return TRUE;
    }

    return FALSE;

  }

  // The rows of a data= value: a list of rows as it is, its keys left behind; a row of
  // fields - an array with a name for a key and a value that is no row, what db() answers
  // for a RECORD - a list of that one row; an object as its array. A list of plain values is
  // a row each, the value of the kind's first field (padJsonLdRow). Nothing else holds rows.

  function padJsonLdRows ( $data ) {

    if ( is_object ( $data ) )
      $data = padToArray ( $data );

    if ( ! is_array ( $data ) or ! $data )
      return [];

    foreach ( $data as $key => $row )
      if ( is_string ( $key ) and ! is_array ( $row ) and ! is_object ( $row ) )
        return [ $data ];

    return array_values ( array_map ( fn ( $row ) => is_object ( $row ) ? padToArray ( $row ) : $row, $data ) );

  }

  // The plain values of a list as the engine holds them: padData makes each a row of one
  // field named for the store, the _data file, toData= or the tag ($names) - a {data} store,
  // a sequence store, data=['a', 'b'] and the rows the handling options give back hold them
  // so. Such a row is its value again, the value of the kind's first field (padJsonLdRow), as
  // the breadcrumb widget takes it - unless its one field is a name the kind reads.

  function padJsonLdPlain ( $spec, $rows, $names ) {

    $known = array_merge ( ...array_values ( array_column ( $spec ['items'] ['fields'] ?? $spec ['fields'], 'names' ) ) );
    $names = array_filter ( $names, fn ( $one ) => is_string ( $one ) and $one !== '' );

    foreach ( $rows as $at => $row )
      if ( is_array ( $row ) and count ( $row ) == 1 and ! is_array ( reset ( $row ) ) and ! is_object ( reset ( $row ) )
           and in_array ( (string) array_key_first ( $row ), $names, TRUE ) and ! in_array ( (string) array_key_first ( $row ), $known, TRUE ) )
        $rows [$at] = reset ( $row );

    return $rows;

  }

  // The object a kind makes - the plain array json_encode writes - or FALSE when there is
  // none: a required field without a value, no rows. $rows is NULL when no rows were given,
  // else a list of rows (padJsonLdRows); $given the fields given beside them, which win over
  // a row's field of the same name; $id the @id. $report hands an author's error on - the
  // strict check's padError for the tag, always padError for padJsonLd.
  //
  // A kind of one object makes one per row - or one of the given fields alone, without rows
  // - and with more than one row a @graph of them. A kind that lists makes one object, its
  // items from the rows, or from the page's trail.

  function padJsonLdMake ( $kind, $rows, $given, $id, $report ) {

    $spec = padJsonLdSpec ( $kind );

    if ( isset ( $spec ['items'] ) ) {

      if ( $rows === NULL and ( $spec ['trail'] ?? FALSE ) )
        $rows = padJsonLdTrail ();

      if ( $rows === NULL ) {
        $report ( "the JSON-LD $kind takes its rows from data= - rows with " . implode ( ' and ', array_keys ( array_filter ( $spec ['items'] ['fields'], fn ( $one ) => $one ['required'] ) ) ) );
        return FALSE;
      }

      $object = padJsonLdObject ( $kind, $spec, $given, $id, $report );

      if ( $object === FALSE )
        return FALSE;

      $items = [];

      foreach ( $rows as $at => $row ) {
        $item = padJsonLdItem ( $kind, $spec ['items'], $row, $at + 1, count ( $items ) + 1, $report );
        if ( $item !== FALSE )
          $items [] = $item;
      }

      if ( ! $items )
        return FALSE;

      $object [ $spec ['items'] ['path'] ] = $items;

      return [ '@context' => 'https://schema.org' ] + $object;

    }

    if ( $rows !== NULL and count ( $rows ) > 1 and ! padJsonLdEmpty ( $id ) ) {
      $report ( "the JSON-LD $kind has an id= and " . count ( $rows ) . " rows - an @id names one object" );
      $id = NULL;
    }

    $objects = [];

    foreach ( $rows ?? [ [] ] as $row ) {
      $object = padJsonLdObject ( $kind, $spec, $given + padJsonLdRow ( $spec ['fields'], $row ), $id, $report );
      if ( $object !== FALSE )
        $objects [] = $object;
    }

    if ( ! $objects )
      return FALSE;

    if ( count ( $objects ) == 1 )
      return [ '@context' => 'https://schema.org' ] + $objects [0];

    return [ '@context' => 'https://schema.org', '@graph' => $objects ];

  }

  // The fields of a row the kind maps, by the first of their names the row has a value for.
  // A row that is no array is the value of the kind's first field - a breadcrumb's name.

  function padJsonLdRow ( $fields, $row ) {

    if ( ! is_array ( $row ) )
      $row = [ array_key_first ( $fields ) => $row ];

    $found = [];

    foreach ( $fields as $name => $field )
      foreach ( $field ['names'] as $one )
        if ( array_key_exists ( $one, $row ) and ! padJsonLdEmpty ( $row [$one] ) ) {
          $found [$name] = padJsonLdText ( $row [$one] );
          break;
        }

    return $found;

  }

  // One object of a kind from the values of its fields: its @type, its @id, and every field
  // with a value written where its path says. FALSE when a required field has none.

  function padJsonLdObject ( $kind, $spec, $values, $id, $report ) {

    $object = [ '@type' => $spec ['type'] ];

    if ( ! padJsonLdEmpty ( $id ) ) {
      $id = padJsonLdUrl ( padJsonLdScalar ( $id ), TRUE );
      if ( $id !== '' )
        $object ['@id'] = $id;
    }

    $ok = padJsonLdFill ( $object, $spec ['fields'], $spec ['objects'], $values, "the JSON-LD $kind", TRUE, $report );

    foreach ( $spec ['needs'] as $field => $need )
      if ( isset ( $values [$field] ) and ! isset ( $values [$need] ) ) {
        $report ( "the JSON-LD $kind has a $field and no $need - give it as $need= or as a field $need of the row" );
        $ok = FALSE;
      }

    return $ok ? $object : FALSE;

  }

  // One item of a kind that lists, from a row: its @type, its position when the kind numbers
  // them, its fields. FALSE when a required field has no value.

  function padJsonLdItem ( $kind, $items, $row, $number, $position, $report ) {

    $item = [ '@type' => $items ['type'] ];

    if ( $items ['position'] ?? FALSE )
      $item ['position'] = $position;

    $ok = padJsonLdFill ( $item, $items ['fields'], $items ['objects'] ?? [], padJsonLdRow ( $items ['fields'], $row ),
                          "row $number of the JSON-LD $kind", FALSE, $report );

    return $ok ? $item : FALSE;

  }

  // Writes the fields with a value into the object, in the order the kind lists them, each
  // object on its path made with its @type first. FALSE when a required field has no value -
  // the others are written all the same, so every error of the object is named. $options
  // says the fields could have been options of the tag, as an item's cannot.

  function padJsonLdFill ( &$object, $fields, $objects, $values, $what, $options, $report ) {

    $ok = TRUE;

    foreach ( $fields as $name => $field ) {

      $value = $values [$name] ?? $field ['default'];
      $value = padJsonLdEmpty ( $value ) ? NULL : padJsonLdValue ( $value, $field ['how'], $name, $what, $report );

      if ( $value === NULL ) {
        if ( $field ['required'] ) {
          $other = array_values ( array_diff ( $field ['names'], [ $name ] ) );
          $report ( "$what has no $name" . ( $other ? ' (' . implode ( ', ', $other ) . ')' : '' )
                    . ( $options ? " - give it as $name= or as a field $name of the row" : " - the row needs a field $name" ) );
          $ok = FALSE;
        }
        continue;
      }

      $at   = &$object;
      $path = explode ( '.', $field ['path'] );
      $last = array_pop ( $path );
      $done = '';

      foreach ( $path as $step ) {
        $done .= ( $done === '' ? '' : '.' ) . $step;
        if ( ! isset ( $at [$step] ) )
          $at [$step] = isset ( $objects [$done] ) ? [ '@type' => $objects [$done] ] : [];
        $at = &$at [$step];
      }

      $at [$last] = $value;

      unset ( $at );

    }

    return $ok;

  }

  // A value written as its field asks - NULL when there is nothing to write, after an error
  // for a value that cannot be written so.

  function padJsonLdValue ( $value, $how, $name, $what, $report ) {

    if ( $how == 'urls' ) {
      $list = is_array ( $value ) ? $value : preg_split ( '/\s*,\s*/', (string) padJsonLdScalar ( $value ) );
      $list = array_values ( array_filter ( array_map ( fn ( $one ) => is_scalar ( $one ) ? padJsonLdUrl ( (string) $one ) : '', $list ), 'strlen' ) );
      return $list ?: NULL;
    }

    if ( is_array ( $value ) ) {
      $report ( "the $name of $what is a list, not a value" );
      return NULL;
    }

    if ( $how == 'date' ) {
      $date = padJsonLdDate ( $value );
      if ( $date === FALSE )
        $report ( "the $name of $what is no date: '" . padMakeSafe ( padJsonLdScalar ( $value ), 40 ) . "' - write 2026-11-20 or 2026-11-20T19:30:00+01:00" );
      return ( $date === FALSE ) ? NULL : $date;
    }

    $value = trim ( (string) padJsonLdScalar ( $value ) );

    switch ( $how ) {

      case 'url':
        return padJsonLdUrl ( $value ) ?: NULL;

      case 'search':
        $target = padJsonLdUrl ( $value );
        if ( $target === '' )
          return NULL;
        if ( ! str_contains ( $target, '{search_term_string}' ) )
          $target .= '{search_term_string}';
        return [ '@type'       => 'SearchAction',
                 'target'      => [ '@type' => 'EntryPoint', 'urlTemplate' => $target ],
                 'query-input' => 'required name=search_term_string' ];

      case 'price':
        if ( preg_match ( '/^-?[0-9]+(\.[0-9]+)?$/D', $value ) )
          return $value;
        $report ( "the $name of $what is no number: '" . padMakeSafe ( $value, 40 ) . "' - write 19.95, with a dot" );
        return NULL;

      case 'currency':
        if ( preg_match ( '/^[A-Za-z]{3}$/D', $value ) )
          return strtoupper ( $value );
        $report ( "the $name of $what is no currency code of ISO 4217: '" . padMakeSafe ( $value, 40 ) . "' - EUR, USD, GBP ..." );
        return NULL;

      case 'availability':
        $word = strtolower ( preg_replace ( '#^https?://schema\.org/|[^A-Za-z]#', '', $value ) );
        foreach ( padJsonLdAvailability as $one )
          if ( strtolower ( $one ) === $word )
            return "https://schema.org/$one";
        $report ( "the $name of $what is none of " . implode ( ', ', padJsonLdAvailability ) . ": '" . padMakeSafe ( $value, 40 ) . "'" );
        return NULL;

    }

    return $value;

  }

  // A value as one value: TRUE and FALSE as words would be no use, so a boolean is nothing,
  // and a date object is its date and time.

  function padJsonLdScalar ( $value ) {

    if ( is_bool ( $value ) or is_array ( $value ) or $value === NULL )
      return '';

    if ( $value instanceof DateTimeInterface )
      return $value->format ( 'Y-m-d\TH:i:sP' );

    return is_object ( $value ) ? '' : $value;

  }

  // A date of ISO 8601, read as the header of this file says - FALSE for one that is none.

  function padJsonLdDate ( $value ) {

    if ( $value instanceof DateTimeInterface )
      return $value->format ( 'Y-m-d\TH:i:sP' );

    if ( is_int ( $value ) )
      return gmdate ( 'Y-m-d\TH:i:s\Z', $value );

    if ( ! is_scalar ( $value ) or is_bool ( $value ) )
      return FALSE;

    $value = trim ( (string) $value );

    if ( preg_match ( '/^([0-9]{4})-([0-9]{2})-([0-9]{2})(T([0-9]{2}):([0-9]{2})(:([0-9]{2})(\.[0-9]+)?)?(Z|[+-][0-9]{2}:?[0-9]{2})?)?$/D', $value, $m ) )
      return ( checkdate ( (int) $m [2], (int) $m [3], (int) $m [1] )
               and ( ! isset ( $m [5] ) or ( $m [5] < 24 and $m [6] < 60 and ( $m [8] ?? '' ) < 60 ) ) ) ? $value : FALSE;

    $parts = date_parse ( $value );

    // The name of the day beside a full date - Fri, 20 Nov 2026 19:30:00 +0000, as an RSS
    // pubDate or a mail header writes it - is no relative date, though date_parse files it
    // under relative: the text is read without it, and the name must be the date's own day.

    $days = '/\b(sunday|monday|tuesday|wednesday|thursday|friday|saturday|sun|mon|tue|wed|thu|fri|sat)\b\.?,?/i';

    if ( isset ( $parts ['relative'] ) and preg_match_all ( $days, $value, $day ) == 1 ) {
      $day   = array_search ( strtolower ( substr ( $day [1] [0], 0, 3 ) ), [ 'sun', 'mon', 'tue', 'wed', 'thu', 'fri', 'sat' ] );
      $value = trim ( preg_replace ( $days, ' ', $value ) );
      $parts = date_parse ( $value );
    } else
      $day = FALSE;

    if ( $parts ['error_count'] or $parts ['warning_count'] or isset ( $parts ['relative'] )
         or $parts ['year'] === FALSE or $parts ['month'] === FALSE or $parts ['day'] === FALSE
         or ! checkdate ( $parts ['month'], $parts ['day'], $parts ['year'] )
         or ( $day !== FALSE and $day != gmdate ( 'w', gmmktime ( 0, 0, 0, $parts ['month'], $parts ['day'], $parts ['year'] ) ) ) )
      return FALSE;

    $date = sprintf ( '%04d-%02d-%02d', $parts ['year'], $parts ['month'], $parts ['day'] );

    if ( $parts ['hour'] === FALSE )
      return $date;

    $date .= sprintf ( 'T%02d:%02d:%02d', $parts ['hour'], $parts ['minute'], $parts ['second'] );

    if ( $parts ['is_localtime'] ?? FALSE )
      try {
        $date .= ( new DateTimeImmutable ( $value ) )->format ( 'P' );
      } catch ( Throwable $e ) {
        return FALSE;
      }

    return $date;

  }

  // An address made absolute, as JSON-LD wants its addresses - '' for one that is none. One
  // of http or https stands as it is, //host/x takes the scheme of the page, /x is on this
  // host, and the rest - ?page, #part, a path - is relative to the application's address,
  // where an <img src="logo.png"> of its pages finds its file. Another scheme is no address
  // - javascript:, data: - read as a browser reads it, without the white space it skips; an
  // @id ($any) is an IRI, and keeps one of another scheme, urn:isbn:...
  //
  // The address is not encoded further: what it holds is JSON-escaped, and the braces of
  // {search_term_string} must stay as they are.

  function padJsonLdUrl ( $value, $any = FALSE ) {

    global $padHost;

    $value = trim ( (string) $value );

    if ( $value === '' )
      return '';

    if ( preg_match ( '/^([a-z][a-z0-9+.-]*):/i', preg_replace ( '/[\x00-\x20]+/', '', $value ), $scheme ) ) {
      $scheme = strtolower ( $scheme [1] );
      if ( in_array ( $scheme, [ 'http', 'https' ], TRUE ) )
        return $value;
      return ( $any and ! in_array ( $scheme, [ 'javascript', 'data', 'vbscript' ], TRUE ) ) ? $value : '';
    }

    $host   = (string) ( $padHost ?? 'http://localhost/' );
    $origin = preg_match ( '#^[a-z][a-z0-9+.-]*://[^/]*#i', $host, $m ) ? $m [0] : 'http://localhost';

    if ( str_starts_with ( $value, '//' ) )
      return strstr ( $origin, '//', TRUE ) . $value;

    if ( $value [0] == '/' )
      return $origin . $value;

    return padJsonLdHome () . ( str_starts_with ( $value, './' ) ? substr ( $value, 2 ) : $value );

  }

  // The application's own address - http://localhost/pad/shop/ - where its pages are asked,
  // ?about of it the page about.

  function padJsonLdHome () {

    global $padHost, $padApp;

    return (string) ( $padHost ?? 'http://localhost/' ) . ( ( $padApp ?? '' ) !== '' ? "$padApp/" : '' );

  }

  // The page's own trail, as the breadcrumb widget shows it without data= (padBreadcrumbPath,
  // subs/widgets/_lib/breadcrumb.php): its steps come escaped for HTML, and are read back as
  // the characters they hold - home= names the first step there as here.

  function padJsonLdTrail () {

    include_once PAD . 'subs/widgets/_lib/widget.php';
    include_once PAD . 'subs/widgets/_lib/breadcrumb.php';

    $rows = [];

    foreach ( padBreadcrumbPath () as [ $label, $link ] )
      $rows [] = [ 'name' => html_entity_decode ( $label, ENT_QUOTES | ENT_HTML5, 'UTF-8' ),
                   'url'  => html_entity_decode ( $link,  ENT_QUOTES | ENT_HTML5, 'UTF-8' ) ];

    return $rows;

  }

  // The script element of an object: its JSON, < and > written as \u003C and \u003E. Its
  // syntax characters go as PAD's stand-ins, put back when the page is written out: the
  // script is the subsystem's own text, never template source - under $padProtectValues =
  // FALSE, where a tag's value is read again as template, the {search_term_string} of a
  // website's search was a tag, and "there is no tag named 'search_term_string'" ended the
  // page.

  function padJsonLdScript ( $object ) {

    $json = json_encode ( $object, JSON_HEX_TAG | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE );

    return ( $json === FALSE ) ? '' : padProtect ( '<script type="application/ld+json">' . $json . '</script>' );

  }

  // Whether the tag has a handling option - sort=, first=, where= ... - that works on the rows
  // of its data=, as on any data tag's.

  function padJsonLdHandles () {

    global $pad, $padParms;

    foreach ( $padParms [$pad] ?? [] as $one )
      if ( $one ['padPrmKind'] == 'option' and is_string ( $one ['padPrmName'] ) and padFileExists ( PAD . "handling/types/{$one ['padPrmName']}.php" )
           and ! in_array ( $one ['padPrmName'], [ 'negative', 'orderly', 'duplicates', 'atLeastOnce', 'left', 'right', 'both', 'sum', 'avg', 'min', 'max' ] ) )
        return TRUE;

    return FALSE;

  }

?>
