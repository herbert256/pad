<?php

  // padRequest over real requests: a query, a form post over a query value of the same
  // name, a JSON body over the query (with a charset, and as a +json type), and the bodies
  // that are no input - JSON that does not parse, and JSON sent as text/plain.

  $inputUrl = $padGoExt . 'helpers/requests_input_echo&padInclude';
  $inputAsk = fn ( $extra, $input = [] ) => trim ( padCurl ( [ 'url' => $inputUrl . $extra ] + $input ) ['data'] );

  $inputLines = [
    $inputAsk ( '&a=+1+&b[]=x' ),
    $inputAsk ( '&a=query', [ 'post' => 'a=posted&c=+3+' ] ),
    $inputAsk ( '&a=query', [ 'post' => '{"a":" json ","n":5,"t":true,"z":null,"o":{"k":" v "}}',
                              'headers' => [ 'Content-Type' => 'application/json; charset=utf-8' ],
                              'options' => [ 'CUSTOMREQUEST' => 'PUT' ] ] ),
    $inputAsk ( '', [ 'post' => '[1,2]', 'headers' => [ 'Content-Type' => 'application/vnd.api+json' ] ] ),
    $inputAsk ( '', [ 'post' => 'not json', 'headers' => [ 'Content-Type' => 'application/json' ] ] ),
    $inputAsk ( '', [ 'post' => '{"a":1}',  'headers' => [ 'Content-Type' => 'text/plain' ] ] ),
  ];

  $inputResult = implode ( ' | ', $inputLines );

?>
