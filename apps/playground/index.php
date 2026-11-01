<?php

  // The playground's editor: a template and its data on the left, what they render to on
  // the right. The data is a text in any format PAD reads - JSON, YAML, XML, CSV ... - or,
  // switched with the button beside its label, PHP code as a page's .php; each has its own
  // pane, with the same sample in it - the text's in JSON. The form
  // posts to ?render into the output frame, which is sandboxed - the rendered page's own
  // scripts do not run, and it cannot reach the playground or its cookies.
  // www/playground/playground.js keeps the panes in the URL hash, so a link carries the
  // example, switches the data pane, and renders on load and on Ctrl+Enter.

  $sampleSource = "<h2>{\$title}</h2>\n<ul>\n  {items}\n    <li>{\$name}{if \$stock eq 0} - sold out{/if}</li>\n  {/items}\n</ul>\n";

  $sampleData = "{\n  \"title\": \"Fruit\",\n  \"items\": [\n    { \"name\": \"Apple\", \"stock\": 3 },\n    { \"name\": \"Pear\",  \"stock\": 0 }\n  ]\n}\n";

  // The boxes become editors with colours when Monaco loads from $playMonaco: the template
  // in the PAD colouring of the edit application - its own www/edit/pad-mode.js, linked
  // with the version of its contents as {asset} links a file of the playground - and the
  // names it colours, PAD's built-in tags, functions, options, properties and prefixes as
  // the edit application gathers them (apps/edit/_lib/language.php) from
  // editors/lsp/completions.json, which editors/generate.php keeps in step with pad/. No
  // documentation: the colours and the completion of names need none.

  $editorMonaco  = $playMonaco ?? '';
  $editorPadMode = '';
  $editorNames   = [];

  if ( $editorMonaco ) {

    $editorFile    = dirname ( APPS ) . '/www/edit/pad-mode.js';
    $editorAlgo    = in_array ( 'xxh128', hash_algos (), TRUE ) ? 'xxh128' : 'sha256';
    $editorPadMode = is_file ( $editorFile ) ? "{$padRoot}edit/pad-mode.js?v=" . substr ( (string) hash_file ( $editorAlgo, $editorFile ), 0, 10 ) : '';

    $editorGroups  = [ 'PAD tag' => 'tags', 'pipe function' => 'functions', 'property@tag' => 'properties',
                       'option' => 'options', 'type prefix' => 'prefixes', 'operator' => 'operators',
                       'sequence type' => 'sequences' ];

    $editorNames   = array_fill_keys ( array_values ( $editorGroups ), [] ) + [ 'single' => [] ];

    foreach ( json_decode ( (string) @file_get_contents ( dirname ( APPS ) . '/editors/lsp/completions.json' ), TRUE ) ?: [] as $editorOne ) {
      if ( isset ( $editorGroups [ $editorOne ['detail'] ?? '' ] ) )
        $editorNames [ $editorGroups [ $editorOne ['detail'] ] ] [] = rtrim ( $editorOne ['label'], ':' );
      if ( ! empty ( $editorOne ['single'] ) and ( $editorOne ['detail'] ?? '' ) == 'PAD tag' )
        $editorNames ['single'] [] = $editorOne ['label'];
    }

    $editorFiles = fn ( $dir ) => array_values ( array_filter ( array_map ( fn ( $f ) => basename ( $f, '.php' ), glob ( "$dir/*.php" ) ?: [] ),
                                                                fn ( $n ) => $n [0] != '_' ) );

    $editorNames ['actions']    = $editorFiles ( PAD . 'sequence/actions/types' );
    $editorNames ['constructs'] = $editorFiles ( PAD . 'constructs' );
    $editorNames ['branch']     = [ 'else', 'elseif', 'when' ];

    unset ( $editorFile, $editorAlgo, $editorGroups, $editorOne, $editorFiles );

  }

  $samplePhp = "<?php\n\n  \$title = 'Fruit';\n\n  \$items = [\n    [ 'name' => 'Apple', 'stock' => 3 ],\n    [ 'name' => 'Pear',  'stock' => 0 ]\n  ];\n\n?>\n";

?>
