<?php

  // An editable spreadsheet in a {live} region (spreadsheet/live) is a form the live script
  // posts (pad-submit), which a change of a cell submits: the region's name its first field,
  // then every cell's text once - no pad-value on the cells. The post carries every cell's
  // field, the changed one and an earlier change alike, and the answer is the region alone,
  // computed again. The fields of another sheet change nothing.

  $liveUrl  = $padHost . 'regression/pages/?spreadsheet/live&padInclude';
  $livePage = padCurl ( $liveUrl );

  $liveForm = preg_match ( '/<form [^>]*pad-submit="(sheet-shop)"[^>]*><input type="hidden" name="padLive" value="(shop)">/', $livePage ['data'], $liveMatch )
            ? "posts {$liveMatch [1]}, its first field padLive={$liveMatch [2]}" : 'none';

  $liveTotal = fn ( $html ) => preg_match ( '/data-formula="=SUM\(D2:D3\)" data-value="([^"]*)"/', $html, $match ) ? $match [1] : 'none';

  $liveChange = padCurl ( [ 'url' => $liveUrl, 'post' => [
    'padLive'            => 'shop',
    'padSheet[shop][B2]' => '2',
    'padSheet[shop][C2]' => '4',
    'padSheet[shop][B3]' => '4',
    'padSheet[shop][C3]' => '2.25',
    'padEvent'           => 'sheet-shop',
    'padValue'           => ''
  ] ] );

  $liveOther = padCurl ( [ 'url' => $liveUrl, 'post' => [
    'padLive'             => 'shop',
    'padSheet[other][B2]' => '100',
    'padEvent'            => 'sheet-other',
    'padValue'            => ''
  ] ] );

  $liveResult = 'the page: total ' . $liveTotal ( $livePage ['data'] ) . ', the form ' . $liveForm
              . ', ' . substr_count ( $livePage ['data'], ' pad-value=' ) . ' pad-values'
              . '; a change of C2 to 4 after B3 to 4: ' . $liveChange ['result']
              . ', the region alone ' . ( str_starts_with ( ltrim ( $liveChange ['data'] ), '<div data-pad-live' ) ? 'no' : 'yes' )
              . ', total ' . $liveTotal ( $liveChange ['data'] )
              . '; the fields of another sheet: total ' . $liveTotal ( $liveOther ['data'] );

?>
