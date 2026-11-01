<?php

  // The signature of Standard Webhooks (standardwebhooks.com, spec/standard-webhooks.md), for
  // the webhook channel: the content signed is the message's id, its timestamp and its body
  // joined by full stops, signed with HMAC-SHA256 under the secret - the base64 after whsec_ -
  // and written v1,<base64 of the MAC>; a header holds one per secret, split by spaces, so a
  // receiver still holding the old secret checks a message signed while a new one is rolled
  // in. A receiver compares in constant time and refuses a timestamp far from its clock.
  //
  // padWebhookSecret     the bytes of a secret - whsec_ and base64, the prefix optional - or
  //                      NULL when it is no padded base64 of 24 to 64 bytes, the spec's size
  // padWebhookSignature  the webhook-signature header: v1,<signature> for every secret

  function padWebhookSecret ( $secret ) {

    $secret = (string) $secret;

    if ( str_starts_with ( $secret, 'whsec_' ) )
      $secret = substr ( $secret, 6 );

    if ( ! preg_match ( '/^(?:[A-Za-z0-9+\/]{4})++(?:[A-Za-z0-9+\/]{2}==|[A-Za-z0-9+\/]{3}=)?$/D', $secret ) )
      return NULL;

    $bytes = base64_decode ( $secret, TRUE );

    return ( $bytes !== FALSE and strlen ( $bytes ) >= 24 and strlen ( $bytes ) <= 64 ) ? $bytes : NULL;

  }

  function padWebhookSignature ( $id, $timestamp, $body, $keys ) {

    $signed = "$id.$timestamp.$body";

    return implode ( ' ', array_map ( fn ( $key ) => 'v1,' . base64_encode ( hash_hmac ( 'sha256', $signed, $key, TRUE ) ), (array) $keys ) );

  }

?>
