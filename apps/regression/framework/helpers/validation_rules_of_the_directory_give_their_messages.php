<?php

  // The rules added to pad/subs/inputs/rules/types/ beside the first twelve, each failing once with its
  // message: the argument as the message shows it - digits:4, between:18,65 read 18 and 65,
  // a list of prefixes read with or - and the number words of between on a number field.

  $ruleErrors = padValidate ( [
    'name'    => 'alpha',               'user'    => 'alphanum',
    'zip'     => 'digits:4',            'pin'     => 'digits',
    'nick'    => 'between:3,5',         'age'     => 'integer|between:18,65',
    'site'    => 'starts_with:https://, ftp://', 'file' => 'ends_with:.pdf',
    'handle'  => 'lowercase',           'country' => 'uppercase',
    'at'      => 'time',                'tint'    => 'color',
    'id'      => 'uuid',                'host'    => 'ip:v6',
    'page'    => 'slug',                'data'    => 'json',
    'account' => 'iban'
  ], [
    'name'    => 'Ann Lee',             'user'    => 'ann-1',
    'zip'     => '123',                 'pin'     => '12a',
    'nick'    => 'ab',                  'age'     => '70',
    'site'    => 'http://x.nl',         'file'    => 'x.doc',
    'handle'  => 'Ann',                 'country' => 'nl',
    'at'      => '25:00',               'tint'    => 'red',
    'id'      => '42',                  'host'    => '10.0.0.1',
    'page'    => 'Hello World',         'data'    => '{a:1}',
    'account' => 'NL91 ABNA 0417 1643 01'
  ] );

  $ruleText = implode ( " | ", $ruleErrors );

?>
