<?php

  $settings = [
    'mail'  => [ 'from' => 'shop@example.com', 'smtp' => [ 'host' => 'localhost', 'port' => 25 ] ],
    'theme' => 'dark'
  ];

  $flat = padArrDot ( $settings );

  $changes = padArrUndot ( [ 'mail.smtp.port' => 587, 'theme' => 'light' ] );
  $port    = $changes ['mail'] ['smtp'] ['port'];

  $tags    = implode ( ', ', padArrFlatten ( [ 'php', [ 'pad', [ 'templates', 'helpers' ] ] ] ) );
  $wrapped = count ( padArrWrap ( 'one recipient' ) ) . ' / ' . count ( padArrWrap ( NULL ) );

?>
