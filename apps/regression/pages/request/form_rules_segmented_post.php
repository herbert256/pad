<?php

  // The rules on a segmented of the template are checked before the page's PHP as those of
  // {input}: a post with no choice breaks required and comes back with none chosen - the other
  // segmented without one too, not its value= - a choice that is no option breaks in: and
  // comes back with none shown, and a post of choices of the bars is stored. The style and the
  // script of the fields, and the session's token, are left out of what is compared.

  $pickResult = '';

  foreach ( [ 'padForm=pick',
              'padForm=pick&size=XL&view=List',
              'padForm=pick&size=M&view=List' ] as $pickPost ) {

    $pickCurl = padCurl ( [ 'url'  => $padGoExt . 'request/form_rules_segmented&padInclude',
                            'post' => $pickPost ] );

    $pickData = preg_replace ( '~<(style|script)\b[^>]*>.*?</\1>|<link\b[^>]*>~s', '', $pickCurl ['data'] );
    $pickData = preg_replace ( '/ value="[0-9a-f]{64}"/', '', $pickData );

    $pickResult .= $pickCurl ['result'] . "\n" . trim ( $pickData ) . "\n\n";

  }

?>
{!pickResult}
