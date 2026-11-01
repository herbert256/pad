<?php

  // mac - A MAC address no maker gave a network card: locally administered and unicast - of
  // its first octet the second-lowest bit set and the lowest clear, as IEEE 802 counts them -
  // so it names no vendor, and written as six pairs of lower-case hexadecimal digits with
  // colons between, the way ip link shows one: 0a:3f:9c:41:d2:7e.

  $octets = [ ( padFakeNumber ( 0, 255 ) & 0xFC ) | 0x02 ];

  for ( $i = 0; $i < 5; $i++ )
    $octets [] = padFakeNumber ( 0, 255 );

  return implode ( ':', array_map ( fn ( $octet ) => sprintf ( '%02x', $octet ), $octets ) );

?>
