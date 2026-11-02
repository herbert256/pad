<?php

  // padFake ( 'ipv6' ) is an address of 2001:db8::/32, the prefix RFC 3849 keeps for
  // documentation, written in the one text RFC 5952 allows - what inet_ntop writes for it -
  // with the :: of its longest run of zero groups in a good part of them; padFake ( 'mac' )
  // is a locally administered unicast address, FILTER_VALIDATE_MAC's, in lower case with
  // colons; the same seed the same addresses.

  padFakeSeed ( 11 );

  $canonical = $prefix = $mac = $local = 0;
  $runs      = 0;

  for ( $i = 0; $i < 1000; $i++ ) {

    $ip  = padFake ( 'ipv6' );
    $bin = @inet_pton ( $ip );

    $canonical += ( $bin !== FALSE and inet_ntop ( $bin ) === $ip and filter_var ( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6 ) ) ? 1 : 0;
    $prefix    += ( $bin !== FALSE and substr ( $bin, 0, 4 ) === "\x20\x01\x0d\xb8" ) ? 1 : 0;
    $runs      += str_contains ( $ip, '::' ) ? 1 : 0;

    $one    = padFake ( 'mac' );
    $mac   += ( filter_var ( $one, FILTER_VALIDATE_MAC ) and preg_match ( '/^([0-9a-f]{2}:){5}[0-9a-f]{2}$/D', $one ) ) ? 1 : 0;
    $local += ( ( hexdec ( substr ( $one, 0, 2 ) ) & 3 ) == 2 ) ? 1 : 0;

  }

  padFakeSeed ( 4 );  $first  = padFake ( 'ipv6' ) . ' ' . padFake ( 'mac' );
  padFakeSeed ( 4 );  $second = padFake ( 'ipv6' ) . ' ' . padFake ( 'mac' );

  $r = json_encode ( [
    'canonical' => $canonical,
    'prefix'    => $prefix,
    'compressed' => $runs > 150 and $runs < 350,
    'mac'       => $mac,
    'local'     => $local,
    'seed'      => $first === $second,
  ] );

?>
