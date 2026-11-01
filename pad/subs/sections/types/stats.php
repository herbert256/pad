<?php

  // {section:stats title='heading'} {section:item '99.9%', label='Uptime'/} ...
  // {/section:stats} - the numbers that tell the story, side by side: each a large value with
  // its label under it and a line more when it has one. The numbers are the rows of data= -
  // their fields value, label and text - or {section:item} pairs or single tags, the item's
  // first value its value and its content its text. A screen reader reads each as a term and
  // its definition - the label, then the value. columns= as for features (the count, at most
  // 4).
  //
  // Parts: eyebrow, title, text. Options: title (or the first value), eyebrow, text, columns,
  // data, level, tone, align, id.

  padSectionParts ( [ 'eyebrow', 'title', 'text' ] );

  $padSecHead  = padSectionHead ();
  $padSecItems = padSectionItems ( 'value', [ 'value', 'label', 'text' ], 'text', 'numbers' );
  $padSecCols  = padSectionInt ( 'columns', max ( 1, min ( 4, count ( $padSecItems ) ) ), 1, 6 );
  $padSecList  = '';

  foreach ( $padSecItems as $padSecItem ) {

    $padSecText = padSectionField ( $padSecItem, 'text' );

    $padSecList .= '<dl class="ps-stat">'
                 . '<dt>' . padSectionField ( $padSecItem, 'label' ) . '</dt>'
                 . '<dd class="ps-stat-value">' . padSectionField ( $padSecItem, 'value' ) . '</dd>'
                 . ( $padSecText !== '' ? "<dd class=\"ps-stat-text\">$padSecText</dd>" : '' )
                 . '</dl>';

  }

  return padSection ( 'stats', $padSecHead . "<div class=\"ps-grid ps-cols-$padSecCols ps-stats\">$padSecList</div>" );

?>
