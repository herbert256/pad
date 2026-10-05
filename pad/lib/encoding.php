<?php

  // Encoding, hashing and id-generation helpers used across the engine.
  //
  // padEscape / padUnescape are the important pair: they swap PAD's syntax characters
  // ({ } | = , @) for &open;-style entities and back, which is how literal text survives
  // the tag parser - the ignore option and JSON in attributes rely on it. padUnescape
  // also restores the @else@ marker.
  //
  // padProtect / padUnprotect are the pair behind $padProtectValues: a value on its way
  // into the page has its syntax characters swapped for stand-ins, and exits/exits.php
  // swaps them back as the page is written out. They cannot be the &open; entities, which
  // the expression evaluator decodes on purpose - a value standing inside another tag's
  // parameters would come back to life there.
  //
  // The rest are small utilities: padJsonForHtmlAttr (JSON safe inside an HTML attribute,
  // for {select} and {reactData}), padMD5 with its helpers padPack, padUnpack, padBase64
  // and padUnbase64 (a 22-character URL-safe digest used for etags and cache keys, with
  // padMD5Unpack giving the hex form back), padRandomString / padRandomChar (session and
  // request ids, temporary file names), and padZip / padUnzip (gzip for cached output).

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

  // The stand-ins come from the Unicode private use area, U+E000 plus the character's own
  // code: nothing in the engine reads them, no sanitize or case function changes them, and
  // a stand-in is still one character to the mb_ functions. The set is every character the
  // tag scanner or the expression parser reads as syntax - the six padEscape covers, the
  // two quotes that end a string and the backslash that escapes inside one - so a value
  // spliced into a quoted parameter cannot close the quote either. A value that is not a
  // string has none of them and passes unchanged.

  function padProtectMap () {

    return [ '{'  => "\u{E07B}", '}' => "\u{E07D}", '|' => "\u{E07C}",
             '='  => "\u{E03D}", ',' => "\u{E02C}", '@' => "\u{E040}",
             "'"  => "\u{E027}", '"' => "\u{E022}", '\\' => "\u{E05C}" ];

  }

  function padProtect ( $value ) {

    if ( ! is_string ( $value ) )
      return $value;

    return strtr ( $value, padProtectMap () );

  }

  function padUnprotect ( $string ) {

    return strtr ( $string ?? '', array_flip ( padProtectMap () ) );

  }

  // The tag kinds whose answer is template source rather than a value, which level/go.php
  // leaves unprotected: the include: type returns an _include/ snippet's text, the content:
  // type a stored {content} block (or a snippet or page it falls back to), and the common:
  // type a _common snippet when the name is not a _common tag.

  function padTagAnswersSource () {

    global $pad, $padType, $padTag;

    if ( in_array ( $padType [$pad], [ 'include', 'content' ] ) )
      return TRUE;

    return $padType [$pad] == 'common' and ! padCommonTagCheck ( $padTag [$pad] );

  }

  function padZip ($data) {

    return gzencode($data);

  }

  function padUnzip ($data) {

    return gzdecode($data);

  }

?>