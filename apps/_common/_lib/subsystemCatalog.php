<?php

  // Shared by the tags and subsystems applications. These families have kinds, but no type:
  // prefix. Read their names from the engine and their descriptions from their files;
  // the examples use the option or helper that actually selects a kind.

  function appSubsystemFamilies () {

    $info = [
      'map'      => [ 'Map views', 'The view parameter of {map:auto} selects a country or region.', 'map', 'world' ],
      'rule'     => [ 'Validation rules', 'Rules passed to padValidate or a form field\'s rules= option.', 'validator', 'required' ],
      'oauth'    => [ 'OAuth providers', 'The provider parameter of {widget:oauth}, padOAuthStart and padOAuthUser.', 'oauth', 'github' ],
      'channel'  => [ 'Notification channels', 'Delivery channels selected in the fourth argument of padNotify.', 'notifications', 'mail' ],
      'ai'       => [ 'AI providers', 'The provider= option of {ai:auto} or padAi.', 'ai', 'anthropic' ],
      'filter'   => [ 'Image filters', 'The filter= option of {image:thumbnail} selects a filter or placeholder.', 'image:thumbnail', 'grayscale' ],
      'holidays' => [ 'Holiday calendars', 'The country parameter of {holidays:auto}, with optional regions.', 'holidays', 'nl' ],
      'search'   => [ 'Search languages', 'The lang= option of {search:auto} selects stop words and a stemmer.', 'search', 'en' ],
      'format'   => [ 'Data formats', 'The format= option of {format:auto}, file extensions and the format helpers.', 'data', 'toml' ],
      'formula'  => [ 'Spreadsheet functions', 'Functions inside the formulas of {sheet:grid} cells.', 'sheet:grid', 'sum' ],
      'theme'    => [ 'Themes', 'The name parameter of {widget:theme} selects page or box colours.', 'theme', 'paper' ],
      'unit' => [ 'Unit conversions', 'Dimension-checked numeric conversion through padConvert.', '', 'length' ],
      'stats' => [ 'Statistics', 'Bounded descriptive statistics and regression through padStats.', '', 'describe' ],
      'graph' => [ 'Graph algorithms', 'Dependency order, components, shortest and critical paths through padGraph.', '', 'toposort' ],
      'schema' => [ 'Schema validation', 'Explicit contracts for JSON values, records and rowsets through padSchema.', '', 'jsonschema' ],
      'textanalysis' => [ 'Text analysis', 'Unicode counts, n-grams and lexical similarity through padTextAnalysis.', '', 'counts' ],
      'solve' => [ 'Constraint solvers', 'Bounded exact cover, bipartite matching and assignment through padSolve.', '', 'assignment' ],
      'highlighter' => [ 'Syntax languages', 'Languages selected by padHighlight and the highlight tag.', 'highlight', 'python' ],
      'diff' => [ 'Comparison kinds', 'Typed change records through padDiffCompare, and escaped HTML through padDiffRender.', 'diff', 'json' ],
      'recurrence' => [ 'Calendar recurrences', 'Bounded occurrences between explicit dates through padOccurrences.', '', 'weekly' ],
      'geo' => [ 'Geographic operations', 'Plain data operations through padGeo, with longitude before latitude.', '', 'distance' ],
      'distribution' => [ 'Sampling distributions', 'Seeded samples through padSample.', '', 'normal' ],
      'interval' => [ 'Interval operations', 'Half-open numeric or instant interval sets through padIntervals.', '', 'subtract' ],
      'color' => [ 'Color computation', 'Explicit color conversion, mixing, contrast and gamut through padColor.', '', 'contrast' ],
      'timeseries' => [ 'Time series', 'Timestamped resampling, windows, smoothing and differences through padTimeSeries.', '', 'resample' ],
      'geometry' => [ 'Planar geometry', 'Convex hulls, simple-ring metrics and rectangle clipping through padGeometry.', '', 'hull' ],
      'numeric' => [ 'Numerical methods', 'Bounded interpolation, integration, roots and linear systems through padNumeric.', '', 'integrate' ],
      'signal' => [ 'Signal analysis', 'Offline spectrum, convolution, correlation and windows through padSignal.', '', 'spectrum' ],
      'palette'  => [ 'Colour palettes', 'The name parameter of {widget:palette}, or palette= on a chart.', 'palette', 'viridis' ],
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
    $docs = basename ( $directory );
    foreach ( [ "docs/subsystems/$docs/$kind.md", "docs/subsystems/$docs.md", "docs/subsystems/$docs/README.md", "docs/subsystems/$docs/index.md" ] as $doc )
      if ( is_file ( dirname ( APPS ) . '/' . $doc ) ) return (string) file_get_contents ( dirname ( APPS ) . '/' . $doc );
    return ucfirst ( $kind ) . ' is selected through the ' . $family . ' subsystem API.';

  }

  function appSubsystemExample ( $family, $kind ) {

    $source = '';
    $language = 'pad';
    $run = FALSE;
    $about = '';

    // Literal helper examples for kinds with distinct argument shapes. Rendering
    // families retain their runnable PAD templates below.
    static $examples = [
      'color' => [
        'contrast' => 'padColor ( \'contrast\', \'#183347\', \'#fff\' );',
        'convert' => 'padColor ( \'convert\', \'#336699\', NULL, [\'to\'=>\'hsl\'] );',
        'gamut' => 'padColor ( \'gamut\', [\'channels\'=>[1.1,0,-.1]], NULL, [\'method\'=>\'clip\'] );',
        'mix' => 'padColor ( \'mix\', \'#f00\', \'#00f\', [\'space\'=>\'linear-srgb\'] );',
      ],
      'diff' => [
        'binary' => 'padDiffCompare(\'binary\',hex2bin(\'004142ff\'),hex2bin(\'004342ff78\'));',
        'sequence' => 'padDiffCompare(\'sequence\',[\'a\',\'b\',\'c\'],[\'b\',\'a\',\'d\']);',
      ],
      'distribution' => [
        'gamma' => 'padSample(\'gamma\',8,[\'shape\'=>2,\'scale\'=>3,\'seed\'=>42]);',
        'geometric' => 'padSample(\'geometric\',8,[\'p\'=>.25,\'seed\'=>42]);',
        'hypergeometric' => 'padSample(\'hypergeometric\',8,[\'population\'=>20,\'successes\'=>7,\'draws\'=>5,\'seed\'=>42]);',
        'weibull' => 'padSample(\'weibull\',8,[\'shape\'=>2,\'scale\'=>3,\'seed\'=>42]);',
      ],
      'format' => [
        'htmltable' => 'padFormatRead(\'htmltable\',\'<table id="sales"><tr><th>Name</th><th>Total</th></tr><tr><td>Ada</td><td>42</td></tr></table>\',[\'table\'=>\'#sales\']);',
        'ods' => 'padFormatRead(\'ods\',padFormatWrite(\'ods\',[[\'Name\'=>\'Ada\',\'Score\'=>42],[\'Name\'=>\'Bo\',\'Score\'=>7]],[\'sheet\'=>\'Scores\']),[\'sheet\'=>\'Scores\']);',
      ],
      'formula' => [
        'choosecols' => 'padFormula(\'=CHOOSECOLS(A1:B2,-1,1)\',[\'A1\'=>1,\'B1\'=>2,\'A2\'=>3,\'B2\'=>4]);',
        'chooserows' => 'padFormula(\'=CHOOSEROWS(A1:B2,-1,1)\',[\'A1\'=>1,\'B1\'=>2,\'A2\'=>3,\'B2\'=>4]);',
        'drop' => 'padFormula(\'=DROP(A1:B2,-1,1)\',[\'A1\'=>1,\'B1\'=>2,\'A2\'=>3,\'B2\'=>4]);',
        'hstack' => 'padFormula(\'=HSTACK(A1:B2,9)\',[\'A1\'=>1,\'B1\'=>2,\'A2\'=>3,\'B2\'=>4]);',
        'sequence' => 'padFormula(\'=SEQUENCE(2,3,10,-2)\',[\'A1\'=>1,\'B1\'=>2,\'A2\'=>3,\'B2\'=>4]);',
        'take' => 'padFormula(\'=TAKE(A1:B2,-1,1)\',[\'A1\'=>1,\'B1\'=>2,\'A2\'=>3,\'B2\'=>4]);',
        'transpose' => 'padFormula(\'=TRANSPOSE(A1:B2)\',[\'A1\'=>1,\'B1\'=>2,\'A2\'=>3,\'B2\'=>4]);',
        'vstack' => 'padFormula(\'=VSTACK(A1:B2,9)\',[\'A1\'=>1,\'B1\'=>2,\'A2\'=>3,\'B2\'=>4]);',
      ],
      'geo' => [
        'polygon-area' => 'padGeo(\'polygon-area\',[\'type\'=>\'Polygon\',\'coordinates\'=>[[[4,51],[5,51],[5,52],[4,52],[4,51]]]],NULL,[\'unit\'=>\'km2\']);',
        'simplify' => 'padGeo(\'simplify\',[\'type\'=>\'LineString\',\'coordinates\'=>[[4,52],[4.01,52.001],[4.02,52]]],NULL,[\'tolerance\'=>200]);',
      ],
      'geometry' => [
        'area' => 'padGeometry ( \'area\', [[0,0],[4,0],[4,4],[0,4]] );',
        'centroid' => 'padGeometry ( \'centroid\', [[0,0],[4,0],[4,4],[0,4]] );',
        'clip' => 'padGeometry ( \'clip\', [[0,0],[4,0],[4,4],[0,4]], [\'box\'=>[1,1,3,3]] );',
        'hull' => 'padGeometry ( \'hull\', [[0,0],[4,0],[4,4],[0,4]] );',
      ],
      'graph' => [
        'spanningtree' => 'padGraph(\'spanningtree\',[\'a\',\'b\',\'c\'],[[\'a\',\'b\',5],[\'a\',\'c\',2],[\'b\',\'c\',1]]);',
        'strongcomponents' => 'padGraph(\'strongcomponents\',[\'a\',\'b\',\'c\'],[[\'a\',\'b\'],[\'b\',\'a\'],[\'b\',\'c\']]);',
      ],
      'interval' => [
        'intersection' => 'padIntervals ( \'intersection\', [[\'start\'=>0,\'end\'=>5]], [[\'start\'=>2,\'end\'=>3]] );',
        'overlaps' => 'padIntervals ( \'overlaps\', [[\'start\'=>0,\'end\'=>5]], [[\'start\'=>2,\'end\'=>3]] );',
        'subtract' => 'padIntervals ( \'subtract\', [[\'start\'=>0,\'end\'=>5]], [[\'start\'=>2,\'end\'=>3]] );',
        'union' => 'padIntervals ( \'union\', [[\'start\'=>0,\'end\'=>5]], [[\'start\'=>2,\'end\'=>3]] );',
      ],
      'numeric' => [
        'integrate' => 'padNumeric(\'integrate\',[[0,0],[1,2],[2,4]],[\'method\'=>\'trapezoid\']);',
        'interpolate' => 'padNumeric(\'interpolate\',[[0,0],[2,4],[5,10]],[\'at\'=>[1,3]]);',
        'linear-system' => 'padNumeric(\'linear-system\',[\'matrix\'=>[[2,1],[1,3]],\'rhs\'=>[5,7]]);',
        'root' => 'padNumeric(\'root\',[\'function\'=>fn($x)=>$x*$x-2,\'lower\'=>0,\'upper\'=>2],[\'tolerance\'=>1e-10]);',
      ],
      'recurrence' => [
        'dates' => 'padOccurrences(\'dates\',[\'mode\'=>\'date\',\'dates\'=>[\'2026-11-10\',\'2026-11-02\',\'2026-11-10\']],\'2026-11-01\',\'2026-12-01\');',
        'hourly' => 'padOccurrences(\'hourly\',[\'start\'=>\'2026-10-25T00:30:00+02:00\',\'timezone\'=>\'Europe/Amsterdam\',\'step\'=>\'elapsed\'],\'2026-10-25\',\'2026-10-25T05:00\');',
      ],
      'rule' => [
        'dateformat' => 'padValidate ( [\'value\'=>\'dateformat:Y-m-d\'], [\'value\'=>\'2026-11-02\'] );',
        'prohibited_if' => 'padValidate ( [\'value\'=>\'prohibited_if:status,closed\'], [\'status\'=>\'closed\'] );',
        'required_if_any' => 'padValidate ( [\'value\'=>\'required_if_any:status,ready;priority,high\'], [\'value\'=>\'Confirmed\',\'status\'=>\'ready\'] );',
      ],
      'schema' => [
        'relation' => 'padSchema(\'relation\',[\'people\'=>[[\'id\'=>1]],\'orders\'=>[[\'person\'=>1]]],[\'primary\'=>[[\'table\'=>\'people\',\'fields\'=>[\'id\']]],\'foreign\'=>[[\'table\'=>\'orders\',\'fields\'=>[\'person\'],\'references\'=>[\'table\'=>\'people\',\'fields\'=>[\'id\']]]]]);',
      ],
      'signal' => [
        'convolve' => 'padSignal(\'convolve\',[\'signal\'=>[1,2,3],\'kernel\'=>[.5,.5]],[\'mode\'=>\'full\']);',
        'correlate' => 'padSignal(\'correlate\',[\'a\'=>[1,0,0],\'b\'=>[0,0,1]],[\'sampleRate\'=>10,\'normalization\'=>\'none\']);',
        'spectrum' => 'padSignal(\'spectrum\',[0,1,0,-1,0,1,0,-1],[\'sampleRate\'=>8,\'window\'=>\'rectangular\',\'scaling\'=>\'amplitude\']);',
        'window' => 'padSignal(\'window\',[1,1,1,1,1],[\'window\'=>\'hann\']);',
      ],
      'solve' => [
        'coloring' => 'padSolve(\'coloring\',[\'nodes\'=>[\'a\',\'b\',\'c\'],\'edges\'=>[[\'a\',\'b\'],[\'b\',\'c\']],\'colors\'=>2]);',
        'knapsack' => 'padSolve(\'knapsack\',[\'items\'=>[[\'id\'=>\'A\',\'weight\'=>3,\'value\'=>5],[\'id\'=>\'B\',\'weight\'=>4,\'value\'=>8]],\'capacity\'=>5]);',
      ],
      'stats' => [
        'bootstrap' => 'padStats(\'bootstrap\',[1,2,3,4,5],[\'seed\'=>42,\'resamples\'=>500,\'interval\'=>\'percentile\']);',
        'correlation' => 'padStats(\'correlation\',[[\'x\'=>1,\'y\'=>2],[\'x\'=>2,\'y\'=>5],[\'x\'=>3,\'y\'=>6]],[\'method\'=>\'pearson\']);',
        'weighted' => 'padStats(\'weighted\',[[\'value\'=>1,\'weight\'=>2],[\'value\'=>4,\'weight\'=>1]],[\'semantics\'=>\'frequency\']);',
      ],
      'textanalysis' => [
        'concordance' => 'padTextAnalysis(\'concordance\',\'PAD pages use PAD templates.\',[\'normalization\'=>\'none\',\'query\'=>\'pad\',\'context\'=>12]);',
        'keywords' => 'padTextAnalysis(\'keywords\',[\'first\'=>\'PAD templates produce pages\',\'second\'=>\'PAD pages contain data\'],[\'normalization\'=>\'none\',\'method\'=>\'tfidf\']);',
      ],
      'timeseries' => [
        'difference' => 'padTimeSeries ( \'difference\', [[\'time\'=>0,\'value\'=>2],[\'time\'=>1,\'value\'=>4],[\'time\'=>4,\'value\'=>8]], [\'lag\'=>1] );',
        'ewma' => 'padTimeSeries ( \'ewma\', [[\'time\'=>0,\'value\'=>2],[\'time\'=>1,\'value\'=>4],[\'time\'=>4,\'value\'=>8]], [\'alpha\'=>0.5] );',
        'resample' => 'padTimeSeries ( \'resample\', [[\'time\'=>0,\'value\'=>2],[\'time\'=>1,\'value\'=>4],[\'time\'=>4,\'value\'=>8]], [\'stepSeconds\'=>2] );',
        'rolling' => 'padTimeSeries ( \'rolling\', [[\'time\'=>0,\'value\'=>2],[\'time\'=>1,\'value\'=>4],[\'time\'=>4,\'value\'=>8]], [\'window\'=>2] );',
      ],
      'unit' => [
        'acceleration' => 'padConvert(1,\'g0\',\'m/s2\');',
        'density' => 'padConvert(1,\'lb/ft3\',\'kg/m3\');',
        'force' => 'padConvert(1,\'kgf\',\'N\');',
        'frequency' => 'padConvert(1,\'rpm\',\'Hz\');',
        'torque' => 'padConvert(1,\'lbf*in\',\'N*m\');',
      ],
    ];
    if ( isset ( $examples [$family] [$kind] ) ) return [ 'source' => $examples [$family] [$kind], 'language' => 'php',
      'run' => FALSE, 'about' => 'A complete helper call. See the subsystem documentation for limits and result fields.' ];

    switch ( $family ) {
      case 'unit':
        $language = 'php';
        $pairs = [ 'length' => [ 'cm', 'm' ], 'area' => [ 'm2', 'ft2' ], 'volume' => [ 'L', 'mL' ], 'mass' => [ 'kg', 'g' ], 'temperature' => [ 'C', 'F' ], 'digital' => [ 'KiB', 'B' ],
          'duration'=>['min','h'], 'speed'=>['km/h','m/s'], 'pressure'=>['kPa','bar'], 'energy'=>['kJ','Wh'], 'power'=>['kW','W'], 'angle'=>['deg','rad'] ];
        [ $a, $b ] = $pairs [$kind];
        $source = "padConvert ( 100, '$a', '$b' );";
        break;
      case 'highlighter':
        $language = 'php'; $source = "padHighlight ( \$source, '$kind', TRUE );";
        break;
      case 'diff':
        $language = 'php'; $args = $kind === 'rows' ? ", [ 'key' => 'id' ]" : '';
        $source = "\$changes = padDiffCompare ( '$kind', \$before, \$after$args );\npadDiffRender ( \$changes, TRUE );";
        if ( $kind === 'unified' ) $source = "include_once PAD . 'subs/diffs/_lib/unified.php';\n\$hunks = padDiffUnifiedHunks ( \$before, \$after, 3 );\necho padDiffUnified ( \$before, \$after, ['context'=>3] );";
        break;
      case 'stats':
        $language = 'php';
        $rows = $kind === 'regression' ? "[['x'=>1,'y'=>2],['x'=>2,'y'=>4],['x'=>3,'y'=>5]]" : '[1,2,2,3,5,8]';
        $source = "padStats ( '$kind', $rows );";
        $about = 'Returns plain data. A runnable table/chart consumer is in Showcase / analysis / statistics.';
        break;
      case 'graph':
        $language = 'php';
        $nodes = $kind === 'criticalpath' ? "[['id'=>'a','duration'=>2],['id'=>'b','duration'=>3],['id'=>'c','duration'=>1]]" : "['a','b','c']";
        $options = $kind === 'shortestpath' ? ", ['source'=>'a','target'=>'c']" : '';
        $source = "padGraph ( '$kind', $nodes, [['a','b'],['b','c']]$options );";
        $about = 'Stable input-order ties; cycles are explicit results. See Showcase / analysis / dependencies.';
        break;
      case 'schema':
        $language = 'php';
        $value = [ 'jsonschema'=>"(object)['name'=>'Ada']", 'record'=>"['name'=>'Ada']", 'rowset'=>"[['name'=>'Ada'],['name'=>'Bo']]" ] [$kind];
        $source = "\$schema = ['type'=>'object','properties'=>['name'=>['type'=>'string']],'required'=>['name'],'additionalProperties'=>false];\npadSchema ( '$kind', $value, \$schema );";
        $about = 'A documented JSON Schema subset, without coercion or remote references. See Showcase / analysis / import-contract.';
        break;
      case 'textanalysis':
        $language = 'php';
        $value = $kind === 'similarity' ? "['a'=>'red blue','b'=>'blue green']" : "'Red blue, red green.'";
        $source = "padTextAnalysis ( '$kind', $value );";
        $about = 'NFC requires PHP intl; normalization=none is an explicit alternative. See Showcase / analysis / duplicates.';
        break;
      case 'solve':
        $language = 'php';
        $problem = [
          'exactcover'=>"['columns'=>['a','b'],'rows'=>['both'=>['a','b'],'left'=>['a'],'right'=>['b']]]",
          'matching'=>"['left'=>['Ada','Bo'],'right'=>['edit','test'],'edges'=>[['Ada','edit'],['Bo','test']]]",
          'assignment'=>"['rows'=>['Ada','Bo'],'columns'=>['edit','test'],'costs'=>[[2,5],[4,1]]]"
        ] [$kind];
        $source = "padSolve ( '$kind', $problem, ['maxSteps'=>100000] );";
        $about = 'Check status: solved, infeasible, limit or cancelled. Showcase / analysis contains allocation and exact-cover consumers.';
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
        $args = [ 'distance' => '[4.9,52.37], [2.35,48.86]', 'bearing' => '[4.9,52.37], [2.35,48.86]', 'bbox' => '[[179,10],[-179,20]]', 'contains' => "[1,1], ['type'=>'Polygon', 'coordinates'=>[[[0,0],[2,0],[2,2],[0,2],[0,0]]]]", 'geohash' => '[4.9,52.37]',
          'destination'=>"[4.9,52.37], 100, ['bearing'=>90,'unit'=>'km']", 'route-length'=>"[[4.9,52.37],[2.35,48.86]], NULL, ['unit'=>'km']", 'nearest'=>"[4.9,52.37], ['Paris'=>[2.35,48.86],'London'=>[-.12,51.5]]" ];
        $source = "padGeo ( '$kind', " . $args [$kind] . ' );';
        break;
      case 'map':
        $source = "{map:auto '$kind', width=540/}";
        $run = TRUE;
        $about = 'The view without data; data= and value= colour its regions.';
        break;
      case 'oauth':
        $source = "{widget:oauth '$kind', href='#example'/}";
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
        if ( is_file ( APPS . "tags/examples/formula/$kind.pad" ) ) {
          $source = trim ( padFileGet ( APPS . "tags/examples/formula/$kind.pad" ) );
          $run = TRUE;
          $about = 'A complete computed example using the shared spreadsheet evaluator.';
          break;
        }
        $text = appSubsystemComment ( $family, $kind );
        if ( preg_match ( '/^  (=\S.*?)(?:[ \t]{2,}\S.*)?$/m', $text, $match ) ) {
          $formula = str_replace ( [ '\\', "'" ], [ '\\\\', "\\'" ], $match [1] );
          $sample = trim ( (string) padFileGet ( APPS . 'tables/_data/sample.csv' ) );
          $source = "{sheet:grid formulas='G1$formula'}\n$sample\n{/sheet:grid}";
          $run = TRUE;
          $about = 'The first example from the function\'s source, computed in G1 on the Tables application\'s sample data.';
        } else {
          $source = strtoupper ( $kind ) . '(...)';
          $about = 'Use this function inside a spreadsheet cell beginning with =; its arguments are described below.';
        }
        break;
      case 'theme':
        $source = "{widget:theme '$kind'}<p>A box in the $kind theme.</p>{/widget:theme}";
        $run = TRUE;
        break;
      case 'palette':
        $source = "{widget:palette '$kind'/}";
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
