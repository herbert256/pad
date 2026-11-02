<?php

  // Shared by the tags and types applications. These families have kinds, but no type:
  // prefix. Read their names from the engine and their descriptions from their files;
  // the examples use the option or helper that actually selects a kind.

  function appSubsystemFamilies () {

    $info = [
      'map'      => [ 'Map views', 'The view parameter of {map:auto} selects a country or region.', 'map', 'world' ],
      'rule'     => [ 'Validation rules', 'Rules passed to padValidate or a form field\'s rules= option.', 'validator', 'required' ],
      'oauth'    => [ 'OAuth providers', 'The provider parameter of {pad:oauth}, padOAuthStart and padOAuthUser.', 'oauth', 'github' ],
      'channel'  => [ 'Notification channels', 'Delivery channels selected in the fourth argument of padNotify.', 'notifications', 'mail' ],
      'ai'       => [ 'AI providers', 'The provider= option of {ai:auto} or padAi.', 'ai', 'anthropic' ],
      'filter'   => [ 'Image filters', 'The filter= option of {image:thumbnail} selects a filter or placeholder.', 'image:thumbnail', 'grayscale' ],
      'holidays' => [ 'Holiday calendars', 'The country parameter of {holidays:auto}, with optional regions.', 'holidays', 'nl' ],
      'search'   => [ 'Search languages', 'The lang= option of {search:auto} selects stop words and a stemmer.', 'search', 'en' ],
      'format'   => [ 'Data formats', 'The format= option of {format:auto}, file extensions and the format helpers.', 'data', 'toml' ],
      'formula'  => [ 'Spreadsheet functions', 'Functions inside the formulas of {sheet:grid} cells.', 'sheet:grid', 'sum' ],
      'theme'    => [ 'Themes', 'The name parameter of {pad:theme} selects page or box colours.', 'theme', 'paper' ],
      'unit' => [ 'Unit conversions', 'Dimension-checked numeric conversion through padConvert.', '', 'length' ],
      'highlighter' => [ 'Syntax languages', 'Languages selected by padHighlight and the highlight tag.', 'highlight', 'python' ],
      'diff' => [ 'Comparison kinds', 'Typed change records through padDiffCompare, and escaped HTML through padDiffRender.', 'diff', 'json' ],
      'recurrence' => [ 'Calendar recurrences', 'Bounded occurrences between explicit dates through padOccurrences.', '', 'weekly' ],
      'geo' => [ 'Geographic operations', 'Plain data operations through padGeo, with longitude before latitude.', '', 'distance' ],
      'distribution' => [ 'Sampling distributions', 'Seeded samples through padSample.', '', 'normal' ],
      'palette'  => [ 'Colour palettes', 'The name parameter of {pad:palette}, or palette= on a chart.', 'palette', 'viridis' ],
    ];

    $families = [];

    foreach ( array_diff_key ( padSubsystemKinds, padSubsystems ) as $key => $directory ) {
      [ $label, $about, $tag, $example ] = $info [$key] ?? [ ucfirst ( $directory ), 'Kinds selected through the subsystem\'s API.', '', '' ];
      $families [$key] = compact ( 'key', 'directory', 'label', 'about', 'tag', 'example' );
    }

    return $families;

  }

  function appSubsystemComment ( $family, $kind ) {

    $directory = padSubsystemKinds [$family] ?? '';
    if ( ! $directory or ! padKind ( $directory, $kind ) ) return '';

    $source = (string) padFileGet ( PAD . "subs/$directory/types/$kind.php" );
    preg_match ( '/\A<\?php\s*\n((?:[ \t]*\/\/[^\n]*\n|[ \t]*\n)+)/', $source, $match );

    $text = trim ( preg_replace ( '/^[ \t]*\/\/ ?/m', '', $match [1] ?? '' ) );
    if ( $text !== '' ) return $text;
    foreach ( [ "docs/subsystems/$directory/$kind.md", "docs/subsystems/$directory.md", "docs/subsystems/$directory/README.md", "docs/subsystems/$directory/index.md" ] as $doc )
      if ( is_file ( dirname ( APPS ) . '/' . $doc ) ) return (string) file_get_contents ( dirname ( APPS ) . '/' . $doc );
    return ucfirst ( $kind ) . ' is selected through the ' . $family . ' subsystem API.';

  }

  function appSubsystemExample ( $family, $kind ) {

    $source = '';
    $language = 'pad';
    $run = FALSE;
    $about = '';

    switch ( $family ) {
      case 'unit':
        $language = 'php';
        $pairs = [ 'length' => [ 'cm', 'm' ], 'area' => [ 'm2', 'ft2' ], 'volume' => [ 'L', 'mL' ], 'mass' => [ 'kg', 'g' ], 'temperature' => [ 'C', 'F' ], 'digital' => [ 'KiB', 'B' ] ];
        [ $a, $b ] = $pairs [$kind];
        $source = "padConvert ( 100, '$a', '$b' );";
        break;
      case 'highlighter':
        $language = 'php'; $source = "padHighlight ( \$source, '$kind', TRUE );";
        break;
      case 'diff':
        $language = 'php'; $args = $kind === 'rows' ? ", [ 'key' => 'id' ]" : '';
        $source = "\$changes = padDiffCompare ( '$kind', \$before, \$after$args );\npadDiffRender ( \$changes, TRUE );";
        break;
      case 'recurrence':
        $language = 'php'; $extra = $kind === 'rrule' ? ", 'rrule' => 'FREQ=WEEKLY;COUNT=4'" : ", 'count' => 4";
        $source = "padOccurrences ( '$kind', [ 'start' => '2026-11-02'$extra ], '2026-11-01', '2031-12-01' );";
        break;
      case 'distribution':
        $language = 'php'; $source = "padSample ( '$kind', 100, [ 'seed' => 7 ] );";
        break;
      case 'geo':
        $language = 'php';
        $args = [ 'distance' => '[4.9,52.37], [2.35,48.86]', 'bearing' => '[4.9,52.37], [2.35,48.86]', 'bbox' => '[[179,10],[-179,20]]', 'contains' => "[1,1], ['type'=>'Polygon', 'coordinates'=>[[[0,0],[2,0],[2,2],[0,2],[0,0]]]]", 'geohash' => '[4.9,52.37]' ];
        $source = "padGeo ( '$kind', " . $args [$kind] . ' );';
        break;
      case 'map':
        $source = "{map:auto '$kind', width=540/}";
        $run = TRUE;
        $about = 'The view without data; data= and value= colour its regions.';
        break;
      case 'oauth':
        $source = "{pad:oauth '$kind', href='#example'/}";
        $run = TRUE;
        $about = 'The button only. In your application, link to a login page that calls padOAuthStart and handles padOAuthUser on return.';
        break;
      case 'channel':
        $language = 'php';
        $source = "padNotify ( \$recipient, 'order-shipped', [ 'order' => 42 ], [ '$kind' ] );\n\$results = padNotifyResults ();";
        $about = 'Provide the recipient, channel settings and _notifications/order-shipped.pad first. Shown as source because this call delivers a notification.';
        break;
      case 'ai':
        $source = "{ai:auto 'Name three rivers of France.', provider='$kind'/}";
        $about = 'Set up the provider and its credentials first. Shown as source because it calls a model service.';
        break;
      case 'filter':
        $source = "{image:thumbnail 'photo.jpg', alt='A landscape', width=320, filter='$kind'/}";
        $about = 'Put photo.jpg in the application\'s www directory before running this example.';
        break;
      case 'holidays':
        $source = "{holidays:auto '$kind', year=2026/}";
        $run = TRUE;
        break;
      case 'search':
        $source = "{format:auto 'catalogDocs'}\n[ { \"title\": \"PAD\", \"body\": \"Templates on the server\" } ]\n{/format:auto}\n{search:auto 'catalogDocs', q='PAD', fields='title, body', lang='$kind'}\n  <p>{\$title}: {!excerpt}</p>\n{/search:auto}";
        $run = TRUE;
        break;
      case 'format':
        $language = 'php';
        $definition = padFormatDef ( $kind );
        if ( isset ( $definition ['read'] ) ) $source .= "\$rows = padFormatRead ( '$kind', \$contents );\n";
        if ( isset ( $definition ['write'] ) ) $source .= "\$contents = padFormatWrite ( '$kind', \$rows );";
        if ( $kind === 'fixedwidth' ) $source = "\$rows = padFormatRead ( 'fixedwidth', \$contents, [ 'columns' => [ 'name' => 12, 'age' => 3 ] ] );";
        $about = 'Supply the document bytes in $contents or the data in $rows. The description below explains the format\'s record shape; XLSX takes binary bytes.';
        break;
      case 'formula':
        $text = appSubsystemComment ( $family, $kind );
        if ( preg_match ( '/^  (=\S.*?)(?:[ \t]{2,}\S.*)?$/m', $text, $match ) ) {
          $formula = str_replace ( [ '\\', "'" ], [ '\\\\', "\\'" ], $match [1] );
          $sample = trim ( (string) padFileGet ( APPS . 'sheets/_data/sample.csv' ) );
          $source = "{sheet:grid formulas='G1$formula'}\n$sample\n{/sheet:grid}";
          $run = TRUE;
          $about = 'The first example from the function\'s source, computed in G1 on the sheets application\'s sample data.';
        } else {
          $source = strtoupper ( $kind ) . '(...)';
          $about = 'Use this function inside a spreadsheet cell beginning with =; its arguments are described below.';
        }
        break;
      case 'theme':
        $source = "{pad:theme '$kind'}<p>A box in the $kind theme.</p>{/pad:theme}";
        $run = TRUE;
        break;
      case 'palette':
        $source = "{pad:palette '$kind'/}";
        $run = TRUE;
        break;
      case 'rule':
        $arguments = [
          'after' => 'other', 'after_or_equal' => 'other', 'before' => 'other', 'before_or_equal' => 'other',
          'between' => '1,10', 'different' => 'other', 'digits' => '4', 'ends_with' => '.jpg,.png',
          'gt' => '0', 'gte' => '0', 'in' => 'red,green', 'ip' => 'v4', 'lt' => '100', 'lte' => '100',
          'max' => '10', 'max_items' => '3', 'max_words' => '20', 'min' => '1', 'min_items' => '1',
          'min_words' => '2', 'multiple_of' => '6', 'not_in' => 'admin,root', 'regex' => '/^[A-Z]+$/',
          'required_if' => 'other,yes', 'required_unless' => 'other,no', 'required_with' => 'other',
          'required_without' => 'other', 'same' => 'other', 'starts_with' => 'PAD,PHP'
        ];
        $rule = $kind . ( isset ( $arguments [$kind] ) ? ':' . $arguments [$kind] : '' );
        $language = 'php';
        $source = "\$errors = padValidate ( [ 'value' => '$rule' ], \$values );";
        $about = 'Supply a field array in $values, including other when the rule compares fields. The result contains the validation errors; an empty array means the values passed.';
        break;
    }

    return [ 'source' => trim ( $source ), 'language' => $language, 'run' => $run, 'about' => $about ];

  }

  function appSubsystemCatalog () {

    static $catalog = NULL;
    if ( $catalog !== NULL ) return $catalog;
    $catalog = [];

    foreach ( appSubsystemFamilies () as $family => $info )
      foreach ( padKinds ( $info ['directory'] ) as $kind ) {
        $text = appSubsystemComment ( $family, $kind );
        $first = trim ( preg_replace ( '/\s+/', ' ', explode ( "\n\n", $text ) [0] ) );
        $first = preg_replace ( '/^' . preg_quote ( $kind, '/' ) . ' - /', '', $first );
        if ( preg_match ( '/^(.+?[.!?])(?:\s|$)/', $first, $match ) ) $first = $match [1];
        $example = appSubsystemExample ( $family, $kind );
        $catalog ["$family:$kind"] = [
          'name' => $kind, 'type' => $family, 'key' => "$family:$kind", 'label' => "$family / $kind",
          'link' => "kind/$family/$kind", 'about' => ucfirst ( $first ?: $info ['about'] ),
          'text' => $text, 'group' => "subsystem-$family", 'form' => 'usage',
          'syntax' => $example ['source'], 'language' => $example ['language'], 'example' => $example,
          'parms' => [], 'options' => [], 'see' => $info ['tag'] ? [ str_contains ( $info ['tag'], ':' ) ? $info ['tag'] : 'pad:' . $info ['tag'] ] : []
        ];
      }

    return $catalog;

  }

  function appSubsystemLinks ( $families, $base = '?kind/' ) {

    $html = [];
    foreach ( (array) $families as $family )
      foreach ( padKinds ( padSubsystemKinds [$family] ) as $kind )
        $html [] = '<a href="' . htmlspecialchars ( ( isset ( padSubsystems [$family] ) && $base === '?kind/' ? '?tag/' : $base ) . "$family/$kind", ENT_QUOTES ) . '"><code>' . htmlspecialchars ( $kind ) . '</code></a>';

    return implode ( ', ', $html );

  }

?>
