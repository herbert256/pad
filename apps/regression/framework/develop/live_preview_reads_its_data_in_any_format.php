<?php

  // The Live's data is a text in any format PAD reads data in, its type recognised as
  // {format:auto} recognises it: a record's keys - a YAML mapping, an XML document - become the
  // template's variables, and a list, which has no names of its own - CSV - becomes the
  // variable rows. The posts carry the token of Live preview's own form, fetched first
  // with its cookie.

  $form    = padCurl ( $padHost . 'live/' );
  $token   = preg_match ( '/name="padCsrfToken" value="([0-9a-f]+)"/', $form ['data'], $m ) ? $m [1] : '';
  $answers = [];

  foreach ( [ "title: yaml\nitems:\n  - name: apple\n  - name: pear"                       => '{$title}: {array:items}{$name} {/array:items}',
              '<data><title>xml</title><items><name>plum</name></items><items><name>fig</name></items></data>' => '{$title}: {array:items}{$name} {/array:items}',
              "name,stock\nkiwi,5\nlime,0"                                                    => 'csv: {array:rows}{$name}={$stock} {/array:rows}' ] as $data => $source )

    $answers [] = trim ( padCurl ( [
      'url'     => $padHost . 'live/?render',
      'cookies' => [ 'PHPSESSID' => $form ['cookies'] ['PHPSESSID'] ?? '' ],
      'post'    => [ 'source' => $source, 'data' => $data, 'padCsrfToken' => $token ]
    ] ) ['data'] );

  $answer = implode ( ' | ', $answers );

?>
