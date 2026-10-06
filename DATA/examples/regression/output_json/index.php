<?php

  // The json writer's promise: a plain 200, the JSON content type, a body that is exactly the
  // exposed variables - the template, which would fail, never runs - and the same page as a
  // table when the request asks for csv. The verdicts are this page's own answer, exposed.

  $r = padCurl ( $padHost . 'regression/output_json/?payload' );

  $verdict = ( $r ['result'] == '200'
               and str_contains ( $r ['headers'] ['Content-Type'] ?? '', 'application/json' )
               and json_decode ( $r ['data'], TRUE ) === [ 'greet' => 'carried all the way',
                                                           'items' => [ [ 'name' => 'one', 'n' => 1 ],
                                                                        [ 'name' => 'two', 'n' => 2 ] ] ] ) ? 'yes' : 'NO';

  $c = padCurl ( $padHost . 'regression/output_json/?payload&padFormat=csv' );

  $verdictCsv = ( $c ['result'] == '200'
                  and str_contains ( $c ['headers'] ['Content-Type'] ?? '', 'text/csv' )
                  and trim ( $c ['data'] ) === "name,n\none,1\ntwo,2" ) ? 'yes' : 'NO';

  $output = 'json';

  $padExpose = [ 'output', 'verdict', 'verdictCsv' ];

?>
