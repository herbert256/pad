<?php

  // Shared by the tags and types applications. These families have kinds, but no type:
  // prefix. Read their names from the engine and their descriptions from their files;
  // the examples use the option or helper that actually selects a kind.

  function appSubsystemFamilies () {

    $info = [
      'map'      => [ 'Map views', 'The view parameter of {pad:map} selects a country or region.', 'map', 'world' ],
      'rule'     => [ 'Validation rules', 'Rules passed to padValidate or a form field\'s rules= option.', 'validator', 'required' ],
      'oauth'    => [ 'OAuth providers', 'The provider parameter of {pad:oauth}, padOAuthStart and padOAuthUser.', 'oauth', 'github' ],
      'channel'  => [ 'Notification channels', 'Delivery channels selected in the fourth argument of padNotify.', 'notifications', 'mail' ],
      'ai'       => [ 'AI providers', 'The provider= option of {pad:ai} or padAi.', 'ai', 'anthropic' ],
      'filter'   => [ 'Image filters', 'The filter= option of {pad:img} selects a filter or placeholder.', 'img', 'grayscale' ],
      'holidays' => [ 'Holiday calendars', 'The country parameter of {pad:holidays}, with optional regions.', 'holidays', 'nl' ],
      'search'   => [ 'Search languages', 'The lang= option of {pad:search} selects stop words and a stemmer.', 'search', 'en' ],
      'format'   => [ 'Data formats', 'The format= option of {pad:data}, file extensions and the format helpers.', 'data', 'toml' ],
      'formula'  => [ 'Spreadsheet functions', 'Functions inside the formulas of {pad:spreadsheet} cells.', 'spreadsheet', 'sum' ],
      'theme'    => [ 'Themes', 'The name parameter of {pad:theme} selects page or box colours.', 'theme', 'paper' ],
      'palette'  => [ 'Colour palettes', 'The name parameter of {pad:palette}, or palette= on a chart.', 'palette', 'viridis' ],
    ];

    $families = [];

    foreach ( padSubsystemKinds as $key => $directory ) {
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

    return trim ( preg_replace ( '/^[ \t]*\/\/ ?/m', '', $match [1] ?? '' ) );

  }

  function appSubsystemExample ( $family, $kind ) {

    $source = '';
    $language = 'pad';
    $run = FALSE;
    $about = '';

    switch ( $family ) {
      case 'map':
        $source = "{pad:map '$kind', width=540/}";
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
        $source = "{pad:ai 'Name three rivers of France.', provider='$kind'/}";
        $about = 'Set up the provider and its credentials first. Shown as source because it calls a model service.';
        break;
      case 'filter':
        $source = "{pad:img 'photo.jpg', alt='A landscape', width=320, filter='$kind'/}";
        $about = 'Put photo.jpg in the application\'s www directory before running this example.';
        break;
      case 'holidays':
        $source = "{pad:holidays '$kind', year=2026/}";
        $run = TRUE;
        break;
      case 'search':
        $source = "{pad:data 'catalogDocs'}\n[ { \"title\": \"PAD\", \"body\": \"Templates on the server\" } ]\n{/pad:data}\n{pad:search 'catalogDocs', q='PAD', fields='title, body', lang='$kind'}\n  <p>{\$title}: {!excerpt}</p>\n{/pad:search}";
        $run = TRUE;
        break;
      case 'format':
        $language = 'php';
        $definition = padFormatDef ( $kind );
        if ( isset ( $definition ['read'] ) ) $source .= "\$rows = padFormatRead ( '$kind', \$contents );\n";
        if ( isset ( $definition ['write'] ) ) $source .= "\$contents = padFormatWrite ( '$kind', \$rows );";
        $about = 'Supply the document bytes in $contents or the data in $rows. The description below explains the format\'s record shape; XLSX takes binary bytes.';
        break;
      case 'formula':
        $text = appSubsystemComment ( $family, $kind );
        if ( preg_match ( '/^  (=\S.*?)(?:[ \t]{2,}\S.*)?$/m', $text, $match ) ) {
          $formula = str_replace ( [ '\\', "'" ], [ '\\\\', "\\'" ], $match [1] );
          $sample = trim ( (string) padFileGet ( APPS . 'sheets/_data/sample.csv' ) );
          $source = "{pad:spreadsheet formulas='G1$formula'}\n$sample\n{/pad:spreadsheet}";
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
          'parms' => [], 'options' => [], 'see' => $info ['tag'] ? [ 'pad:' . $info ['tag'] ] : []
        ];
      }

    return $catalog;

  }

  function appSubsystemLinks ( $families, $base = '?kind/' ) {

    $html = [];
    foreach ( (array) $families as $family )
      foreach ( padKinds ( padSubsystemKinds [$family] ) as $kind )
        $html [] = '<a href="' . htmlspecialchars ( "$base$family/$kind", ENT_QUOTES ) . '"><code>' . htmlspecialchars ( $kind ) . '</code></a>';

    return implode ( ', ', $html );

  }

?>
