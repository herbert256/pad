<?php

  // The playground's data is a text in any format PAD reads data in, its type recognised as
  // {data} recognises it: a record's keys - a YAML mapping, an XML document - become the
  // template's variables, and a list, which has no names of its own - CSV - becomes the
  // variable rows. The posts carry the token of the playground's own form, fetched first
  // with its cookie.

  $form    = padCurl ( $padHost . 'playground/' );
  $token   = preg_match ( '/name="padCsrfToken" value="([0-9a-f]+)"/', $form ['data'], $m ) ? $m [1] : '';
  $answers = [];

  foreach ( [ "title: yaml\nitems:\n  - name: apple\n  - name: pear"                       => '{$title}: {items}{$name} {/items}',
              '<data><title>xml</title><items><name>plum</name></items><items><name>fig</name></items></data>' => '{$title}: {items}{$name} {/items}',
              "name,stock\nkiwi,5\nlime,0"                                                    => 'csv: {rows}{$name}={$stock} {/rows}' ] as $data => $source )

    $answers [] = trim ( padCurl ( [
      'url'     => $padHost . 'playground/?render',
      'cookies' => [ 'PHPSESSID' => $form ['cookies'] ['PHPSESSID'] ?? '' ],
      'post'    => [ 'source' => $source, 'data' => $data, 'padCsrfToken' => $token ]
    ] ) ['data'] );

  $answer = implode ( ' | ', $answers );

?>
