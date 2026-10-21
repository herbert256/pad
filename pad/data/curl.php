<?php

  // Treats $data as a URL, fetches it, and feeds the response body back through padData()
  // using the content type curl reported, so a remote JSON/XML/CSV document becomes a PAD
  // data array. Included by padData() as data/<type>.php; padContentType picks 'curl' for
  // anything starting http: or https:, and a _data/*.curl file is read as this type. A
  // non-2xx status is a padError.
  //
  // A .curl file holds the URL, or a <curl> document naming it with the seconds to keep
  // the answer:
  //
  //   <curl>
  //     <url>https://api.example.com/rates.json</url>
  //     <ttl>600</ttl>
  //   </curl>
  //
  // The tag the data arrives for can give the ttl as well - {pad data='https://...',
  // ttl=600}, {rates ttl=600} - and the tag's wins. With a ttl the fetch goes through
  // padCurlCached (lib/curlCache.php), which also serves the last good copy when the
  // source fails. SELF:// stands for $padHost, as in {curl}.
  //
  // padCurlData (lib/prefetch.php) turns the answer into data - padPrefetch's answers go
  // the same way. A fetched body is data: PAD's own list syntax - ( 'a', $b, 2 * 3 ) -
  // evaluates every element as an expression, so under $padProtectValues a body that reads
  // as one is refused rather than run.

  $curlUrl = trim ( $data );
  $curlTtl = 0;

  if ( str_starts_with ( $curlUrl, '<' ) ) {

    $curlXml = @simplexml_load_string ( $curlUrl );

    if ( $curlXml === FALSE or $curlXml->getName () != 'curl' or ! isset ( $curlXml->url ) )
      return padError ( "a .curl document is <curl> with a <url> inside" );

    $curlUrl = trim ( (string) $curlXml->url );
    $curlTtl = (int) ( $curlXml->ttl ?? 0 );

  } else

    // The address as it was written (padParmText, lib/encoding.php): one that reached the
    // source through a printed parameter or field - a component's data='{#src}' - kept the
    // HTML escape of its & and the stand-in of its =, and asked for ?query&amp;a\u{E03D}1,
    // which php -S refused outright. lib/data.php padDataValueAddress judges this same text.
    // A value spliced into an address's query is the author's to encode, {$q | urlencode}.

    $curlUrl = padParmText ( $curlUrl );

  $curlUrl = str_replace ( 'SELF://', $GLOBALS ['padHost'], $curlUrl );
  $curlTtl = padTagParm ( 'ttl', $curlTtl );

  $curl = ( $curlTtl ) ? padCurlCached ( $curlUrl, $curlTtl ) : padCurl ( $curlUrl );

  return padCurlData ( $curl, $name );

?>
