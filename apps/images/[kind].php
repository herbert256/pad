<?php

  // A pattern, by the name in the address - ?topography, /patterns/topography: one of the
  // engine's patterns, else a page not found. It is drawn large with the seed the address asks
  // for - ?topography&seed=8 - beside the template that draws it and the links to the seeds
  // next to it; then the example its file gives, and the same pattern as the background of a
  // box, written as a CSS url().

  $one = array_values ( array_filter ( $patterns, fn ( $item ) => $item ['kind'] === $kind ) ) [0] ?? NULL;

  if ( ! $one )
    padAbort ( 404 );

  $title = $one ['name'];
  $text  = $one ['text'];
  $seed  = patternsSeed ( padRequest ( 'seed', 1 ) );
  $prev  = max ( 0, $seed - 1 );
  $next  = min ( 999999, $seed + 1 );

  // Whether the seed changes the picture at all: a tile repeated is the same for every seed.
  // Compared as url(), whose ids do not follow from the seed.

  $seeded = padPattern ( $kind, [ 'seed' => 1, 'as' => 'url' ] ) !== padPattern ( $kind, [ 'seed' => 2, 'as' => 'url' ] );

  $template = "{pattern:$kind seed=$seed, width=960, height=320/}";
  $example  = $one ['example'];
  $backdrop = "<div style=\"background-image:{pattern:$kind seed=$seed, as='url'/}; background-size:cover; padding:48px 24px\">\n"
            . "  <p style=\"margin:0; padding:12px 16px; background:#fffc; color:#222; display:inline-block\">Text over a pattern</p>\n"
            . "</div>";

  // The templates coloured, and the icons at their top right that take them to the WASM editor
  // and the live application (_common/_lib/playground.php).

  include_once COMMON . '_lib/playground.php';

  $source         = padHighlightTokens ( $template, 'pad' );
  $play           = padPlayLink ( "$template\n" );
  $exampleSource  = ( $example !== '' ) ? padHighlightTokens ( $example, 'pad' ) : '';
  $examplePlay    = ( $example !== '' ) ? padPlayLink ( "$example\n" ) : '';
  $backdropSource = padHighlightTokens ( $backdrop, 'pad' );
  $backdropPlay   = padPlayLink ( "$backdrop\n" );

?>
