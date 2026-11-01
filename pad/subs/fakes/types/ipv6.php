<?php

  // ipv6 - An IPv6 address of the prefix kept for documentation, 2001:db8::/32 (RFC 3849), so
  // a log full of them points at no one - a site, a subnet of it and a host: one of four a
  // host number set by hand, 2001:db8:4f2a:12::7, the others an interface identifier of
  // sixty-four bits, 2001:db8:4f2a:3:9c1e:22ff:fe3b:41d0. Written as RFC 5952 says an address
  // is written: lower case, no leading zeros, the longest run of two or more zero groups - the
  // first of two as long - as ::.

  $groups = [ 0x2001, 0x0db8, padFakeNumber ( 0, 0xFFFF ), padFakeNumber ( 0, 0xFF ) ];

  if ( padFakeNumber ( 0, 3 ) == 0 )
    array_push ( $groups, 0, 0, 0, padFakeNumber ( 1, 0xFF ) );
  else
    for ( $i = 0; $i < 4; $i++ )
      $groups [] = padFakeNumber ( 0, 0xFFFF );

  // The longest run of zero groups, the first when two are as long; one zero alone stays 0.

  $at = $length = $run = 0;

  foreach ( $groups as $index => $group ) {
    $run = $group ? 0 : $run + 1;
    if ( $run > $length )
      [ $at, $length ] = [ $index - $run + 1, $run ];
  }

  $hex = array_map ( 'dechex', $groups );

  if ( $length < 2 )
    return implode ( ':', $hex );

  return implode ( ':', array_slice ( $hex, 0, $at ) ) . '::' . implode ( ':', array_slice ( $hex, $at + $length ) );

?>
