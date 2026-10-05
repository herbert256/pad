<?php

  // Reads what the browser already has and what it can accept, into the globals the exits
  // consult when deciding on a response.
  //
  // $padClientEtag is the If-None-Match value with its quotes stripped, compared against the
  // freshly computed $padEtag by exits/output/web.php to answer 304 instead of resending the
  // page; $padClientDate is If-Modified-Since as a timestamp; $padClientGzip records whether
  // the response may be compressed - Accept-Encoding read with its quality values by
  // padAcceptsEncoding (lib/output.php), so gzip;q=0 is the refusal it is.

  // If-None-Match is read as the list it may be - a weak W/ prefix and the quotes dropped,
  // * kept - and only tags of the shape PAD mints survive (padMD5: 22 of A-Z a-z 0-9 _ -),
  // since a tag goes on to the cache backends as a key, to the file backend as a file name.
  // It was cut out as characters 2 to 23, so W/"..", a list or * never matched anything.
  // $padClientEtags holds them all, $padClientEtag the first.

  $padClientEtags = [];

  foreach ( explode ( ',', $_SERVER['HTTP_IF_NONE_MATCH'] ?? '' ) as $padClientTag ) {

    $padClientTag = trim ( preg_replace ( '/^\s*W\//', '', $padClientTag ), " \t\"" );

    // A gzip body carries its own tag, the same one with -gzip behind it (lib/output.php);
    // the representation underneath is one, so the suffix comes off for the comparison.

    if ( $padClientTag == '*' )
      $padClientEtags [] = $padClientTag;
    elseif ( preg_match ( '/^([A-Za-z0-9_-]{22})(-gzip)?$/', $padClientTag, $padClientMatch ) )
      $padClientEtags [] = $padClientMatch [1];

  }

  $padClientEtag = $padClientEtags [0] ?? '';
  $padClientDate = isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])  ? strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) : 0;
  $padClientGzip = padAcceptsEncoding ( $_SERVER['HTTP_ACCEPT_ENCODING'] ?? '', 'gzip' );

  // The format the request asks for (lib/expose.php): $padClientFormat is 'json' or 'csv'
  // from ?padFormat= or an Accept header that prefers one, and $padClientFormatAsked says it
  // was asked for outright - a page that cannot answer that way says 406, where a page asked
  // only through Accept renders its HTML. build/expose.php decides, once the page's PHP has
  // said what it exposes.

  $padClientFormatAsked = isset ( $_REQUEST ['padFormat'] );
  $padClientFormat      = padClientFormat ();

?>