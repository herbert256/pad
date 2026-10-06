<?php

  // Rules on a field whose name PHP rewrites - user[email] is posted as $_POST ['user']
  // ['email'], first.name as $_POST ['first_name'] - check the value the field posted, and
  // the field refills from it. The rules looked for $_POST ['user[email]'], found nothing
  // and skipped every rule but required: a post of nope and a name of eleven letters was
  // stored, and a good post never passed required.

  $namesResult = '';

  foreach ( [ [ 'user[email]' => 'nope',   'first.name' => 'toolongname' ],
              [ 'user[email]' => 'a@b.nl', 'first.name' => 'Ann'         ] ] as $namesPost ) {

    $namesCurl = padCurl ( [ 'url'  => $padGoExt . 'request/form_rules_on_names_php_rewrites&padInclude',
                             'post' => [ 'padForm' => 'person' ] + $namesPost ] );

    $namesResult .= $namesCurl ['result'] . "\n"
                  . preg_replace ( '/ value="[0-9a-f]{64}"/', '', trim ( $namesCurl ['data'] ) ) . "\n\n";

  }

?>
