<?php

  // {section:highlights title='Built for the web'} {section:item 'Templates drive',
  // image='drive.png', alt='A template beside its page', link='?docs'}The page decides what
  // runs.{/section:item} ... {/section:highlights} - the few things that matter most, a row
  // each: a picture on one side and the words on the other, the sides changing from row to
  // row, so the page reads as a zigzag; one above the other when the section is narrow, the
  // picture first. The highlights are the rows of data= - their fields title, eyebrow, text,
  // image, alt, link and button - or {section:item} pairs or single tags, the item's first
  // value the title and its content the text; a content that is a picture itself - an <img>,
  // an <svg>, a {placeholder} - is the picture, and text= its text, as in a gallery. link= is
  // where a highlight leads, its link button= ('Learn more'), named by the highlight's title
  // for a screen reader; without a link there is none. A picture without alt= decorates. The words come first in the page,
  // whichever side they are drawn on; flip starts the zigzag with the picture on the left.
  //
  // Parts: eyebrow, title, text. Options: title (or the first value), eyebrow, text, flip,
  // data, level, tone, align, id.

  padSectionParts ( [ 'eyebrow', 'title', 'text' ] );

  $padSecHead  = padSectionHead ();
  $padSecItems = padSectionItems ( 'title', [ 'title', 'eyebrow', 'text', 'image', 'alt', 'link', 'button' ], 'text', 'highlights' );
  $padSecFlip  = padSectionFlag ( 'flip' );
  $padSecLevel = min ( 6, padSectionLevel () + 1 );
  $padSecId    = padSectionId ();
  $padSecList  = '';

  foreach ( $padSecItems as $padSecN => $padSecItem ) {

    // The content of an item that is a picture is the picture, its text text=.

    $padSecMade = NULL;

    if ( isset ( $padSecItem ['html'] ['text'] ) and preg_match ( '/\A\s*(?:<style\b[^>]*>.*?<\/style>\s*)*<(img|svg|picture|video)\b/is', padUnprotect ( $padSecItem ['html'] ['text'] ) ) ) {
      $padSecMade = padSectionHold ( $padSecItem ['html'] ['text'] );
      unset ( $padSecItem ['html'] ['text'] );
    }

    $padSecOneId   = "$padSecId-highlight-" . ( $padSecN + 1 );
    $padSecTitle   = padSectionField ( $padSecItem, 'title' );
    $padSecEyebrow = padSectionField ( $padSecItem, 'eyebrow' );
    $padSecText    = padSectionField ( $padSecItem, 'text' );
    $padSecImage   = padSectionLink ( padSectionRaw ( $padSecItem, 'image' ) );
    $padSecLink    = padSectionLink ( padSectionRaw ( $padSecItem, 'link' ) );
    $padSecButton  = padSectionHas ( $padSecItem, 'button' ) ? padSectionField ( $padSecItem, 'button' ) : 'Learn more';

    if ( $padSecTitle === '' ) {
      if ( $GLOBALS ['padCheckSyntax'] )
        padError ( "a highlight of the " . padSectionWhat () . " has no title - the item's first value, or the field title of its row" );
      continue;
    }

    $padSecPicture = $padSecMade ?? ( $padSecImage !== '' ? "<img src=\"$padSecImage\" alt=\"" . padSectionField ( $padSecItem, 'alt' ) . '" loading="lazy">' : '' );

    $padSecList .= '<li class="ps-highlight' . ( $padSecPicture === '' ? ' ps-highlight-bare' : '' ) . '">'
                 . '<div class="ps-highlight-copy">'
                 . ( $padSecEyebrow !== '' ? "<p class=\"ps-eyebrow\">$padSecEyebrow</p>" : '' )
                 . "<h$padSecLevel class=\"ps-highlight-title\" id=\"$padSecOneId\">$padSecTitle</h$padSecLevel>"
                 . ( $padSecText !== '' ? padSectionTextBlock ( $padSecText, 'ps-item-text' ) : '' )
                 . ( $padSecLink !== '' ? "<a class=\"ps-highlight-link\" href=\"$padSecLink\" aria-describedby=\"$padSecOneId\">$padSecButton</a>" : '' )
                 . '</div>'
                 . ( $padSecPicture !== '' ? "<div class=\"ps-highlight-image\">$padSecPicture</div>" : '' )
                 . '</li>';

  }

  return padSection ( 'highlights', $padSecHead . '<ul class="ps-highlights' . ( $padSecFlip ? ' ps-highlights-flip' : '' ) . "\">$padSecList</ul>" );

?>
