<?php

  // {section:features title='heading'} {section:item 'Fast', icon='zap'}...{/section:item} ...
  // {/section:features} - what a product does, a card per feature in a grid: an icon, a short
  // heading and a line or two. The features are the rows of data= - their fields title, text,
  // icon (a name of {icon}) and link - or {section:item} pairs, the item's first value its
  // title and its content its text. columns= the cards side by side at the widest, 1 to 6
  // (3); the grid takes fewer as the section narrows.
  //
  // Parts: eyebrow, title, text. Options: title (or the first value), eyebrow, text, columns,
  // data, level, tone, align, id.

  padSectionParts ( [ 'eyebrow', 'title', 'text' ] );

  $padSecHead  = padSectionHead ();
  $padSecCols  = padSectionInt ( 'columns', 3, 1, 6 );
  $padSecItems = padSectionItems ( 'title', [ 'title', 'text', 'icon', 'link' ], 'text', 'features' );
  $padSecLevel = min ( 6, padSectionLevel () + 1 );
  $padSecCards = '';

  foreach ( $padSecItems as $padSecItem ) {

    $padSecIcon  = padSectionIcon ( padSectionRaw ( $padSecItem, 'icon' ) );
    $padSecTitle = padSectionField ( $padSecItem, 'title' );
    $padSecLink  = padSectionLink ( padSectionRaw ( $padSecItem, 'link' ) );
    $padSecText  = padSectionField ( $padSecItem, 'text' );

    if ( $padSecLink !== '' and $padSecTitle !== '' )
      $padSecTitle = "<a href=\"$padSecLink\">$padSecTitle</a>";

    $padSecCards .= '<li class="ps-card ps-feature">'
                  . ( $padSecIcon  !== '' ? "<span class=\"ps-feature-icon\">$padSecIcon</span>" : '' )
                  . ( $padSecTitle !== '' ? "<h$padSecLevel class=\"ps-item-title\">$padSecTitle</h$padSecLevel>" : '' )
                  . ( $padSecText  !== '' ? padSectionTextBlock ( $padSecText, 'ps-item-text' ) : '' )
                  . '</li>';

  }

  return padSection ( 'features', $padSecHead . "<ul class=\"ps-grid ps-cols-$padSecCols ps-features\">$padSecCards</ul>" );

?>
