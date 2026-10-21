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
    } elseif ( is_string ( $padStoreList ) and padContentType ( $padStoreList ) == 'list' and padSpliceInCode ( $padStoreList ) ) {

      // The content walked before it is stored - the name on the closing tag, {data}( {$x} )
      // {/data 'e'} - holds its fields' values, and a value standing bare among the elements
      // ran as one of their expressions: a request value php:strlen(12345) stored 5. Written
      // in quotes, ( '{$x}' ), it is an element's text (lib/level.php padSpliceInCode).

      padError ( "a value spliced into a {data} list would run as an expression: put it in quotes, ( '{\$x}' ), or hand it over as data=\$x" );
      $padStoreData = [];

    } elseif ( $padProtectValues and padDataFetches ( $padStoreSource, padTagParm ( 'type' ) )
             and padDataValueAddress ( $padStoreSource, $padStoreValue,
                   ( $padWalk [$pad] == 'end' and (string) $padContent !== '' ) ? $padBase [$pad] : NULL ) ) {

      // An address is fetched only when the author wrote it, as data= has it (options/
      // data.php): {data 'd', $v}, and a value walked into a {data} named at its close that
      // starts its content - the scheme and host the author did not write - had the server
      // fetch whatever the request named.

      padError ( "the data value reads as an address to fetch - write the address in the template, data='https://...', or fetch it in the page's PHP with padPrefetch or padCurl" );
      $padStoreData = [];

    } else {

      $padStoreData = padData ($padStoreSource, padTagParm('type'), $padName [$pad]);

      // Walked content holds its values with their syntax characters as stand-ins (lib/
      // encoding.php), which keep a spliced comma from splitting a CSV cell and a quote from
      // closing a JSON string while it is parsed. Parsed, the fields are values again: they
      // kept the stand-ins - "Smith, John" stored with the comma's - and {if}, where=, sort,
      // lookup and php: calls on them compared another string. Printed, a field is a value
      // and travels protected again.

      if ( $padWalk [$pad] == 'end' and (string) $padContent !== '' and is_array ( $padStoreData ) )
        array_walk_recursive ( $padStoreData, function ( &$padStoreOne ) {
          if ( is_string ( $padStoreOne ) )
            $padStoreOne = padUnprotect ( $padStoreOne );
        } );

    }

  } elseif ( $padTag [$pad] == 'bool' ) {

    // A bool from a value - {bool 'b', $v} - is that value's own truth, not the value run
    // as PAD: padMakeFlag hands a string to padEval, and the parameter was evaluated to the
    // value before it got here, so $v holding a php: call ran it. Blank and '0' are FALSE,
    // any other value TRUE, PHP's own (bool) rule. A written-out literal is evaluated as
    // before ($padStoreValue above). The pair named on its opening tag keeps padMakeFlag:
    // padEval leaves a {$field} source untouched, so no value runs there and its flag stays.

    if ( $padStoreValue )
      $padStoreData = ! in_array ( trim ( (string) $padStoreSource ), [ '', '0' ], TRUE );

    elseif ( $padWalk [$pad] == 'end' and (string) $padContent !== '' ) {

      // Named on its closing tag, {bool}{$a}{/bool 'b'}, the content is walked first and
      // holds its fields' values: handed to padEval they ran as the expression they read
      // like - a request value php:strlen(12345) eq 6 stored FALSE, connection_status called
      // that PHP function. What the author wrote is the source with its tags taken out:
      // nothing there, the content is values alone and the flag their text's own truth, as
      // {bool 'b', $a} takes it ('Hello world' TRUE, blank and '0' FALSE). An expression of
      // the author's own is evaluated with the values in it - a number, {$n} gt 2, a word,
      // {$s} eq 'paid', or a value in quotes, '{$u}' eq 'Ann' - and refused, as the {data}
      // list is, when a value stands in its code: one of more than a word, which
      // padSpliceInCode knows, or a single word the author did not write that the evaluator
      // takes for a reference - a function, a tag, a constant: phpversion and
      // connection_status ran with no arguments. TRUE, FALSE and NULL are values.

      $padStoreStrings = '/\'(?:[^\'\\\\]|\\\\.)*\'|"(?:[^"\\\\]|\\\\.)*"/s';
      $padStoreOwn     = $padBase [$pad];

      do $padStoreOwn = preg_replace ( '/\{[^{}]*\}/', ' ', $padStoreOwn, -1, $padStoreHits );
      while ( $padStoreHits );

      preg_match_all ( '/[a-z_]\w*/i', preg_replace ( $padStoreStrings, '', $padStoreOwn ), $padStoreOwnNames );
      preg_match_all ( '/[a-z_]\w*/i', preg_replace ( $padStoreStrings, '', (string) $padStoreSource ), $padStoreNames );

      $padStoreCalls = array_filter ( array_diff ( $padStoreNames [0], $padStoreOwnNames [0] ),
        fn ( $name ) => ! in_array ( strtolower ( $name ), [ 'true', 'false', 'null' ] )
                        and ( padTypeFunction ( $name ) or defined ( $name ) ) );

      if ( trim ( $padStoreOwn ) === '' )
        $padStoreData = ! in_array ( trim ( (string) $padStoreSource ), [ '', '0' ], TRUE );
      elseif ( $padStoreCalls or padSpliceInCode ( $padStoreSource ) ) {
        padError ( "a value spliced into a {bool} expression would run as part of it: put it in quotes, '{\$x}' eq 'Ann', or take its own truth with {bool 'name', \$x}" );
        $padStoreData = FALSE;
      } else
        $padStoreData = padMakeFlag ( $padStoreSource );

    } else
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
