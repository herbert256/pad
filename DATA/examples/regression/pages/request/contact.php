<?php

  // The demo's contact form, posted with mistakes in it: what was typed comes back in the
  // fields, escaped - a password-free form refills every one of them - and each field that
  // broke a rule of padValidate shows its message beside it, in the words of its own label,
  // tied to it by aria-invalid and aria-describedby. Nothing is stored. The demo did this
  // by hand before, into a list above the form.

  $contactForm  = padCurl ( $padHost . 'demo/?contact&padInclude' );
  $contactToken = preg_match ( '/name="padCsrfToken" value="([0-9a-f]{64})"/', $contactForm ['data'], $contactMatch ) ? $contactMatch [1] : '';

  $contactCurl = padCurl ( [ 'url'     => $padHost . 'demo/?contact&padInclude',
                             'cookies' => [ 'PHPSESSID' => $contactForm ['cookies'] ['PHPSESSID'] ?? '' ],
                             'post'    => [ 'padCsrfToken' => $contactToken, 'padForm' => 'contact',
                                            'name' => 'Ann "the" <b>', 'email' => 'not-an-email',
                                            'subject' => '', 'message' => 'Hello' ] ] );

  preg_match_all ( '/<(?:input|textarea)\b[^>]*name="(?:name|email|subject|message)".*?(?=<\/p>)/s', $contactCurl ['data'], $contactFields );

  $contactResult = $contactCurl ['result'] . "\n" . implode ( "\n", $contactFields [0] );

?>
