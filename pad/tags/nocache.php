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

  if ( (string) $padParm !== '' ) {
    if ( isset ( $padNocacheRows [$padParm] ) )
      $padNocacheRow = $padNocacheRows [$padParm];
    elseif ( $padCheckSyntax )
      padError ( "the {nocache} takes no parameter" );
  }

  if ( padNocacheWanted () ) {

    [ $padNocacheOpen, $padNocacheClose ] = padNocacheOpen ( $padSource [$pad] );

    $padContent = $padNocacheOpen . $padContent . $padNocacheClose;

  }

  return $padNocacheRow ? [ $padNocacheRow ] : TRUE;

?>
