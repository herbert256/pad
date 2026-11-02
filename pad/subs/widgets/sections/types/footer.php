<?php

  // {section:footer brand='PAD', note='© 2026 PAD'} {section:item 'Product'}Features | ?features
  // {/section:item} ... {/section:footer} - the foot of a page: the name and a line about it on
  // one side, columns of links on the other, and the small print under them. A column is a
  // {section:item} pair, its first value the column's heading and its content the links, a
  // line each - Label | ?page, a link of your own, or plain text - or the rows of data=,
  // a link each, their fields column, label and link, the rows of one column together in the
  // order they come. brand= the name (a slot of markup - a logo - in its place), text= or the
  // content the line under it, note= the small print.
  //
  // Parts: brand, text, note. Options: brand, text, note, data, level (the columns' headings,
  // 2), tone, align (left), id.

  padSectionParts ( [ 'brand', 'text', 'note' ] );

  $GLOBALS ['padSectionNow'] ['align'] = 'left';

  $padSecBrand = padSectionPart ( 'brand' );
  $padSecText  = padSectionPart ( 'text' );
  $padSecNote  = padSectionPart ( 'note' );
  $padSecLevel = padSectionLevel ();
  $padSecItems = padSectionItems ( 'title', [ 'title', 'column', 'label', 'link' ], 'links', 'columns' );

  // The columns: an item with links of its own is one, rows of the same column= are gathered
  // into one, where its first row stands.

  $padSecCols = [];

  foreach ( $padSecItems as $padSecItem ) {

    if ( isset ( $padSecItem ['html'] ['links'] ) ) {
      $padSecCols [] = [ padSectionField ( $padSecItem, 'title' ), padSectionFooterLinks ( $padSecItem ['html'] ['links'] ) ];
      continue;
    }

    $padSecKey  = 'column:' . trim ( (string) padSectionRaw ( $padSecItem, 'column', padSectionRaw ( $padSecItem, 'title' ) ) );
    $padSecHref = padSectionLink ( padSectionRaw ( $padSecItem, 'link' ) );
    $padSecOne  = padSectionField ( $padSecItem, 'label' );
    $padSecOne  = ( $padSecHref !== '' ) ? "<a href=\"$padSecHref\">$padSecOne</a>" : $padSecOne;

    if ( ! isset ( $padSecCols [$padSecKey] ) )
      $padSecCols [$padSecKey] = [ padSectionEscape ( substr ( $padSecKey, 7 ) ), '' ];

    if ( trim ( strip_tags ( $padSecOne ) ) !== '' )
      $padSecCols [$padSecKey] [1] .= "<li>$padSecOne</li>";

  }

  $padSecNav = '';

  foreach ( $padSecCols as [ $padSecHead, $padSecLinks ] )
    $padSecNav .= '<div class="ps-footer-column">'
                . ( $padSecHead !== '' ? "<h$padSecLevel class=\"ps-footer-head\">$padSecHead</h$padSecLevel>" : '' )
                . "<ul>$padSecLinks</ul></div>";

  $padSecAbout = ( $padSecBrand !== '' ? "<p class=\"ps-footer-brand\">$padSecBrand</p>" : '' )
               . ( $padSecText  !== '' ? padSectionTextBlock ( $padSecText, 'ps-footer-text' ) : '' );

  return padSection ( 'footer', '<div class="ps-footer">'
                              . ( $padSecAbout !== '' ? "<div class=\"ps-footer-about\">$padSecAbout</div>" : '' )
                              . ( $padSecNav   !== '' ? "<nav class=\"ps-footer-nav\" aria-label=\"Footer\">$padSecNav</nav>" : '' )
                              . '</div>'
                              . ( $padSecNote !== '' ? "<p class=\"ps-footer-note\">$padSecNote</p>" : '' ) );

?>
