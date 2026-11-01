<?php

  // An editable spreadsheet in a {form} (spreadsheet/form): its cells post back with the form
  // and the page computes the sheet again with them - a number, a formula typed into a cell,
  // a formula that cannot be read, which shows #ERROR! in its cell and is no error of the
  // page: a visitor typed it. A cell that is not to be edited (B4, the payment) and a text
  // of more than 1000 characters are left as they were.

  $sheetUrl = $padHost . 'regression/pages/?spreadsheet/form&padInclude';

  $sheetCell = function ( $html, $cell ) {
    if ( preg_match ( '/name="padSheet\[loan\]\[' . $cell . '\]" value="([^"]*)"/', $html, $match ) )
      return 'field ' . html_entity_decode ( $match [1] );
    return 'none';
  };

  $sheetPayment = fn ( $html ) => preg_match ( '/data-formula="=ROUND\(PMT\(B2\/12,B3\*12,-B1\),2\)"[^>]*>([^<]*)</', $html, $match ) ? $match [1] : 'none';

  $sheetGet  = padCurl ( $sheetUrl );
  $sheetPost = padCurl ( [ 'url' => $sheetUrl, 'post' => [
    'padForm'                => 'loan',
    'padSheet[loan][B1]'     => '20000',
    'padSheet[loan][B3]'     => '=2+3',
    'padSheet[loan][B4]'     => '0',
    'padSheet[loan][B2]'     => str_repeat ( '9', 1001 )
  ] ] );
  $sheetBad  = padCurl ( [ 'url' => $sheetUrl, 'post' => [ 'padForm' => 'loan', 'padSheet[loan][B2]' => '=SUM(' ] ] );

  $sheetResult = 'asked: ' . $sheetGet ['result'] . ', payment ' . $sheetPayment ( $sheetGet ['data'] )
               . '; posted: ' . $sheetPost ['result'] . ', B1 ' . $sheetCell ( $sheetPost ['data'], 'B1' ) . ', B2 ' . $sheetCell ( $sheetPost ['data'], 'B2' )
               . ', B3 ' . $sheetCell ( $sheetPost ['data'], 'B3' ) . ', B4 ' . $sheetCell ( $sheetPost ['data'], 'B4' )
               . ', payment ' . $sheetPayment ( $sheetPost ['data'] )
               . '; unreadable: ' . $sheetBad ['result'] . ', B2 ' . $sheetCell ( $sheetBad ['data'], 'B2' )
               . ', payment ' . $sheetPayment ( $sheetBad ['data'] );

?>
