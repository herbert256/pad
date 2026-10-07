<?php

  // padPosted () asked without a name, on a page whose template gives the fields of the
  // form 'order' rules: a post that names no form, or a form the template has no rules for,
  // has kept none of them, and is not posted - it was, so leaving padForm out of the post
  // skipped every rule. A post of the form that keeps them is posted.

  $unnamedResult = [];

  foreach ( [ 'no form named' => [ 'qty' => '0', 'email' => 'x' ],
              'another form'  => [ 'padForm' => 'other', 'qty' => '0', 'email' => 'x' ],
              'the form'      => [ 'padForm' => 'order', 'qty' => '3', 'email' => 'a@b.nl', 'note' => 'ok', 'code' => 'z' ] ] as $unnamedWhat => $unnamedPost ) {

    $unnamedCurl = padCurl ( [ 'url'  => $padGoExt . 'request/form_rules&padInclude',
                               'post' => $unnamedPost ] );

    preg_match ( '/^.*processed.*$/m', $unnamedCurl ['data'], $unnamedMatch );

    $unnamedResult [] = "$unnamedWhat: " . $unnamedCurl ['result'] . ' ' . trim ( $unnamedMatch [0] ?? 'nothing' );

  }

  $unnamedResult = implode ( "\n", $unnamedResult );

?>
