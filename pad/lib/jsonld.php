<?php

  // The JSON-LD kinds: the files of subs/jsonld/types/ (padKinds, lib/subsystem.php) - a kind
  // is added by adding its file there. The rest is the subsystem in subs/jsonld/, its library
  // subs/jsonld/_lib/ loaded when a page writes a block.
  //
  //   $data = padJsonLd ( 'article', $post );                 one row, its fields by name
  //   $data = padJsonLd ( 'faq', $questions );                rows of question and answer
  //   $data = padJsonLd ( 'organization', $company, '#org' ); with an @id
  //
  // padJsonLd       the object {jsonld} writes for the same kind and rows, as a plain PHP
  //                 array - no HTML: json_encode it, add it to an API answer; a wrong
  //                 argument or a missing required field is reported with padError, and []
  //                 comes back
  // padJsonLdKinds  the kinds, sorted

  function padJsonLdKinds () {

    return padKinds ( 'jsonld' );

  }

  function padJsonLd ( $kind, $data = [], $id = NULL ) {

    include_once PAD . 'subs/jsonld/_lib/jsonld.php';

    $kind = strtolower ( trim ( (string) $kind ) );

    if ( ! padKind ( 'jsonld', $kind ) ) {
      padError ( "padJsonLd: there is no JSON-LD kind named '" . padMakeSafe ( $kind, 20 ) . "' - " . implode ( ', ', padJsonLdKinds () ) );
      return [];
    }

    if ( ! is_array ( $data ) and ! is_object ( $data ) and $data !== NULL ) {
      padError ( "padJsonLd: the data is a row or a list of rows - [ 'headline' => 'Hello' ]" );
      return [];
    }

    $rows = padJsonLdEmpty ( $data ) ? NULL : padJsonLdRows ( $data );

    return padJsonLdMake ( $kind, $rows, [], padJsonLdText ( $id ), fn ( $message ) => padError ( "padJsonLd: $message" ) ) ?: [];

  }

?>
