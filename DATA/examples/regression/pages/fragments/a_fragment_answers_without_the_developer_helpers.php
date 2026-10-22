<?php

  // A response fragment is the fragment alone. A page with the toolbar, live reload and the
  // output check on carries the bar, the script and the check's panel as a whole page; asked
  // for its fragment - by padFragment, or by htmx naming it in HX-Target - the answer is the
  // fragment and nothing more: htmx swapped all of it in, every swap another fixed bar and
  // another poller of the stamp.

  $fhUrl = $padHost . 'regression/pages/?fragments/helpers';

  foreach ( [ 'whole page'  => [ '',                  [] ],
              'padFragment' => [ '&padFragment=list', [] ],
              'HX-Target'   => [ '',                  [ 'HX-Request' => 'true', 'HX-Target' => 'list' ] ] ]
            as $fhLabel => [ $fhQuery, $fhHeaders ] ) {

    $fhAnswer = (string) ( padCurl ( [ 'url' => $fhUrl . $fhQuery, 'headers' => $fhHeaders ] ) ['data'] ?? '' );

    $fhHelpers = [];

    foreach ( [ 'toolbar' => 'id="padToolbar"', 'reload' => 'id="padReload"', 'check' => 'class="pad-check"' ] as $fhName => $fhMark )
      if ( str_contains ( $fhAnswer, $fhMark ) )
        $fhHelpers [] = $fhName;

    echo "$fhLabel: " . ( $fhHelpers ? implode ( ', ', $fhHelpers ) : 'none' )
       . ( str_contains ( $fhAnswer, '<img src="a.png">' ) ? '' : ' - the fragment is missing' ) . "\n";

  }

?>
