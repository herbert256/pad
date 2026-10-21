<?php

  $title = 'Reference';

  // The frame every PAD application shares (_common/_tags/padFrame.pad) instead of the
  // _common wrapper. Its menu is the index's sections (_data/reference.json), each a link to
  // its heading there: a section of the first level with parts of its own is a heading of
  // the menu, the ones without - Data handling, Data types ... - stand together at the end.

  $commonFramed = TRUE;

  $frameMenu = [ [ 'link' => '?index', 'label' => 'Index', 'icon' => 'home', 'current' => $padPage == 'index' ] ];
  $referenceMore = [];

  foreach ( json_decode ( padFileGet ( APP . '_data/reference.json' ), TRUE ) as $referenceRow ) {

    $referenceLink = [ 'link' => '?index#' . padStrSlug ( $referenceRow ['type'] ), 'label' => $referenceRow ['type'] ];

    if ( $referenceRow ['lvl'] > 1 )         $frameMenu []     = $referenceLink;
    elseif ( $referenceRow ['xref'] === '' ) $frameMenu []     = [ 'head' => $referenceRow ['type'] ];
    else                                     $referenceMore [] = $referenceLink;

  }

  if ( $referenceMore )
    $frameMenu = array_merge ( $frameMenu, [ [ 'head' => 'More' ] ], $referenceMore );

  unset ( $referenceMore, $referenceRow, $referenceLink );

?>
