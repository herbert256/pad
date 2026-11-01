<?php

  // sms - a text message through an HTTP gateway of the generic shape: a POST to SMS_URL of
  // JSON { "to": ..., "from": ..., "text": ... }, with SMS_TOKEN as a bearer token when it is
  // set and SMS_FROM as the sender; the number is the recipient's field phone. The @sms@
  // section, or the plain text, is the text. A gateway that wants another shape gets a copy
  // of this file with its own name.

  return [
    'settings' => [ 'url'   => [ 'env' => 'SMS_URL' ],
                    'token' => [ 'env' => 'SMS_TOKEN', 'default' => '' ],
                    'from'  => [ 'env' => 'SMS_FROM',  'default' => '' ],
                    'phone' => [ 'to'  => 'phone' ] ],
    'send'     => function ( $message, $settings ) {
      $phone = preg_replace ( '/[\s().-]/', '', (string) $settings ['phone'] );
      if ( ! preg_match ( '/^\+?\d{6,15}$/D', $phone ) )
        return 'the recipient\'s phone is no number - ' . padMakeSafe ( $settings ['phone'], 30 );
      $headers = ( $settings ['token'] !== '' ) ? [ 'Authorization' => 'Bearer ' . $settings ['token'] ] : [];
      $body    = [ 'to' => $phone, 'text' => $message ['text'] ];
      if ( $settings ['from'] !== '' )
        $body = [ 'to' => $phone, 'from' => (string) $settings ['from'], 'text' => $message ['text'] ];
      return padNotifyAnswer ( padNotifyHttp ( 'POST', (string) $settings ['url'], $body, $headers ), 'The SMS gateway' );
    },
  ];

?>
