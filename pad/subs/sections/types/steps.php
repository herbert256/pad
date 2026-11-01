<?php

  // {section:steps title='How it works'} {section:item 'Write a template'}It is
  // HTML.{/section:item} ... {/section:steps} - the way to a result in numbered steps, the
  // numbers drawn by the list itself (an <ol>, so a screen reader counts them too). The steps
  // are the rows of data= - their fields title and text - or {section:item} pairs, the item's
  // first value its title and its content its text. columns= as for features (the count, at
  // most 4).
  //
  // Parts: eyebrow, title, text. Options: title (or the first value), eyebrow, text, columns,
  // data, level, tone, align, id.

  padSectionParts ( [ 'eyebrow', 'title', 'text' ] );

  $padSecHead  = padSectionHead ();
  $padSecItems = padSectionItems ( 'title', [ 'title', 'text' ], 'text', 'steps' );
  $padSecCols  = padSectionInt ( 'columns', max ( 1, min ( 4, count ( $padSecItems ) ) ), 1, 6 );
  $padSecLevel = min ( 6, padSectionLevel () + 1 );
  $padSecList  = '';

  foreach ( $padSecItems as $padSecItem ) {

    $padSecText = padSectionField ( $padSecItem, 'text' );

    $padSecList .= '<li class="ps-step">'
                 . "<h$padSecLevel class=\"ps-item-title\">" . padSectionField ( $padSecItem, 'title' ) . "</h$padSecLevel>"
                 . ( $padSecText !== '' ? padSectionTextBlock ( $padSecText, 'ps-item-text' ) : '' )
                 . '</li>';

  }

  return padSection ( 'steps', $padSecHead . "<ol class=\"ps-grid ps-cols-$padSecCols ps-steps\">$padSecList</ol>" );

?>
