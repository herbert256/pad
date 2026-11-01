<?php

  // twilio - a text message through Twilio's Messaging API (the Message resource of its REST
  // API 2010-04-01): the account in TWILIO_ACCOUNT_SID - AC and 32 hex digits, in the address
  // of the resource - and its auth token in TWILIO_AUTH_TOKEN as the password of HTTP basic
  // authentication, the account's SID the user; with an API key in TWILIO_API_KEY (SK and 32)
  // the key is the user and TWILIO_AUTH_TOKEN holds its secret. The sender is TWILIO_FROM: a
  // number in E.164, a short code, an alphanumeric sender id - up to 11 letters, digits and
  // spaces, a letter among them, as Twilio's Trust Hub has it - or a Messaging Service's SID,
  // MG and 32, which goes as MessagingServiceSid. The number is the recipient's field phone,
  // its spaces, dots, dashes and parentheses taken out: Twilio wants E.164, + and the country
  // code, and a number without them is the data's reason not to send. The @twilio@ section,
  // or the plain text, is the Body, cut at Twilio's 1600 characters. Form-encoded, as the API
  // takes it; a 201 is sent, an error is the message of Twilio's JSON.

  return [
    'settings' => [ 'account' => [ 'env' => 'TWILIO_ACCOUNT_SID', 'pattern' => '/^AC[0-9a-fA-F]{32}$/D', 'is' => 'Twilio account SID - AC and 32 hex digits' ],
                    'token'   => [ 'env' => 'TWILIO_AUTH_TOKEN',  'pattern' => '/^[A-Za-z0-9]{32}$/D', 'secret' => TRUE,
                                   'is' => 'Twilio auth token or API key secret - 32 letters and digits' ],
                    'key'     => [ 'env' => 'TWILIO_API_KEY', 'default' => '', 'pattern' => '/^SK[0-9a-fA-F]{32}$/D', 'is' => 'Twilio API key SID - SK and 32 hex digits' ],
                    'from'    => [ 'env' => 'TWILIO_FROM', 'pattern' => '/^(\+[1-9][0-9]{6,14}|[0-9]{3,8}|MG[0-9a-fA-F]{32}|(?=[A-Za-z0-9 ]*[A-Za-z])[A-Za-z0-9 ]{1,11})$/D',
                                   'is' => 'Twilio sender - a number in E.164, a short code, a sender id of up to 11 letters, digits and spaces, or a Messaging Service SID' ],
                    'phone'   => [ 'to'  => 'phone' ] ],
    'send'     => function ( $message, $settings ) {
      $phone = preg_replace ( '/[\s().-]/', '', (string) $settings ['phone'] );
      if ( ! preg_match ( '/^\+[1-9][0-9]{6,14}$/D', $phone ) )
        return 'the recipient\'s phone is no number in E.164, + and the country code - ' . padMakeSafe ( (string) $settings ['phone'], 30 );
      $from = (string) $settings ['from'];
      $body = [ 'To' => $phone ] + ( str_starts_with ( $from, 'MG' ) && strlen ( $from ) == 34 ? [ 'MessagingServiceSid' => $from ] : [ 'From' => $from ] )
            + [ 'Body' => padNotifyCut ( $message ['text'], 1600 ) ];
      $user = ( (string) $settings ['key'] !== '' ) ? (string) $settings ['key'] : (string) $settings ['account'];
      return padNotifyAnswer ( padNotifyHttp ( 'POST', 'https://api.twilio.com/2010-04-01/Accounts/' . $settings ['account'] . '/Messages.json', http_build_query ( $body ),
                                               [ 'Authorization' => 'Basic ' . base64_encode ( $user . ':' . $settings ['token'] ),
                                                 'Content-Type'  => 'application/x-www-form-urlencoded' ] ), 'Twilio' );
    },
  ];

?>
