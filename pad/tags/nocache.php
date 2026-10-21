<?php

  // {nocache}...{/nocache}: renders its content on every request, also when the page or the
  // {cache} section round it comes from a cache - lib/nocache.php. Where nothing caches,
  // the content renders as any content does.
  //
  // On a hit the stored text names each part - {nocache 'key-n'} - and the part gets the
  // fields of the rows it stood in when it was stored back as its row.

  if ( ! $padPair [$pad] and $padCheckSyntax )
    padError ( "the pair {nocache} never closes" );

  $padNocacheRow = NULL;

  // The variables the cached text had made before the part are set again, as they stood on
  // the miss (padNocacheVars) - the tags that made them are stored text on a hit. Only one
  // that is not there, or that an earlier part set so ($padNocacheSet): one the request has
  // otherwise - its page, or a part that set it itself for this visitor - is the request's.

  if ( (string) $padParm !== '' ) {
    if ( isset ( $padNocacheRows [$padParm] ) ) {
      $padNocacheRow = $padNocacheRows [$padParm] ['row'];
      foreach ( $padNocacheRows [$padParm] ['vars'] as $padNocacheVar => $padNocacheValue )
        if ( ! padEngineName ( (string) $padNocacheVar )
             and ( ! isset ( $GLOBALS [$padNocacheVar] ) or isset ( $GLOBALS ['padNocacheSet'] [$padNocacheVar] ) ) ) {
          $GLOBALS [$padNocacheVar]                  = $padNocacheValue;
          $GLOBALS ['padNocacheSet'] [$padNocacheVar] = TRUE;
        }
    } elseif ( $padCheckSyntax )
      padError ( "the {nocache} takes no parameter" );
  }

  if ( padNocacheWanted () ) {

    [ $padNocacheOpen, $padNocacheClose ] = padNocacheOpen ( $padSource [$pad] );

    $padContent = $padNocacheOpen . $padContent . $padNocacheClose;

  }

  return $padNocacheRow ? [ $padNocacheRow ] : TRUE;

?>
