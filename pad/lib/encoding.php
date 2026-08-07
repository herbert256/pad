<?php

  // Encoding, hashing and id-generation helpers used across the engine.
  //
  // padEscape / padUnescape are the important pair: they swap PAD's syntax characters
  // ({ } | = , @) for &open;-style entities and back, which is how literal text survives
  // the tag parser - the ignore option and JSON in attributes rely on it. padUnescape
  // also restores the @else@ marker.
  //
  // The rest are small utilities: padJsonForHtmlAttr (JSON safe inside an HTML attribute,
  // for {select} and {reactData}), padMD5 with its helpers padPack, padUnpack, padBase64
  // and padUnbase64 (a 22-character URL-safe digest used for etags and cache keys, with
  // padMD5Unpack giving the hex form back), padRandomString / padRandomChar (keys for
  // fast links and ajax element ids), and padZip / padUnzip (gzip for cached output).

  function padJsonForHtmlAttr ( $input ) {
  
    return padEscape ( htmlspecialchars ( json_encode ( $input ), ENT_QUOTES, 'UTF-8' ) );

  }

  function padMD5 ($input) {
    return substr(padBase64(padPack(md5($input))),0,22);
  }

  function padMD5Unpack ($input) {
    return padUnpack(padUnbase64 ($input.'=='));
  }

  function padPack ($data) {
    return pack('H*',$data);
  }

  function padUnpack ($data) {
    return unpack('H*',$data)[1];
  }

  function padBase64 ($string) {
    return strtr(base64_encode($string),'+/','_-');
  }

  function padUnbase64 ($string) {
    return base64_decode(strtr($string,'_-','+/'));
  }

  function padRandomString ($len=8) {
    $random = ceil(($len/4)*3);
    $random = random_bytes($random);
    $random = base64_encode($random);
    $random = substr($random,0,$len);
    $random = str_replace ( '+', padRandomChar(), $random );
    $random = str_replace ( '/', padRandomChar(), $random );
    return $random;
  }

  function padRandomChar () {
    $random = mt_rand(0,61);
    return ($random < 10) ? chr($random+48) : ($random < 36 ? chr($random+55) : chr($random+61));
  }

  // Both take NULL for an empty string. A value read out of a database row is NULL where the
  // column is, and a caller passing one straight in - the select subsystem does, for a row
  // whose relation field is empty - would otherwise end the request on str_replace().

  function padUnescape ( $string ) {

    return str_replace ( [ '&open;','&close;','&pipe;', '&eq;','&comma;','&at;', '&else;' ],
                         [ '{',     '}',      '|',      '=',   ',',      '@',    '@else@' ],
                         $string ?? '' );
  }

  function padEscape ( $string ) {

    return str_replace ( [ '{',     '}',      '|',      '=',    ',',     '@'    ],
                         [ '&open;','&close;','&pipe;', '&eq;','&comma;','&at;' ],
                         $string ?? '' );
  }

  function padZip ($data) {

    return gzencode($data);

  }

  function padUnzip ($data) {

    return gzdecode($data);

  }

?>