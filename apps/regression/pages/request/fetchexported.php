<?php

  // The fixture for request/an_exported_page_fetches_this_site_without_the_developers_view:
  // the page fetches request/exported - whose own PHP switches the toolbar, live reload and
  // the output check on, and which shows a value with {debug} - and names which of the four
  // came back with it.

  $feMarks  = [ 'toolbar' => 'id="padToolbar"', 'reload' => 'id="padReload"', 'check' => 'class="pad-check"', 'debug' => 'class="pad-debug"' ];
  $feAnswer = (string) ( padCurl ( $padHost . 'regression/pages/?request/exported' ) ['data'] ?? '' );
  $feFound  = [];

  foreach ( $feMarks as $feName => $feMark )
    if ( str_contains ( $feAnswer, $feMark ) )
      $feFound [] = $feName;

  $fetchedView = ( $feFound ? implode ( ', ', $feFound ) : 'none' ) . ( str_contains ( $feAnswer, 'an exported page' ) ? '' : ' - the page is missing' );

?>
