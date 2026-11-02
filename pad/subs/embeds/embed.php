<?php

  // {embed:auto 'https://youtu.be/aqz-KE-bpKQ', title='Big Buck Bunny'/} detects the
  // provider from the address. {embed:youtube 'aqz-KE-bpKQ', title='Video'/} selects
  // a provider explicitly and also accepts its bare ids. The caller sets $padEmbedName;
  // auto resolves to an empty name for provider discovery (padEmbedProviders).
  //
  // title= is required: the button's text, the frame's title, what a screen reader says.
  // poster= is a picture of www/<application>/, thumbnail the provider's own picture where
  // it has one at a known address, ratio= the proportion; a provider reads options of its own
  // - at=, zoom=, tab= ... - and only those. An address no provider knows is an error under
  // the strict check and answers nothing, so the @else@ shows.

  include_once PAD . 'subs/embeds/_lib/embed.php';

  if ( $padEmbedName == 'auto' )
    $padEmbedName = '';

  $padEmbedSource = trim ( padParmText ( $padParm ) );
  $padEmbedTitle  = trim ( padParmText ( padTagParm ( 'title' ) ) );

  if ( $padEmbedTitle === '' and $padCheckSyntax )
    padError ( "the embed needs a title= - the button's text and the frame's name for a screen reader" );

  if ( $padEmbedName === '' ) {

    $padEmbedFound = padEmbedFind ( $padEmbedSource );

    if ( $padEmbedFound === NULL ) {
      if ( $padCheckSyntax )
        padError ( "the embed knows no provider for '" . padMakeSafe ( $padEmbedSource, 80 ) . "' - " . implode ( ', ', padEmbedProviders () ) );
      return FALSE;
    }

    list ( $padEmbedName, $padEmbedParts ) = $padEmbedFound;

  } else {

    $padEmbedParts = padEmbedProvider ( $padEmbedName ) ['match'] ( $padEmbedSource, TRUE );

    if ( $padEmbedParts === NULL ) {
      if ( $padCheckSyntax )
        padError ( "the $padEmbedName embed cannot read '" . padMakeSafe ( $padEmbedSource, 80 ) . "' - it is no address of "
                   . padEmbedProvider ( $padEmbedName ) ['name'] . ' nor anything else it knows' );
      return FALSE;
    }

  }

  // The provider used goes on the xref record, which the reference application shows: the
  // pages that embed something of each provider.

  if ( $padInfoXref ?? FALSE )
    padInfoXref ( 'embed', $padEmbedName );

  $padEmbedOpts = [ 'poster'    => padTagParm ( 'poster', '' ),
                    'ratio'     => padTagParm ( 'ratio', '' ),
                    'thumbnail' => (bool) padTagParm ( 'thumbnail', FALSE ) ];

  foreach ( padEmbedProvider ( $padEmbedName ) ['options'] as $padEmbedOption )
    if ( ( $padEmbedValue = padTagParm ( $padEmbedOption, NULL ) ) !== NULL )
      $padEmbedOpts [$padEmbedOption] = $padEmbedValue;

  $padEmbedHtml = padEmbed ( $padEmbedName, $padEmbedParts, $padEmbedTitle, $padEmbedOpts );

  return ( $padEmbedHtml === '' ) ? FALSE : $padEmbedHtml;

?>
