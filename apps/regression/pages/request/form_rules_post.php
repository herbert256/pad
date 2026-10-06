<?php

  // The rules of a form written on its fields in the template - rules='required|email' -
  // are checked before the page's PHP runs: a post that breaks them comes back refilled,
  // each message beside its field and the form's error= above them, and padPosted is
  // FALSE for it, so the PHP that would store it does not. A field in a branch that did
  // not render is checked all the same. A post that keeps them is padPosted, and the
  // page's own padValidate adds its errors to the template's.

  $formRulesResult = '';

  foreach ( [ [ 'qty' => '0', 'email' => 'x',       'note' => 'long', 'code' => 'z' ],
              [ 'qty' => '3', 'email' => 'a@b.nl',  'note' => 'ok',   'code' => 'z' ],
              [ 'qty' => '3', 'email' => 'a@b.nl',  'note' => 'ok'                  ],
              [ 'qty' => '3', 'email' => 'a@b.nl',  'note' => 'long', 'code' => 'z' ] ] as $formRulesPost ) {

    $formRulesCurl = padCurl ( [ 'url'  => $padGoExt . 'request/form_rules&padInclude',
                                 'post' => [ 'padForm' => 'order' ] + $formRulesPost ] );

    $formRulesResult .= $formRulesCurl ['result'] . "\n"
                      . preg_replace ( '/ value="[0-9a-f]{64}"/', '', trim ( $formRulesCurl ['data'] ) ) . "\n\n";

  }

?>
