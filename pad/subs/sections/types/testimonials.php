<?php

  // {section:testimonials title='heading'} {section:item 'Ada Lovelace', role='Analyst'}The
  // quote{/section:item} ... {/section:testimonials} - what customers say, a card per quote:
  // the words, and who said them with their role and a picture - their initials in a circle
  // when there is none. The quotes are the rows of data= - their fields name, role, text (the
  // quote), image and link (where the name leads) - or {section:item} pairs, the item's first
  // value the name and its content the quote. columns= as for features (3).
  //
  // Parts: eyebrow, title, text. Options: title (or the first value), eyebrow, text, columns,
  // data, level, tone, align, id.

  padSectionParts ( [ 'eyebrow', 'title', 'text' ] );

  $padSecHead  = padSectionHead ();
  $padSecItems = padSectionItems ( 'name', [ 'name', 'role', 'text', 'image', 'link' ], 'text', 'quotes' );
  $padSecCols  = padSectionInt ( 'columns', max ( 1, min ( 3, count ( $padSecItems ) ) ), 1, 6 );
  $padSecCards = '';

  foreach ( $padSecItems as $padSecItem ) {

    $padSecName  = padSectionField ( $padSecItem, 'name' );
    $padSecRole  = padSectionField ( $padSecItem, 'role' );
    $padSecText  = padSectionField ( $padSecItem, 'text' );
    $padSecImage = padSectionLink ( padSectionRaw ( $padSecItem, 'image' ) );
    $padSecLink  = padSectionLink ( padSectionRaw ( $padSecItem, 'link' ) );

    // The picture is decoration beside the name it shows: its alt is empty, the initials are
    // hidden from a screen reader, which reads the name.

    $padSecFace  = ( $padSecImage !== '' )
                 ? "<img class=\"ps-face\" src=\"$padSecImage\" alt=\"\" width=\"44\" height=\"44\" loading=\"lazy\">"
                 : '<span class="ps-face ps-initials" aria-hidden="true">' . padSectionEscape ( padSectionInitials ( padSectionRaw ( $padSecItem, 'name' ) ) ) . '</span>';

    if ( $padSecLink !== '' and $padSecName !== '' )
      $padSecName = "<a href=\"$padSecLink\">$padSecName</a>";

    $padSecCards .= '<li class="ps-card ps-quote"><figure>'
                  . '<blockquote>' . ( padSectionBlock ( $padSecText ) ? $padSecText : "<p>$padSecText</p>" ) . '</blockquote>'
                  . "<figcaption>$padSecFace<span class=\"ps-person\"><b>$padSecName</b>"
                  . ( $padSecRole !== '' ? "<span>$padSecRole</span>" : '' ) . '</span></figcaption>'
                  . '</figure></li>';

  }

  return padSection ( 'testimonials', $padSecHead . "<ul class=\"ps-grid ps-cols-$padSecCols ps-quotes\">$padSecCards</ul>" );

?>
