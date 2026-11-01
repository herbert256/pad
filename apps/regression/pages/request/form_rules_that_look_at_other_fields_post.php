<?php

  // The rules that look at another field, posted for real: required_if on user[address] when
  // delivery is ship, required_without on phone when user[email] - $_POST ['user'] ['email'] -
  // is empty, after with a date of the rule and with the start field, and not_in. The first
  // post breaks them all and comes back refilled with each message; the second keeps them -
  // delivery collect, an e-mail, the end after the start - and is stored; the third is left
  // with the start before the date of its rule, the end left empty passing.

  $tripResult = '';

  foreach ( [ [ 'delivery' => 'ship',    'user[address]' => '',       'user[email]' => '',       'phone' => '',
                'start'    => '2026-03-01', 'end' => '2026-02-01', 'login' => 'root' ],
              [ 'delivery' => 'collect', 'user[address]' => '',       'user[email]' => 'a@b.nl', 'phone' => '',
                'start'    => '2026-03-01', 'end' => '2026-03-02', 'login' => 'ada' ],
              [ 'delivery' => 'ship',    'user[address]' => 'Main 1', 'user[email]' => '',       'phone' => '0612',
                'start'    => '2025-12-31', 'end' => '',           'login' => 'Root' ] ] as $tripPost ) {

    $tripCurl = padCurl ( [ 'url'  => $padGoExt . 'request/form_rules_that_look_at_other_fields&padInclude',
                            'post' => [ 'padForm' => 'trip' ] + $tripPost ] );

    $tripResult .= $tripCurl ['result'] . "\n"
                 . preg_replace ( '/ value="[0-9a-f]{64}"/', '', trim ( $tripCurl ['data'] ) ) . "\n\n";

  }

?>
