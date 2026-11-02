<?php

  // {section:hero title='heading', text='line', button='Start', link='?start'} ... {/section:hero}
  // - the opening block of a page: a large heading, a line or two under it, the buttons that
  // lead on, and a picture beside them when it has one. The content between the tags is the
  // text under the heading; eyebrow= (or a slot) is the small line above it, note= the small
  // print under the buttons. With image= (alt= its text) the words and the picture stand side
  // by side, the words aligned left; without, everything is centred unless align='left'.
  //
  // Parts: eyebrow, title, text, actions, image, note. Options: title (or the first value),
  // eyebrow, text, button and link, second and secondLink, image and alt, note, level (2),
  // tone (plain, soft, accent), align (center, left), id.

  padSectionParts ( [ 'eyebrow', 'title', 'text', 'actions', 'image', 'note' ] );

  $padSecImage = padSectionImage ( 'ps-hero-image' );

  if ( $padSecImage !== '' )
    $GLOBALS ['padSectionNow'] ['align'] = 'left';

  $padSecCopy = padSectionHead ( [ 'eyebrow', 'title', 'text' ] ) . padSectionActions ();

  if ( ( $padSecNote = padSectionPart ( 'note' ) ) !== '' )
    $padSecCopy .= "<p class=\"ps-note\">$padSecNote</p>";

  $padSecCopy = "<div class=\"ps-hero-copy\">$padSecCopy</div>";

  return padSection ( 'hero', ( $padSecImage !== '' ) ? "<div class=\"ps-hero-split\">$padSecCopy$padSecImage</div>" : $padSecCopy,
                      ( $padSecImage !== '' ) ? 'ps-hero-pictured' : '' );

?>
