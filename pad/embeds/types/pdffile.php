<?php

  // pdffile - A PDF in the browser's own viewer, with a link to open it under the frame. A
  // file of www/<application>/, versioned as an {asset}, is a frame of the application's
  // own, loaded lazily; one of another site is a facade first - a drawn page, the title and
  // a button - its frame loaded on the click. 4:5 unless ratio= says otherwise.

  return [

    'name'    => 'a PDF',
    'kind'    => 'document',
    'generic' => TRUE,

    'match'   => fn ( $source, $named ) => padEmbedFile ( $source, [ 'pdf' ] ),

    'embed'   => function ( $parts, $opts ) {

      // A PDF of another site names that site: it is the one the click loads from.

      if ( ! $parts ['local'] )
        return [ 'src' => $parts ['url'], 'link' => $parts ['url'], 'ratio' => '4:5',
                 'name' => preg_replace ( '/^www\./', '', strtolower ( parse_url ( $parts ['url'], PHP_URL_HOST ) ) ) ];

      $url = padAsset ( $parts ['url'] );

      return [ 'mode' => 'page', 'src' => $url, 'link' => $url, 'ratio' => '4:5' ];

    },

  ];

?>
