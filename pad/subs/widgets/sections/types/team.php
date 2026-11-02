<?php

  // {section:team title='The people'} {section:item 'Grace Hopper', role='Compilers',
  // image='grace.jpg'/} ... {/section:team} - the people behind it, a card each: a picture -
  // their initials in a circle when there is none - the name, the role and a line about them.
  // The people are the rows of data= - their fields name, role, text, image and link (where
  // the name leads) - or {section:item} pairs or single tags, the item's first value the name
  // and its content the line. columns= as for features (4).
  //
  // Parts: eyebrow, title, text. Options: title (or the first value), eyebrow, text, columns,
  // data, level, tone, align, id.

  padSectionParts ( [ 'eyebrow', 'title', 'text' ] );

  $padSecHead  = padSectionHead ();
  $padSecItems = padSectionItems ( 'name', [ 'name', 'role', 'text', 'image', 'link' ], 'text', 'people' );
  $padSecCols  = padSectionInt ( 'columns', 4, 1, 6 );
  $padSecLevel = min ( 6, padSectionLevel () + 1 );
  $padSecCards = '';

  foreach ( $padSecItems as $padSecItem ) {

    $padSecName  = padSectionField ( $padSecItem, 'name' );
    $padSecRole  = padSectionField ( $padSecItem, 'role' );
    $padSecText  = padSectionField ( $padSecItem, 'text' );
    $padSecImage = padSectionLink ( padSectionRaw ( $padSecItem, 'image' ) );
    $padSecLink  = padSectionLink ( padSectionRaw ( $padSecItem, 'link' ) );

    $padSecFace  = ( $padSecImage !== '' )
                 ? "<img class=\"ps-face\" src=\"$padSecImage\" alt=\"\" width=\"96\" height=\"96\" loading=\"lazy\">"
                 : '<span class="ps-face ps-initials" aria-hidden="true">' . padSectionEscape ( padSectionInitials ( padSectionRaw ( $padSecItem, 'name' ) ) ) . '</span>';

    if ( $padSecLink !== '' and $padSecName !== '' )
      $padSecName = "<a href=\"$padSecLink\">$padSecName</a>";

    $padSecCards .= '<li class="ps-member">' . $padSecFace
                  . "<h$padSecLevel class=\"ps-item-title\">$padSecName</h$padSecLevel>"
                  . ( $padSecRole !== '' ? "<p class=\"ps-member-role\">$padSecRole</p>" : '' )
                  . ( $padSecText !== '' ? padSectionTextBlock ( $padSecText, 'ps-item-text' ) : '' )
                  . '</li>';

  }

  return padSection ( 'team', $padSecHead . "<ul class=\"ps-grid ps-cols-$padSecCols ps-team\">$padSecCards</ul>" );

?>
