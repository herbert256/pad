<?php

  // A field the post leaves out is read from the query string - the variable and padRequest
  // take it from there - so the rules check it there: a post of name alone to an address
  // carrying website=javascript:..., plan=enterprise or role=admin passed the rules, which
  // looked at the post only, and the page stored what nothing had checked. The same values
  // in the body were refused; good values in the query string pass as they do in the body.

  $leftResult = [];

  foreach ( [ 'website=javascript:alert(1)', 'plan=enterprise', 'role=admin', 'website=https://example.com&plan=pro&role=user' ] as $leftQuery ) {

    $leftCurl = padCurl ( [ 'url'  => $padGoExt . 'request/a_field_a_post_leaves_out&' . str_replace ( [ ':', '(', ')', '/' ], [ '%3A', '%28', '%29', '%2F' ], $leftQuery ) . '&padInclude',
                            'post' => 'padForm=sign&name=Ann' ] );

    preg_match ( '/^.*stored.*$/m', $leftCurl ['data'], $leftMatch );

    $leftResult [] = "$leftQuery: " . $leftCurl ['result'] . ' ' . trim ( $leftMatch [0] ?? 'nothing' );

  }

  $leftResult = implode ( "\n", $leftResult );

?>
