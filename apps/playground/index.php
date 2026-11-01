<?php

  // The playground's editor: a template and its data on the left, what they render to on
  // the right. The data is a text in any format PAD reads - JSON, YAML, XML, CSV ... - or,
  // switched with the button beside its label, PHP code as a page's .php; each has its own
  // pane, with the same sample in it - the text's in JSON. The form
  // posts to ?render into the output frame, which is sandboxed with scripts allowed and
  // nothing else - the rendered page's own scripts run, so a toy moves and a game plays, but
  // in an origin of their own: they cannot reach the playground, its form and its token,
  // this site's cookies or storage, or open or move a window.
  // www/playground/playground.js keeps the panes in the URL hash, so a link carries the
  // example, switches the data pane, and renders on load and on Ctrl+Enter.

  include_once COMMON . '_lib/playground.php';

  [ $sampleSource, $sampleData, $samplePhp ] = padPlaySamples ();

  // The boxes become editors with colours when Monaco loads from $playMonaco, in the PAD
  // colouring of the edit application (_common/_lib/playground.php, padPlayEditor).

  $editorMonaco = $playMonaco ?? '';

  [ $editorPadMode, $editorNames ] = $editorMonaco ? padPlayEditor () : [ '', [] ];

?>
