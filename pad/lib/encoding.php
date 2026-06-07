<?php

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

  function padUnescape ( $string ) {

    return str_replace ( [ '&open;','&close;','&pipe;', '&eq;','&comma;','&at;', '&else;' ],
                         [ '{',     '}',      '|',      '=',   ',',      '@',    '@else@' ],
                         $string );
  }

  function padEscape ( $string ) {

    return str_replace ( [ '{',     '}',      '|',      '=',    ',',     '@'    ],
                         [ '&open;','&close;','&pipe;', '&eq;','&comma;','&at;' ],
                         $string );
  }

  function padZip ($data) {

    return gzencode($data);

  }

  function padUnzip ($data) {

    return gzdecode($data);

  }

?>