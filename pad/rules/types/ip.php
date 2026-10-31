<?php

  // ip, ip:v4, ip:v6 - an IP address as FILTER_VALIDATE_IP reads one: four numbers of 0 to
  // 255 without a leading zero, or the groups of IPv6 with one :: at most - of either kind,
  // or of the one the argument names.

  return [
    'check'    => fn ( $value, $arg ) => filter_var ( $value, FILTER_VALIDATE_IP,
                    [ 'v4' => FILTER_FLAG_IPV4, 'v6' => FILTER_FLAG_IPV6 ] [ strtolower ( trim ( $arg ) ) ] ?? 0 ) !== FALSE,
    'argument' => fn ( $arg ) => in_array ( strtolower ( trim ( $arg ) ), [ '', 'v4', 'v6' ], TRUE ) ? '' : 'no version - ip:v4 or ip:v6',
    'message'  => fn ( $arg ) => match ( strtolower ( trim ( $arg ) ) ) {
                    'v4'    => ':label must be an IPv4 address',
                    'v6'    => ':label must be an IPv6 address',
                    default => ':label must be an IP address'
                  }
  ];

?>
