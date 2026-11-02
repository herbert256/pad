<?php

  // A field is read as HTML when it is HTML - an end tag, a comment, a void element or a
  // start tag with a quoted attribute - where any "<word" followed by a ">" further on made
  // it HTML and what stood between was dropped: "a<b and b>c" was indexed as "ac" and
  // excerpted so, "<i think>" was not there at all. An entity is the character it stands
  // for, in plain text too: &amp; is no word amp.

  $notes = [
    [ 'f' => 'Valid when a<b and b>c, else none' ],
    [ 'f' => 'fits screens <i think> up to 15 inch' ],
    [ 'f' => 'Tom &amp; Jerry' ],
    [ 'f' => '<p>Hello <b>bold</b> world</p><p>again</p>' ],
    [ 'f' => 'line one<br>line two' ]
  ];

  $hits = [];

  foreach ( [ 'valid', 'think', 'amp', 'jerry', 'bold', 'two' ] as $q ) {
    $one = [];
    foreach ( padSearch ( $notes, $q, [ 'name' => 'notes' ] ) as $row )
      $one [] = preg_replace ( '#<style[^>]*>.*?</style>#s', '', $row ['excerpt'] );
    $hits [] = "$q: " . ( $one ? implode ( ' ', $one ) : 'none' );
  }

  $hits = implode ( ' | ', $hits );

?>
