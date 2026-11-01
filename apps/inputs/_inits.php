<?php

  // The menu of the frame every PAD application shares (_common/_tags/padFrame.pad): the home
  // page, the form with every field, and a page per kind - the kinds the engine has,
  // pad/inputs/types/. A kind's page is its own file here, <kind>.pad, with its fields and
  // their rules written out - the rules of a form are read from the page's own text - and
  // [kind].pad shows a kind that has no page of its own yet.

  // A list, not keyed by kind: an array of the page holding a key slider is what {slider}
  // reads first - a bare kind comes after every other kind of tag - and the fields of the
  // pages would be its value.

  $inputs = array_values ( inputsCatalog () );

  $frameMenu = [ [ 'link' => '?index', 'label' => 'All fields', 'icon' => 'grid', 'current' => $padPage == 'index' ],
                 [ 'link' => '?form',  'label' => 'One form',   'icon' => 'edit', 'current' => $padPage == 'form' ],
                 [ 'head' => 'Fields' ] ];

  foreach ( $inputs as $one )
    $frameMenu [] = [ 'link' => '?' . $one ['kind'], 'label' => $one ['kind'], 'icon' => 'play',
                      'current' => ( $padPage == $one ['kind'] or ( $padPage == '[kind]' and ( $kind ?? '' ) == $one ['kind'] ) ) ];

  // The page's kind and what its file says of it, and what a post of the page's form brought:
  // every field as the page's PHP received it, and whether it kept the rules of the template -
  // checked before any of this PHP ran.

  // The icon that takes a page's form to the playground (_common/_lib/playground.php): the
  // {form} of its template, the field or fields it tries - the rest of the page is this
  // application's. [kind].php makes its own.

  include_once COMMON . '_lib/playground.php';

  $playText = (string) @file_get_contents ( APP . "$padPage.pad" );
  $play     = preg_match ( '#\{form\b.*?\{/form\}#s', $playText, $playForm ) ? padPlayLink ( $playForm [0] . "\n" ) : '';

  unset ( $playText, $playForm );

  $about    = inputsCatalog () [$padPage] ['text'] ?? '';
  $sent     = padFormCameBack ();
  $gotRows  = $sent ? inputsReceived () : [];
  $passed   = padPosted ();

?>
