<?php

  // The live application's editor: a template and its data on the left, the page they make on
  // the right. As in the playground, the data is a text in any format PAD reads - JSON, YAML,
  // XML, CSV ... - or, switched with the button beside its label, PHP code as a page's .php.
  // Run posts them to ?save, which saves them as a page of this server and sends the output
  // frame there: the page is a real request, answered as any page is - its PHP, request
  // values, sessions, forms that post back, scripts. www/live/live.js keeps the boxes in the
  // URL hash, so a link carries the example, and runs on load and on Ctrl+Enter.

  include_once COMMON . '_lib/playground.php';

  [ $sampleSource, $sampleData, $samplePhp ] = padPlaySamples ();

  // The boxes become editors with colours when Monaco loads from $liveMonaco, in the PAD
  // colouring of the edit application (_common/_lib/playground.php, padPlayEditor).

  $editorMonaco = $liveMonaco ?? '';

  [ $editorPadMode, $editorNames ] = $editorMonaco ? padPlayEditor () : [ '', [] ];

?>
