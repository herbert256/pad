<?php


  /** Returns URL-safe base64 encoded MD5 hash (22 chars). */
  function padMD5 ($input) {
    return substr(padBase64(padPack(md5($input))),0,22);
  }

  /** Unpacks URL-safe base64 MD5 back to hex. */
  function padMD5Unpack ($input) {
    return padUnpack(padUnbase64 ($input.'=='));
  }

  /** Packs hex string to binary. */
  function padPack ($data) {
    return pack('H*',$data);
  }

  /** Unpacks binary to hex string. */
  function padUnpack ($data) {
    return unpack('H*',$data)[1];
  }

  /** URL-safe base64 encode (replaces +/ with _-). */
  function padBase64 ($string) {
    return strtr(base64_encode($string),'+/','_-');
  }

  /** URL-safe base64 decode (replaces _- with +/). */
  function padUnbase64 ($string) {
    return base64_decode(strtr($string,'_-','+/'));
  }

  /**
   * Generates cryptographically secure random string.
   *
   * @param int $len Length of string.
   *
   * @return string Random alphanumeric string.
   */
  function padRandomString ($len=8) {
    $random = ceil(($len/4)*3);
    $random = random_bytes($random);
    $random = base64_encode($random);
    $random = substr($random,0,$len);
    $random = str_replace ( '+', padRandomChar(), $random );
    $random = str_replace ( '/', padRandomChar(), $random );
    return $random;
  }

  /** Returns random alphanumeric character. */
  function padRandomChar () {
    $random = mt_rand(0,61);
    return ($random < 10) ? chr($random+48) : ($random < 36 ? chr($random+55) : chr($random+61));
  }


  /**
   * Restores escaped PAD entities to original characters.
   *
   * @param string $string String with entities.
   *
   * @return string String with restored characters.
   */
  function padUnescape ( $string ) {

    return str_replace ( [ '&open;','&close;','&pipe;', '&eq;','&comma;','&at;', '&else;' ],
                         [ '{',     '}',      '|',      '=',   ',',      '@',    '{else}' ],
                         $string );
  }


  /**
   * Escapes special PAD characters to entities.
   *
   * @param string $string String to escape.
   *
   * @return string String with escaped entities.
   */
  function padEscape ( $string ) {

    return str_replace ( [ '{',     '}',      '|',      '=',    ',',     '@'    ],
                         [ '&open;','&close;','&pipe;', '&eq;','&comma;','&at;' ],
                         $string );
  }


  /**
   * Compresses data using gzip.
   *
   * @param string $data Data to compress.
   *
   * @return string Compressed data.
   */
  function padZip ($data) {

    return gzencode($data);

  }


  /**
   * Decompresses gzip data.
   *
   * @param string $data Compressed data.
   *
   * @return string Decompressed data.
   */
  function padUnzip ($data) {

    return gzdecode($data);

  }


?>
