<?php

  // {jsonview $response} - a value as a tree of JSON to fold open and shut, each object and
  // list a <details>: lib/jsonview.php. The value is the first item - a $field, a list of
  // the page or a row included, which is why the tag reads its items raw, as {debug} does
  // (lib/attrs.php) - or data= names it: a {data} store, a page array or a _data file. A
  // text that is JSON is decoded; as a pair the content is the JSON, taken as it stands,
  // so its braces need no {ignore}.
  //
  //   {jsonview $response, open=1, title='GET /orders/42'}
  //   {jsonview data='settings'}
  //   {jsonview} {"id": 42, "paid": true, "lines": [ ... ]} {/jsonview}
  //
  // open= is the number of levels shown unfolded, 2 when not given - 0 folds everything;
  // title= labels the outermost level.

  $padJsonviewOpen   = 2;
  $padJsonviewTitle  = '';
  $padJsonviewItems  = 0;
  $padJsonviewData   = NULL;
  $padJsonviewHas    = FALSE;
  $padJsonviewNamed  = '';
  $padJsonviewSource = $padContent;
  $padContent        = '';

  foreach ( padAttrsItems () as list ( $padJsonviewName, $padJsonviewExpr ) )
    if ( $padJsonviewName === '' and ! $padJsonviewItems++ ) {
      $padJsonviewData = padJsonviewValue ( $padJsonviewExpr );
      $padJsonviewHas  = TRUE;
    }
    elseif ( $padJsonviewName === 'open' )
      $padJsonviewOpen = padEval ( $padJsonviewExpr );
    elseif ( $padJsonviewName === 'title' )
      $padJsonviewTitle = (string) padEval ( $padJsonviewExpr );
    elseif ( $padJsonviewName === 'data' ) {
      $padJsonviewNamed = (string) padEval ( $padJsonviewExpr );
      $padJsonviewData  = padJsonviewNamed ( $padJsonviewNamed );
      $padJsonviewHas   = TRUE;
    }
    elseif ( $padCheckSyntax )
      padError ( $padJsonviewName === ''
        ? "the jsonview shows one value, not '" . padMakeSafe ( $padJsonviewExpr, 40 ) . "' as well"
        : "the jsonview has no option '" . padMakeSafe ( $padJsonviewName, 30 ) . "' - open, title or data" );

  if ( ! is_int ( $padJsonviewOpen ) and ! ctype_digit ( (string) $padJsonviewOpen ) ) {
    if ( $padCheckSyntax )
      padError ( "the open of the jsonview is a number of levels, not '" . padMakeSafe ( $padJsonviewOpen, 20 ) . "'" );
    $padJsonviewOpen = 2;
  }

  // A data= name that names nothing, a pair whose content is no JSON, and no value at all.

  if ( $padJsonviewNamed !== '' and $padJsonviewData === NULL ) {
    if ( $padCheckSyntax )
      padError ( "there is no data named '" . padMakeSafe ( $padJsonviewNamed, 40 ) . "' for the jsonview" );
    return '';
  }

  if ( ! $padJsonviewHas ) {

    if ( trim ( (string) $padJsonviewSource ) === '' ) {
      if ( $padCheckSyntax )
        padError ( "the jsonview has nothing to show - {jsonview \$value}, data='name' or the JSON between {jsonview} and {/jsonview}" );
      return '';
    }

    $padJsonviewData = padJsonviewText ( $padJsonviewSource );

    if ( $padJsonviewData === NULL and trim ( $padJsonviewSource ) !== 'null' ) {
      if ( $padCheckSyntax )
        padError ( 'the content of the jsonview is no JSON: ' . padJsonviewError ( $padJsonviewSource ) );
      return '';
    }

  }

  // A text that reads as a JSON object or list is shown as what it holds.

  if ( is_string ( $padJsonviewData ) and preg_match ( '/^\s*[\[{]/', $padJsonviewData )
       and ( $padJsonviewDecoded = padJsonviewText ( padUnprotect ( $padJsonviewData ) ) ) !== NULL )
    $padJsonviewData = $padJsonviewDecoded;

  return padJsonviewHtml ( $padJsonviewData, (int) $padJsonviewOpen, $padJsonviewTitle );

?>
