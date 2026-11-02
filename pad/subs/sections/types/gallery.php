<?php

  // {section:gallery title='Made with PAD'} {section:item 'dawn.jpg', alt='The harbour at
  // dawn'}Dawn{/section:item} ... {/section:gallery} - pictures in a grid, each with its caption
  // under it, cropped to one shape so the rows line up. The pictures are the rows of data= -
  // their fields image, caption, alt and link (where the picture leads) - or {section:item}
  // pairs or single tags, the item's first value the picture and its content the caption; a
  // content that is a picture itself - an <img>, an <svg>, a {widget:placeholder} - is the picture,
  // and caption= its caption. A picture without alt= takes its caption as its alt text.
  // columns= as for features (3).
  //
  // Parts: eyebrow, title, text. Options: title (or the first value), eyebrow, text, columns,
  // data, level, tone, align, id.

  padSectionParts ( [ 'eyebrow', 'title', 'text' ] );

  $padSecHead  = padSectionHead ();
  $padSecItems = padSectionItems ( 'image', [ 'image', 'caption', 'alt', 'link' ], 'caption', 'pictures' );
  $padSecCols  = padSectionInt ( 'columns', 3, 1, 6 );
  $padSecList  = '';

  foreach ( $padSecItems as $padSecItem ) {

    // The content of an item that is a picture is the picture, its caption caption=.

    $padSecMade = NULL;

    if ( isset ( $padSecItem ['html'] ['caption'] ) and preg_match ( '/\A\s*(?:<style\b[^>]*>.*?<\/style>\s*)*<(img|svg|picture|video)\b/is', padUnprotect ( $padSecItem ['html'] ['caption'] ) ) ) {
      $padSecMade = padSectionHold ( $padSecItem ['html'] ['caption'] );
      unset ( $padSecItem ['html'] ['caption'] );
    }

    $padSecImage   = padSectionLink ( padSectionRaw ( $padSecItem, 'image' ) );
    $padSecCaption = padSectionField ( $padSecItem, 'caption' );
    $padSecLink    = padSectionLink ( padSectionRaw ( $padSecItem, 'link' ) );
    $padSecAlt     = padSectionHas ( $padSecItem, 'alt' ) ? padSectionField ( $padSecItem, 'alt' ) : padSectionPlain ( $padSecItem, 'caption' );

    if ( $padSecMade === NULL and $padSecImage === '' )
      continue;

    $padSecPicture = $padSecMade ?? "<img src=\"$padSecImage\" alt=\"$padSecAlt\" loading=\"lazy\">";

    if ( $padSecLink !== '' )
      $padSecPicture = "<a href=\"$padSecLink\">$padSecPicture</a>";

    $padSecList .= "<li><figure class=\"ps-picture\">$padSecPicture"
                 . ( $padSecCaption !== '' ? "<figcaption>$padSecCaption</figcaption>" : '' )
                 . '</figure></li>';

  }

  return padSection ( 'gallery', $padSecHead . "<ul class=\"ps-grid ps-cols-$padSecCols ps-gallery\">$padSecList</ul>" );

?>
