<?php

  // A field of one value - its name has no [] - posted as a list is refused, whatever its
  // rules: name[]=x passed required and color[]=red passed required and in:, and the page
  // went on with a list where its template has one text - strtoupper ( $color ) ended the
  // request. A list belongs to a field named name[].

  $oneResult = [];

  foreach ( [ 'name%5B%5D=x&color=red', 'name=Ann&color%5B%5D=red', 'name=Ann&color=red' ] as $onePost ) {

    $oneCurl = padCurl ( [ 'url' => $padGoExt . 'request/form_rules_one_value&padInclude', 'post' => "padForm=one&$onePost" ] );

    preg_match ( '/^.*stored.*$/m', $oneCurl ['data'], $oneMatch );

    $oneResult [] = urldecode ( $onePost ) . ': ' . $oneCurl ['result'] . ' ' . trim ( $oneMatch [0] ?? 'nothing' );

  }

  $oneResult = implode ( "\n", $oneResult );

?>
