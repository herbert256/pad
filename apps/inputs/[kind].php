<?php

  // A kind of the engine that has no page of its own here yet - ?name, /inputs/name - shown in
  // a form of its own, without rules: the rules of a form are read from the page's own text,
  // and this page writes the field from a variable. Any other name is a page not found.

  if ( ! isset ( inputsCatalog () [$kind] ) )
    padAbort ( 404 );

  $title    = $kind;
  $about    = inputsCatalog () [$kind] ['text'];
  $template = "{input:form 'try', class='inputs-try'}\n  {input:$kind '$kind', label='" . ucfirst ( $kind ) . "'/}\n  <button class=\"inputs-send\">Send</button>\n{/input:form}";
  $source   = padHighlightTokens ( $template, 'pad' );

  // The icon at the top right of the template that takes it to the WASM editor
  // (_common/_lib/playground.php).

  include_once COMMON . '_lib/playground.php';

  $play     = padPlayLink ( "$template\n" );

?>
