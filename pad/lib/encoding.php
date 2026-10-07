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
  // padMD5Unpack giving the hex form back), padRandomString (session and request ids,
  // temporary file names), and padZip / padUnzip (gzip for cached output).

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

  // The engine's own random names - the session and request ids, a temporary file - are
  // letters and digits, each drawn on its own by padStrRandom from random_int. They were cut
  // from base64 with every + and every / turned into one same character picked by mt_rand,
  // which made some ids easier to guess than their length says.

  function padRandomString ( $len = 8 ) {

    return padStrRandom ( $len );

  }

  // Both take NULL for an empty string. A value read out of a database row is NULL where the
  // column is, and a caller passing one straight in - the select subsystem does, for a row
  // whose relation field is empty - would otherwise end the request on str_replace().

  function padUnescape ( $string ) {

    return str_replace ( [ '&open;','&close;','&pipe;', '&eq;','&comma;','&at;', '&else;' ],
                         [ '{',     '}',      '|',      '=',   ',',      '@',    '@else@' ],
                         $string ?? '' );
  }

  // A brace the opening pipe masked (level/pipes/before.php, padPipeMask) is a brace of the
  // content all the same, so {json 'products' | ignore} still turns the content's braces to
  // text.

  function padEscape ( $string ) {

    list ( $open, $close ) = padPipeMarks ();

    return str_replace ( [ '{',     '}',      '|',      '=',    ',',     '@',    $open,    $close    ],
                         [ '&open;','&close;','&pipe;', '&eq;','&comma;','&at;', '&open;', '&close;' ],
                         $string ?? '' );
  }

  // The content's own braces as markers while an opening pipe runs over it, and back: what
  // the pipe adds can then be told from what the author wrote. A marker is a private-use
  // character followed by a nonce of six more drawn at random once per request, so a value
  // the pipe adds cannot bring one along: with a fixed marker, ?v=%EE%83%B4php:getcwd%EE%83%B5
  // in {n | @ . $v} came back a live tag and ran. No case or trim function changes them.

  function padPipeMarks () {

    static $marks = NULL;

    if ( $marks === NULL ) {
      $nonce = '';
      for ( $i = 0; $i < 6; $i++ )
        $nonce .= mb_chr ( random_int ( 0xF000, 0xF8FF ), 'UTF-8' );
      $marks = [ "\u{E0F4}$nonce", "\u{E0F5}$nonce" ];
    }

    return $marks;

  }

  function padPipeMask ( $string ) {

    return str_replace ( [ '{', '}' ], padPipeMarks (), $string );

  }

  function padPipeUnmask ( $string ) {

    return str_replace ( padPipeMarks (), [ '{', '}' ], $string );

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

  // The stand-ins of the two quotes and the backslash in a value about to be escaped, as the
  // characters they stand for. exits.php restores a stand-in only after every escaper has
  // run, so one that came in with the request - %EE%80%A2 is U+E022 - passed htmlentities
  // untouched and came out a live quote: title="{$name}" with ?name=x%EE%80%A2%20onclick=...
  // closed the attribute. An escaper reads it as the quote it will be, and escapes that.

  function padUnprotectQuotes ( $string ) {

    return str_replace ( [ "\u{E022}", "\u{E027}", "\u{E05C}" ], [ '"', "'", '\\' ], $string );

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
