<?php

  // A number is a Unix timestamp, fraction and all; text is read as PHP's DateTime reads
  // it, trimmed; a DateTimeInterface becomes a DateTimeImmutable. A timestamp and text
  // without a zone are in the application's zone, a zone given is kept.

  $padTimezone = 'UTC';

  $show = fn ( $value ) => padDateParse ( $value )->format ( 'Y-m-d H:i:s.u P' );

  $parsed = [
    'zero'     => $show ( 0 ),
    'text'     => $show ( '1759660800' ),
    'fraction' => $show ( 1759660800.25 ),
    'before'   => $show ( -1.5 ),
    'date'     => $show ( '2026-10-05' ),
    'trimmed'  => $show ( ' 2026-10-05 14:30 ' ),
    'zoned'    => $show ( '2026-10-05T10:00:00+02:00' ),
    'object'   => $show ( new DateTime ( '2020-02-29 12:00:00', new DateTimeZone ( 'Asia/Tokyo' ) ) ),
    'string'   => $show ( new class { function __toString () { return '2026-12-31'; } } )
  ];

  $mutable   = get_class ( padDateParse ( new DateTime ( '2020-01-01' ) ) );
  $immutable = new DateTimeImmutable ( '2020-01-01' );
  $kept      = ( padDateParse ( $immutable ) === $immutable ) ? 'kept' : 'copied';

  $parsed = json_encode ( $parsed );

?>
