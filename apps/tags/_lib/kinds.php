<?php

  // The kinds of the subsystems - the chart types, the games, the toys ... - and the sequence
  // types: each a tag of its own, written with its type prefix, {chart:pie/}, and listed by
  // the tags application beside the built-in tags, {pad:if}. Nothing lists them here: a kind
  // is a file of pad/subs/<directory>/types/ (padSubsystems, lib/subsystem.php), a sequence type a
  // directory of pad/sequence/types/, and what its page says comes from there - the comment
  // at the top of its file, the line of docs/sequences/SEQUENCES.md - and its example from
  // what plays it already: the charts application's examples, the templates of the games
  // and the toys applications, the inputs application's pages, the tags application's own
  // examples of the widgets and the diagrams, or a line made for the kind.

  // The types, in the order of their groups: [ group label, tagline, form, syntax after the
  // name, the tag the subsystem has ].

  function tagsKindTypes () {

    return [
      'diff' => [ 'Diffs', 'Text and structured changes', 'single', " \$old, \$new/}", 'diff' ],
      'table' => [ 'Tables', 'Formatted, grouped and aggregated rows', 'both', " data='rows'/}", 'datatable' ],
      'time' => [ 'Time displays', 'Relative time, clocks and timers', 'single', '/}', 'time' ],
      'timeline' => [ 'Timelines', 'Events, milestones and lanes', 'both', " date='date', label='label'}...{/timeline:%s}", 'timeline' ],

      'image' => [ 'Images', 'Responsive pictures, figures and contact sheets', 'single', " 'photos/avatar-demo.jpg', alt='Harbour'/}", 'image' ],
      'avatar' => [ 'Avatars', 'Identity pictures and private fallbacks', 'single', " 'Ada Lovelace'/}", 'avatar' ],
      'calendar' => [ 'Calendars', 'Gregorian dates, events and holidays', 'both', " '2026-11', data='appointments'/}", 'calendar' ],
      'chart'    => [ 'Chart types',       'inline SVG',                 'both',   " data='rows', label='field', value='field'/}", 'chart'   ],
      'game'     => [ 'Games',             'played in the browser',       'both',   '/}',                                           'game'    ],
      'toy'      => [ 'Toys',              'a live simulation',             'both',   '/}',                                           'toy'     ],
      'diagram'  => [ 'Diagram types',     'laid out on the server',   'pair',   "}\n  ...\n{/diagram:%s}",                     'diagram' ],
      'widget'   => [ 'Widgets',           'working without script',  'both',   " 'name'}...{/widget:%s}",                     'widget'  ],
      'barcode'  => [ 'Barcode types',     'encoded on the server', 'single', " 'value'/}",                                'barcode' ],
      'embed'    => [ 'Embed providers',   'loaded on a click',      'single', " 'address', title='...'/}",                   'embed'   ],
      'input'    => [ 'Rich form fields',  'A field of {pad:input} each, posted by any form',        'single', " 'name', label='...'/}",                      'input'   ],
      'icon'     => [ 'Icons',             'Outlines on a 24 grid',    'single', ' size=24/}',                                  'icon'    ],
      'fake'     => [ 'Fake kinds',        'the same for the same seed',  'both',   '/}',                                           'fake'    ],
      'sequence' => [ 'Sequence types',    'Mathematical sequences, sources and operations, iterated as loops',    'both',   " rows=10}{\$%s} {/sequence:%s}",              'sequence' ],
      'pattern'  => [ 'Patterns',          'seeded SVG art',        'single', ' seed=7/}',                                   'pattern' ],
      'sound'    => [ 'Sounds',            'synthesised in the browser', 'single', '/}',                                          'sound'   ],
      'printable' => [ 'Printables',       'pages made for paper',   'pair',   "}\n  ...\n{/printable:%s}",                   'printable' ],
      'notation' => [ 'Notations',         'drawn on the server',  'both',   " 'text'/}",                                   'notation' ],
      'section'  => [ 'Sections',          'pure CSS', 'pair', " title='...'}\n  ...\n{/section:%s}",          'section' ],
      'effect'   => [ 'Effects',           'Motion and decoration',  'both',   "}...{/effect:%s}",                             'effect'  ],
      'jsonld'   => [ 'JSON-LD kinds',     'for search engines', 'single', " data='rows'/}",                          'jsonld'  ],
    ];

  }

  // The kinds of a type, their names sorted.

  function tagsKindNames ( $type ) {

    if ( $type == 'sequence' )
      return array_values ( array_filter ( padDirList ( PAD . 'sequence/types' ) ?: [],
                                           fn ( $one ) => $one [0] != '.' and $one [0] != '_' and is_dir ( PAD . "sequence/types/$one" ) ) );

    return isset ( padSubsystems [$type] ) ? padKinds ( padSubsystems [$type] [0] ) : [];

  }

  // Every kind as a catalog entry, keyed type:kind - what tagsRead gives of a built-in tag,
  // with its description and its examples.

  function tagsKinds () {

    static $kinds = NULL;

    if ( $kinds !== NULL )
      return $kinds;

    $kinds = [];

    foreach ( tagsKindTypes () as $type => list ( $label, $tagline, $form, $after, $tag ) )
      foreach ( tagsKindNames ( $type ) as $kind ) {

        list ( $syntax, $about, $text ) = tagsKindText ( $type, $kind );

        $form = ( $type == 'sequence' and in_array ( $kind, tagsKindParameterized () ) ) ? 'both' : $form;

        $tail = ( $type == 'sequence' ) ? tagsKindSequenceParm ( $kind ) . $after : $after;

        if ( $type === 'diff' and $kind === 'rows' ) $tail = " \$old, \$new, key='id'/}";

        if ( $type === 'table' )
          $tail = " data='rows'" . ( [ 'grouped' => ", group='region', totals='amount'", 'pivot' => ", row='region', column='quarter', value='amount'", 'matrix' => ", row='region', column='quarter'" ] [$kind] ?? '' ) . '/}';
        if ( $type === 'time' && in_array ( $kind, [ 'countdown', 'relative' ], TRUE ) )
          $tail = " '2026-12-31 00:00'/}";
        if ( $type === 'image' and $kind === 'contactsheet' )
          $tail = " data='photos', src='src', caption='caption'/}";

        if ( $type === 'calendar' )
          $tail = " '" . ( [ 'day' => '2026-11-04', 'week' => '2026-11-02', 'year' => '2026', 'quarter' => '2026-Q4' ] [$kind] ?? '2026-11' ) . "', data='appointments'/}";

        if ( $syntax === '' ) {
          $syntax = "{{$type}:$kind" . sprintf ( $tail, $kind, $kind );
          if ( tagsKindBare ( $type, $kind ) )
            $syntax .= "\n{{$kind}" . sprintf ( str_replace ( "$type:", '', $tail ), $kind, $kind );
        }

        $kinds ["$type:$kind"] = [
          'name'    => $kind,
          'type'    => $type,
          'key'     => "$type:$kind",
          'link'    => "tag/$type/$kind",
          'about'   => $about,
          'text'    => $text,
          'group'   => "kind-$type",
          'form'    => $form,
          'syntax'  => $syntax,
          'parms'   => [],
          'options' => [],
          'see'     => padTagBuiltIn ( $tag ) ? [ "pad:$tag" ] : [] ];

      }

    foreach ( padSequenceOperations as $operation ) {
      $sample = tagsKindExample ( $operation, 'auto' );
      $kinds ["$operation:auto"] = [
        'name' => 'auto', 'type' => $operation, 'key' => "$operation:auto",
        'link' => "tag/$operation/auto", 'about' => 'Selects the source from the parameters, preserving their order.',
        'text' => '', 'group' => 'kind-sequence', 'form' => 'both', 'syntax' => $sample ['source'],
        'parms' => [], 'options' => [], 'see' => [ 'sequence:range', 'sequence:prime' ] ];
    }

    return $kinds;

  }

  // What a sequence type is written with before its options: a number for the types that
  // take one - add=3 - a range, a list, an A-number of the OEIS, the value to repeat.

  function tagsKindSequenceParm ( $kind ) {

    if ( in_array ( $kind, tagsKindParameterized () ) )
      return ' 3,';

    return [ 'range' => " '1..10',", 'list' => " '5;2;8;1;9',", 'oeis' => ' 81,', 'repeat' => ' 7,' ] [$kind] ?? '';

  }

  // The sequence types that take a number - add=3 - and are written with one in an example.

  function tagsKindParameterized () {

    return [ 'add', 'subtract', 'multiply', 'divide', 'modulo', 'power', 'exponentiation', 'ceil',
             'floor', 'round', 'multiple', 'step', 'and', 'or', 'xor', 'nand', 'nor', 'xnor', 'chance' ];

  }

  // [ syntax, one line, description ] of a kind: the comment at the top of its file - a
  // widget's starts with its syntax, every other kind's with its name and a dash - or for an
  // icon, which is an SVG, and a sequence type, the line of SEQUENCES.md.

  function tagsKindText ( $type, $kind ) {

    if ( $type == 'icon' )
      return [ '', "The $kind icon - an outline on a grid of 24, drawn in the colour of its text.", '' ];

    if ( $type == 'sequence' ) {
      $line = tagsKindSequenceLines () [$kind] ?? '';
      return [ '', $line !== '' ? "$line." : "The $kind sequence.", '' ];
    }

    $file = PAD . "subs/" . padSubsystems [$type] [0] . "/types/$kind.php";
    $text = '';

    if ( preg_match ( '/\A<\?php\s*\n((?:[ \t]*\/\/[^\n]*\n)+)/', padFileGet ( $file ), $match ) )
      $text = trim ( implode ( "\n", array_map ( fn ( $line ) => preg_replace ( '/^\s*\/\/ ?/', '', $line ), explode ( "\n", $match [1] ) ) ) );

    $syntax = '';
    $flat   = preg_replace ( '/\s+/', ' ', explode ( "\n\n", $text ) [0] );

    if ( str_starts_with ( $flat, '{' ) and ( $at = strpos ( $flat, '} - ' ) ) !== FALSE ) {
      $syntax = implode ( "\n", array_map ( fn ( $one ) => preg_replace ( "/^\{$kind\b/", "{{$type}:$kind", trim ( $one ) ),
                                            explode ( ' or ', substr ( $flat, 0, $at + 1 ) ) ) );
      $syntax = preg_replace ( "#\{/$kind\}#", "{/$type:$kind}", $syntax );
      $flat   = substr ( $flat, $at + 4 );
    } elseif ( str_starts_with ( $flat, "$kind - " ) )
      $flat   = substr ( $flat, strlen ( $kind ) + 3 );

    return [ $syntax, tagsKindSentence ( $flat ), $text ];

  }

  // The one line of a kind: the first sentence of its text up to its first colon, semicolon,
  // dash or bracket - what the sentence is, without how - its first letter a capital and a
  // full stop at its end.

  function tagsKindSentence ( $text ) {

    $text = trim ( $text );

    if ( preg_match ( '/^(.{12,}?[.!?])(\s|$)/s', $text, $match ) )
      $text = $match [1];

    if ( preg_match ( '/^(.{24,}?)(?:: | - |; | \()/s', $text, $match ) )
      $text = $match [1];

    if ( strlen ( $text ) > 140 and ( $cut = strrpos ( substr ( $text, 0, 130 ), ', ' ) ) )
      $text = substr ( $text, 0, $cut );

    $text = rtrim ( $text, ' :;,-' );

    if ( ! preg_match ( '/[.!?]$/', $text ) )
      $text .= '.';

    return ucfirst ( $text );

  }

  // The line of each sequence type in docs/sequences/SEQUENCES.md - "- `fibonacci` -
  // Fibonacci numbers (0, 1, 1, 2, ...)" - the type's name and the =N it takes left off.

  function tagsKindSequenceLines () {

    static $lines = NULL;

    if ( $lines !== NULL )
      return $lines;

    $lines = [];
    $file  = dirname ( APPS ) . '/docs/sequences/SEQUENCES.md';

    if ( preg_match_all ( '/^- `([a-zA-Z]+)(?:=N)?` - (.+)$/m', is_file ( $file ) ? (string) file_get_contents ( $file ) : '', $found, PREG_SET_ORDER ) )
      foreach ( $found as list ( , $name, $line ) )
        $lines [$name] ??= rtrim ( trim ( $line ), '.' );

    return $lines;

  }

  // The example of a kind: [ title, about, source, page, app, run ] - a template run here
  // (source), or a page of an application run there (page and app, '' for this one); run is
  // FALSE for an example shown but never run, as the tags application's .src files are.

  function tagsKindExample ( $type, $kind ) {

    $example = fn ( $title, $about, $source = '', $page = '', $app = '', $run = TRUE ) => compact ( 'title', 'about', 'source', 'page', 'app', 'run' );

    if ( $kind === 'auto' and in_array ( $type, padSequenceOperations, TRUE ) ) {
      $args = [ 'sequence' => "'1..5'", 'action' => "'1..5', reverse",
                'keep' => "'1..10', prime", 'make' => "'1..5', add=10",
                'remove' => "'1..10', prime" ] [$type];
      return $example ( 'A source selected from its parameters',
                        'A range, list, count, stored sequence or type can be supplied at runtime.',
                        "{{$type}:auto $args}{\$sequence} {/{$type}:auto}" );
    }

    switch ( $type ) {

      case 'diff':
        $file = APPS . "tags/examples/diff/$kind.pad";
        return $example ( "A $kind comparison", 'Two versions compared on the server.', trim ( padFileGet ( $file ) ) );

      case 'table':
        $file = APPS . "tags/examples/table/$kind.pad";
        return $example ( "A $kind table", 'Rows formatted and aggregated on the server.', trim ( padFileGet ( $file ) ) );
      case 'time':
        $file = APPS . "tags/examples/time/$kind.pad";
        return $example ( "The $kind time display", 'Date-aware server output with browser controls when needed.', trim ( padFileGet ( $file ) ) );

      case 'timeline':
        $file = APPS . "tags/examples/timeline/$kind.pad";
        return $example ( "The $kind timeline", 'Events and spans from the same row format.', trim ( padFileGet ( $file ) ) );
      case 'image':
        $file = APPS . "tags/examples/image/$kind.pad";
        return $example ( "An image $kind", 'Local images through the shared filter and thumbnail pipeline.', trim ( padFileGet ( $file ) ) );

      case 'avatar':
        $file = APPS . "tags/examples/avatar/$kind.pad";
        return $example ( "A $kind avatar", 'An identity picture with accessible text.', trim ( padFileGet ( $file ) ) );

      case 'calendar':
        $file = APPS . "calendars/_examples/$kind.pad";
        return $example ( "The $kind calendar", 'With the events shown in the calendars application.',
                          is_file ( $file ) ? trim ( padFileGet ( $file ) ) : "{calendar:$kind/}" );

      case 'chart':
        foreach ( tagsKindAppPages ( 'charts' ) as $page => $source )
          if ( preg_match ( "/\\{chart:$kind\\b/", $source ) )
            return $example ( 'From the charts application', "Its example $page there, with the data it draws.", '', $page, 'charts' );
        return $example ( "A $kind chart", 'Provide the chart data as rows.', "{chart:$kind data='rows', label='label', value='value'/}", '', '', FALSE );

      case 'game':
        $file = APPS . "games/_games/$kind.pad";
        return $example ( "A game of $kind", 'As the games application plays it.', is_file ( $file ) ? trim ( padFileGet ( $file ) ) : "{game:$kind/}" );

      case 'toy':
        $file = APPS . "toys/_toys/$kind.pad";
        return $example ( "The $kind toy", 'As the toys application shows it.', is_file ( $file ) ? trim ( padFileGet ( $file ) ) : "{toy:$kind/}" );

      case 'widget':
      case 'diagram':
      case 'notation':
        $dir = in_array ( $kind, [ 'copy', 'chess' ], TRUE ) ? $kind : $type;
        foreach ( tagsKindAppPages ( 'tags', "examples/$dir" ) as $page => $source )
          if ( in_array ( $dir, [ 'copy', 'chess' ], TRUE ) or str_starts_with ( basename ( $page ), "$kind-" )
               or preg_match ( "/\\{" . $type . ":$kind\\b/", $source ) )
            return $example ( 'From the tags application', "Its example in the $dir collection.", '', $page );
        foreach ( tagsKindAppPages ( 'tags', "examples/$dir", '.src' ) as $page => $source )
          if ( str_starts_with ( basename ( $page ), "$kind-" ) )
            return $example ( 'From the tags application', "Its example in the $dir collection, shown but not run here.", $source, '', '', FALSE );
        return $example ( "A $kind", '', "{{$type}:$kind/}" );

      case 'input':
        $line = preg_match ( "/\\{input:$kind\\s[^\\n]*?\\/\\}/", padFileGet ( APPS . 'inputs/form.pad' ), $m ) ? $m [0] : "{input:$kind 'field', label='Try it'/}";
        $line = preg_replace ( "/, rules='[^']*'/", '', $line );
        return $example ( 'A field of a form', "As the inputs application's form writes it - its rules= left out, which the page's own template must hold.",
                          "{pad:form 'try'}\n  $line\n  <button>Send</button>\n{/pad:form}" );

      case 'barcode':
        return $example ( "A $kind barcode", '', "{barcode:$kind '" . ( tagsKindBarcodeValues () [$kind] ?? '12345' ) . "'/}" );

      case 'embed':
        list ( $address, $title ) = tagsKindEmbedAddresses () [$kind == 'auto' ? 'youtube' : $kind] ?? [ '', $kind ];
        return $example ( "An $kind embed", 'Nothing of the provider is asked before the click.', "{embed:$kind '$address', title='$title'/}" );

      case 'icon':
        return $example ( "The $kind icon", 'At three sizes, and in the colour of its text.',
                          "{icon:$kind size=16/} {icon:$kind size=24/} {icon:$kind size=48/}\n<span style=\"color:#2a78d6\">{icon:$kind size=48/}</span>" );

      case 'fake':
        if ( $kind == 'rows' )
          return $example ( 'Rows of several fields', 'Names and mail addresses from one seed.',
                            "{fake:rows 'name, email', rows=3, seed=7}<p>{\$name} — {\$email}</p>{/fake:rows}" );
        return $example ( "Three fake values", 'The same on every request.', "<ul>\n  <li>{fake:$kind/}</li>\n  <li>{fake:$kind/}</li>\n  <li>{fake:$kind/}</li>\n</ul>" );

      case 'pattern':
        $line = preg_match ( "/^\\s*(\\{pattern:{$kind}[^\\n]*\\/\\})\\s*$/m", tagsKindText ( $type, $kind ) [2], $m ) ? $m [1] : "{pattern:$kind seed=7/}";
        return $example ( "A $kind pattern", 'The example its file gives.', $line );
      case 'sound':
        return $example ( "The $kind sound", 'A button that plays it, and its WAV file.', "{sound:$kind download/}" );

      case 'printable':
        $file = APPS . "printables/kinds/$kind.pad";
        return $example ( "The $kind", 'As the printables application shows it.', is_file ( $file ) ? trim ( padFileGet ( $file ) ) : "{printable:$kind scale=0.4}A $kind{/printable:$kind}" );

      case 'sequence':
        $parm = tagsKindSequenceParm ( $kind );
        return $example ( 'The first ten terms', $parm ? 'With the parameter the type takes.' : '', "{sequence:$kind$parm rows=10}{\$$kind} {/sequence:$kind}" );

      case 'jsonld':
        return $example ( "The $kind as JSON-LD", 'The script it writes, shown here with | html - on a page the tag alone writes it, unseen.',
                          tagsKindJsonLdExamples () [$kind] ?? "<pre style=\"white-space:pre-wrap\">{jsonld:$kind | html/}</pre>" );

      case 'section':
      case 'effect':
        $file = APPS . "sections/_examples/$type/$kind.pad";
        return $example ( "The $kind $type", 'As the sections application shows it.', is_file ( $file ) ? trim ( padFileGet ( $file ) ) : "{{$type}:$kind}...{/$type:$kind}" );

    }

    return $example ( $kind, '' );

  }

  // The pages of an application, or of a directory of it, by name, with their template.

  function tagsKindAppPages ( $app, $dir = '', $extension = '.pad' ) {

    static $pages = [];

    if ( isset ( $pages ["$app/$dir$extension"] ) )
      return $pages ["$app/$dir$extension"];

    $found = [];
    $root  = APPS . "$app/";

    foreach ( new RecursiveIteratorIterator ( new RecursiveDirectoryIterator ( $root . $dir, FilesystemIterator::SKIP_DOTS ) ) as $file ) {
      $path = substr ( $file->getPathname (), strlen ( $root ) );
      if ( str_ends_with ( $path, $extension ) and ! str_contains ( "/$path", '/_' ) and ! str_contains ( $path, '[' ) )
        $found [ substr ( $path, 0, - strlen ( $extension ) ) ] = padFileGet ( $file->getPathname () );
    }

    ksort ( $found );

    return $pages ["$app/$dir$extension"] = $found;

  }

  // The example of each JSON-LD kind: the tag with fields of its own, its script made visible
  // by the html pipe - a faq's rows from a {pad:data} block, a breadcrumb the trail of its page.

  function tagsKindJsonLdExamples () {

    $pre = fn ( $tag ) => "<pre style=\"white-space:pre-wrap\">$tag</pre>";

    return [
      'article'      => $pre ( "{jsonld:article headline='Pages that drive themselves', author='Ann Smith', published='2026-11-01', image='cover.jpg' | html/}" ),
      'breadcrumb'   => $pre ( "{jsonld:breadcrumb | html/}" ),
      'business'     => $pre ( "{jsonld:business type='Bakery', name='The Loaf', street='Main Street 1', city='Utrecht', country='NL', latitude='52.09074', longitude='5.12142', hours='Mo-Fr 08:00-18:00; Sa 08:00-16:00' | html/}" ),
      'course'       => $pre ( "{jsonld:course name='PAD in a day', description='The template language from the first tag to a shop.', provider='PAD Academy', providerurl='https://academy.example', code='PAD101' | html/}" ),
      'event'        => $pre ( "{jsonld:event name='PAD day', starts='2026-11-20 19:30', place='The Hall', address='Main Street 1, Utrecht', price='12.50', currency='EUR', availability='InStock' | html/}" ),
      'faq'          => "{pad:data 'questions'}\n[ { \"question\": \"Is it free?\", \"answer\": \"Yes, under the GPL.\" } ]\n{/pad:data}\n" . $pre ( "{jsonld:faq data='questions' | html/}" ),
      'itemlist'     => "{pad:data 'soups'}\n[ { \"title\": \"Pea soup\", \"link\": \"?recipes/pea-soup\" },\n  { \"title\": \"Tomato soup\", \"link\": \"?recipes/tomato-soup\" } ]\n{/pad:data}\n" . $pre ( "{jsonld:itemlist data='soups', name='Soups' | html/}" ),
      'job'          => $pre ( "{jsonld:job title='Baker', description='Bread from 05:00, four days a week.', posted='2026-11-01', company='The Loaf', city='Utrecht', country='NL', employment='part time', salary='2400-2800', currency='EUR', per='month' | html/}" ),
      'organization' => $pre ( "{jsonld:organization name='PAD', url='https://pad.example', logo='logo.png', profiles='https://github.com/pad', phone='+31 30 123 4567', contact='customer service' | html/}" ),
      'person'       => $pre ( "{jsonld:person name='Ann Smith', job='Engineer', profiles='https://github.com/ann, https://social.example/@ann' | html/}" ),
      'product'      => $pre ( "{jsonld:product name='Aurora kettle', sku='AK-1', brand='Aurora', price='39.95', currency='EUR', availability='InStock' | html/}" ),
      'profile'      => $pre ( "{jsonld:profile name='Ann Smith', handle='ann', image='avatars/ann.jpg', profiles='https://github.com/ann', followers=340, posts=415, created='2025-01-10' | html/}" ),
      'recipe'       => "{pad:data 'soup'}\n{ \"name\": \"Pea soup\", \"image\": \"soup.jpg\", \"prep\": \"20 min\", \"cook\": \"1 h 30 min\", \"yield\": \"4\",\n  \"ingredients\": [ \"500 g split peas\", \"1 leek, sliced\", \"2 l water\" ],\n  \"steps\": [ \"Soak the peas overnight.\", \"Simmer it all for an hour and a half.\" ] }\n{/pad:data}\n" . $pre ( "{jsonld:recipe data='soup' | html/}" ),
      'restaurant'   => $pre ( "{jsonld:restaurant name='De Ketel', cuisine='Dutch', menu='?menu', street='Oudegracht 1', city='Utrecht', country='NL', hours='Tu-Su 17:00-23:00', prices='\$\$' | html/}" ),
      'software'     => $pre ( "{jsonld:software name='Pocket Ledger', type='mobile application', os='Android 12, iOS 17', category='finance application', price=0, rating='4.6', ratings=1523 | html/}" ),
      'video'        => $pre ( "{jsonld:video name='Making pea soup', thumbnail='soup.jpg', uploaded='2026-11-01T10:00:00+01:00', duration='4 min 12 s', player='https://video.example/embed/123', views=2347 | html/}" ),
      'website'      => $pre ( "{jsonld:website name='PAD', search='?search&q=' | html/}" ) ];

  }

  function tagsKindBarcodeValues () {

    return [ 'ean13' => '400638133393', 'ean8' => '9638507', 'upca' => '03600029145', 'upce' => '0123456',
             'code128' => 'PAD-2026', 'code39' => 'PAD 2026', 'code93' => 'PAD-93', 'itf' => '12345678',
             'itf14' => '1540014128876', 'codabar' => 'A40156B', 'msi' => '1234567', 'qr' => 'https://example.org/pad',
             'datamatrix' => 'PAD 2026',
             'gs1128' => '(01)09501101530003(17)261231(10)AB-123', 'aztec' => 'Code 2D!', 'pdf417' => 'PDF417 Test', 'bookland' => '978-0-306-40615-7', 'databar' => '09501101530003' ];

  }

  function tagsKindEmbedAddresses () {

    return [
      'youtube'         => [ 'https://www.youtube.com/watch?v=aqz-KE-bpKQ',            'Big Buck Bunny' ],
      'vimeo'           => [ 'https://vimeo.com/76979871',                             'The new Vimeo player' ],
      'peertube'        => [ 'https://framatube.org/w/kkGMgK9ZtnKfYAgnEtQxbv',         'What is PeerTube?' ],
      'dailymotion'     => [ 'https://www.dailymotion.com/video/x7tgad0',              'A video on Dailymotion' ],
      'internetarchive' => [ 'https://archive.org/details/BigBuckBunny_124',           'Big Buck Bunny at the Internet Archive' ],
      'soundcloud'      => [ 'https://soundcloud.com/forss/flickermood',               'Flickermood' ],
      'spotify'         => [ 'https://open.spotify.com/intl-nl/track/4uLU6hMCjMI75M1A2tKUQC', 'A track on Spotify' ],
      'codepen'         => [ 'https://codepen.io/team/codepen/pen/PNaGbb',             'A pen on CodePen' ],
      'openstreetmap'   => [ '52.3731,4.8922',                                         'Dam Square, Amsterdam' ],
      'applemusic'      => [ 'https://music.apple.com/us/album/abbey-road-2019-mix/1474815798', 'Abbey Road - The Beatles' ],
      'applepodcasts'   => [ 'https://podcasts.apple.com/us/podcast/the-daily/id1200361736', 'The Daily' ],
      'figma'           => [ 'https://www.figma.com/design/nrPSsILSYjesyc5UHjYYa4/Embed-Kit-2.0-examples', 'Embed Kit 2.0 examples' ],
      'loom'            => [ 'https://www.loom.com/share/fa3c0bc94e904ab6b0746f844ca7a385', 'Follow a Loom product demo' ],
      'tiktok'          => [ 'https://www.tiktok.com/@scout2015/video/6718335390845095173', 'A video of @scout2015' ],
      'stackblitz'      => [ 'https://stackblitz.com/github/vitejs/vite/tree/main/packages/create-vite/template-vanilla', 'The vanilla template of Vite' ],
      'twitch'          => [ 'twitchgaming',                                           'Twitch Gaming' ],
      'videofile'       => [ 'https://example.org/clips/sunrise.webm',                 'A sunrise' ],
      'audio'           => [ 'https://example.org/sounds/bell.mp3',                    'A bell' ],
      'pdffile'         => [ 'https://www.example.org/docs/plan.pdf',                  'The floor plan' ] ];

  }

  // Named subsystem tags always select their namespace explicitly.
  function tagsKindBare ( $type, $kind ) {
    return FALSE;
  }

  // The description on a kind's page: the ways it is written, then the comment at the top of
  // its file, a paragraph each, a {...} in it as code.

  function tagsKindHtml ( $tag ) {

    $type  = $tag ['type'];
    $kind  = $tag ['name'];
    if ( $kind === 'auto' and in_array ( $type, padSequenceOperations, TRUE ) )
      return '<p>The <code>' . $type . ':auto</code> selector reads its source and operations from the parameters, in their written order. '
           . 'Use a named kind when it is known, such as <code>{sequence:range \'1..5\'}</code> or <code>{action:reverse}</code>. '
           . 'For a variable that holds only a kind name, use <code>{' . $type . ':$kind}</code>.</p>';

    $types = tagsKindTypes ();
    $of    = $types [$type] [4];
    $count = count ( tagsKindNames ( $type ) );
    $code  = fn ( $text ) => '<code>' . htmlspecialchars ( $text ) . '</code>';

    $ways = [ $tag ['syntax'] ];
    if ( $type == 'sequence' )
      $ways [] = "sequence:$kind(8) in an expression, the eighth term";

    // The group's label in the sentence, lower case - but one that starts with an acronym,
    // JSON-LD kinds, as it is written.

    $label = preg_match ( '/^[A-Z]{2}/', $types [$type] [0] ) ? $types [$type] [0] : strtolower ( $types [$type] [0] );

    $html = '<p>One of the ' . $count . ' ' . $label . ' of <a href="?tag/' . $of . '">' . $code ( $type . ':*' ) . '</a>, written '
          . implode ( ', ', array_map ( fn ( $way ) => preg_match ( '/^(\{.*?\}|\S+\))(.*)$/', $way, $m ) ? $code ( $m [1] ) . htmlspecialchars ( $m [2] ) : htmlspecialchars ( $way ), $ways ) )
          . '.</p>';

    $text = preg_replace ( '/\A' . preg_quote ( $kind, '/' ) . ' - /', '', (string) $tag ['text'] );

    foreach ( preg_split ( '/\n\s*\n/', ucfirst ( $text ) ) as $paragraph )
      if ( trim ( $paragraph ) !== '' and ! preg_match ( '/^\s{2,}\{/', $paragraph ) )
        $html .= '<p>' . preg_replace ( '/\{[^{}\n]*\}/', '<code>$0</code>', htmlspecialchars ( preg_replace ( '/\s+/', ' ', trim ( $paragraph ) ), ENT_NOQUOTES ) ) . '</p>';

    return $html;

  }

?>
