<?php

  $user        = new stdClass;
  $user->name  = 'Bob';
  $user->tags  = [ 'admin', 'editor' ];
  $user->nick  = NULL;

  $settings = new ArrayObject ( [ 'mail' => [ 'from' => 'shop@example.com' ] ] );

  $orders = [
    [ 'number' => 101, 'lines' => [ [ 'sku' => 'A' ], [ 'sku' => 'B' ] ] ],
    [ 'number' => 102, 'lines' => [ [ 'sku' => 'C' ] ] ],
    [ 'number' => 103 ]
  ];

  $tag      = padArrGet ( $user, 'tags.1' );
  $nick     = json_encode ( padArrGet ( $user, 'nick', 'not used' ) );
  $from     = padArrGet ( [ 'settings' => $settings ], 'settings.mail.from' );
  $numbers  = json_encode ( padArrGet ( $orders, '*.number' ) );
  $lines    = json_encode ( padArrGet ( $orders, '*.lines' ) );
  $skus     = json_encode ( padArrGet ( $orders, '*.lines.*.sku' ) );
  $none     = json_encode ( padArrGet ( [], '*.number' ) );
  $notList  = padArrGet ( [ 'count' => 5 ], 'count.*', 'not a list' );
  $props    = json_encode ( padArrGet ( [ 'u' => $user ], 'u.*' ) );

?>
