<?php

  // {section:cta title='Ready to start?', button='Create an account', link='?signup'} ...
  // {/section:cta} - a call to action: a band in the accent colour with a heading, a line and
  // the buttons that act on it. The content between the tags is the line. tone= gives it
  // another background - plain or soft - than the accent it has unless given.
  //
  // Parts: eyebrow, title, text, actions, note. Options: title (or the first value), eyebrow,
  // text, button and link, second and secondLink, note, level, tone (accent), align, id.

  padSectionParts ( [ 'eyebrow', 'title', 'text', 'actions', 'note' ] );

  $GLOBALS ['padSectionNow'] ['tone'] = 'accent';

  $padSecHtml = padSectionHead () . padSectionActions ();

  if ( ( $padSecNote = padSectionPart ( 'note' ) ) !== '' )
    $padSecHtml .= "<p class=\"ps-note\">$padSecNote</p>";

  return padSection ( 'cta', "<div class=\"ps-cta\">$padSecHtml</div>" );

?>
