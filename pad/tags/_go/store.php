<?php

  // Shared body of {data}, {content} and {bool}: puts the tag's content in a named store,
  // so that later on {thatName} addresses it (lib/type.php resolves names against the
  // stores).
  //
  // Parameters written on the closing tag only exist once the content has been walked, so
  // in that case the first visit just asks for a second one at level end ($padWalk =
  // 'end'). The name is the first parameter; the source is the tag's content, or the
  // second parameter when there is no content. {content} keeps the unprocessed source
  // when it is stored at the open tag and the rendered text otherwise, {data} runs the
  // source through padData() unless the level already carries data, {bool} reduces it to
  // TRUE/FALSE with padMakeFlag().
  //
  // The result lands in $padDataStore, $padContentStore or $padBoolStore; the content is
  // then cleared and NULL returned, so storing prints nothing.

  if ( $padWalk [$pad] == 'start' and $padPrmType [$pad] == 'close' ) {
    $padWalk [$pad] = 'end';
    return TRUE;
  }

  // A store with no name at all lands under the tag's own word, unreachable on purpose
  // by nobody. Strict mode asks for the name.

  if ( $padCheckSyntax and trim ( $padOpt [$pad] [1] ?? '' ) == '' and ! isset ( $padOpt [$pad] [2] ) )
    padError ( "the {" . $padTag [$pad] . "} has no name" );

  $padStoreName = 'pad' . ucwords($padTag[$pad]) . 'Store';

  if ( isset ( $padParm ) or isset ( $padOpt [$pad] [2] ) )
    $padName [$pad] = $padParm;

  // No content means an empty string - a content of 0 is a value - and then the source is
  // the second parameter when there is one, else nothing.
  //
  // That parameter is a value unless it is written out - a quoted string, {content 'c',
  // 'Hi {$name}'} - and a value is text: {content 'c', $v} ran what $v held as PAD when
  // {c} rendered, where {content 'c'}{$v}{/content} keeps it text; {data 'd', $v} ran the
  // elements of a value that reads as a PAD list, as data=$v does not; {bool} below.

  $padStoreValue = FALSE;

  if ( (string) $padContent === '' ) {

    $padStoreSource = $padOpt [$pad] [2] ?? '';

    if ( $padProtectValues )
      foreach ( $padParms [$pad] as $padStoreParm )
        if ( $padStoreParm ['padPrmKind'] == 'parm' and $padStoreParm ['padPrmName'] === 2 )
          $padStoreValue = ! is_string ( padMetaLiteral ( $padStoreParm ['padPrmOrg'] ) );

    if ( $padTag [$pad] == 'content' and $padStoreValue )
      $padStoreSource = padProtect ( $padStoreSource );

  }
  elseif ($padTag [$pad] == 'content' and $padWalk [$pad] == 'start')
    $padStoreSource = $padBase [$pad];
  else
    $padStoreSource = $padContent;

  if ( $padTag [$pad] == 'content') {

    if ( $padWalk [$pad] == 'start' and (string) $padContent !== '' )
      $padStoreData = $padSource [$pad];
    else
      $padStoreData = padMakeContent ($padStoreSource);

  } elseif ( $padTag [$pad] == 'data' ) {

    // Judged as padData will read it (padDataText): trimmed and a leading UTF-8 byte order
    // mark taken off, which padData strips before it sniffs the type - a list behind a BOM,
    // or behind white space and one, passed this check and then ran.

    $padStoreList = is_string ( $padStoreSource ) ? padDataText ( $padStoreSource ) : $padStoreSource;

    if ( ! padIsDefaultData ( $padData [$pad] ) )
      $padStoreData = $padData [$pad];
    elseif ( $padStoreValue and is_string ( $padStoreList ) and padContentType ( $padStoreList ) == 'list' ) {
      padError ( "the data value reads as a PAD list, whose elements would run as expressions" );
      $padStoreData = [];
    } else
      $padStoreData = padData ($padStoreSource, padTagParm('type'), $padName [$pad]);

  } elseif ( $padTag [$pad] == 'bool' ) {

    // A bool from a value - {bool 'b', $v} - is that value's own truth, not the value run
    // as PAD: padMakeFlag hands a string to padEval, and the parameter was evaluated to the
    // value before it got here, so $v holding a php: call ran it. Blank and '0' are FALSE,
    // any other value TRUE, PHP's own (bool) rule. A written-out literal is evaluated as
    // before ($padStoreValue above). The pair form's content keeps padMakeFlag: padEval
    // leaves a {$field} source untouched, so no value runs there and its flag stays.

    if ( $padStoreValue )
      $padStoreData = ! in_array ( trim ( (string) $padStoreSource ), [ '', '0' ], TRUE );
    else
      $padStoreData = padMakeFlag ( $padStoreSource );

  }

  // A store named like a tag is never reached by its bare name - resolution finds the
  // tag first. The sequence stores refuse such names already; strict mode holds these
  // three stores to the same rule.

  if ( $padCheckSyntax ) {

    if ( file_exists ( PAD . "tags/" . $padName [$pad] . ".php" ) )
      padError ( "the store name '" . $padName [$pad] . "' is already a built-in tag - {" . $padName [$pad] . "} will never reach the store" );

    if ( padAppTagCheck ( $padName [$pad] ) )
      padError ( "the store name '" . $padName [$pad] . "' is already an application tag - {" . $padName [$pad] . "} will never reach the store" );

  }

  $GLOBALS [$padStoreName] [$padName [$pad]] = $padStoreData;

  if ( $padInfo )
    include PAD . 'events/store.php';

  $padContent = '';

  return NULL;

?>
