<?php

  // An editable spreadsheet in a {live} region (spreadsheet/live): a change of a cell posts the
  // region with that cell and, as its value, the texts of the other cells - what the live
  // script sends - and the answer is the region alone, computed again: the change and an
  // earlier change carried in the value both count. An event of another sheet changes
  // nothing.

  $liveUrl  = $padHost . 'regression/pages/?spreadsheet/live&padInclude';
  $livePage = padCurl ( $liveUrl );
  $liveSent = preg_match ( '/name="padSheet\[shop\]\[B2\]"[^>]* pad-value="([^"]*)"/', $livePage ['data'], $liveMatch ) ? html_entity_decode ( $liveMatch [1] ) : '';

  $liveTotal = fn ( $html ) => preg_match ( '/data-formula="=SUM\(D2:D3\)" data-value="([^"]*)"/', $html, $match ) ? $match [1] : 'none';

  $liveChange = padCurl ( [ 'url' => $liveUrl, 'post' => [
    'padLive'            => 'shop',
    'padEvent'           => 'sheet-shop',
    'padValue'           => 'B2=2&C2=3.50&B3=4&C3=2.25',
    'padSheet[shop][C2]' => '4'
  ] ] );

  $liveOther = padCurl ( [ 'url' => $liveUrl, 'post' => [
    'padLive'  => 'shop',
    'padEvent' => 'sheet-other',
    'padValue' => 'B2=100'
  ] ] );

  $liveResult = 'the page: total ' . $liveTotal ( $livePage ['data'] ) . ', a change sends ' . $liveSent
              . '; a change of C2 to 4 after B3 to 4: ' . $liveChange ['result']
              . ', the region alone ' . ( str_starts_with ( ltrim ( $liveChange ['data'] ), '<div data-pad-live' ) ? 'no' : 'yes' )
              . ', total ' . $liveTotal ( $liveChange ['data'] )
              . '; an event of another sheet: total ' . $liveTotal ( $liveOther ['data'] );

?>
