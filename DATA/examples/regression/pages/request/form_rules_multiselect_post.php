<?php

  // The rules on a multiselect of the template are the rules of its list, interests[], checked
  // before the page's PHP as those of {input:field}: a post with nothing chosen breaks required and
  // comes back with nothing chosen - not value= - a choice that is no option breaks in: and
  // comes back without it, the other list refilled, and a post of choices of the lists is
  // stored. A list was refused under the name the template wrote, interests, as no single
  // value. Each post carries padLists[] naming both lists, as the browser posts the form.
  // The style and the script of the fields, and the session's token, are left out of what
  // is compared.

  $pickResult = '';
  $pickLists  = '&padLists[]=interests[]&padLists[]=days[]';

  foreach ( [ 'padForm=pick' . $pickLists,
              'padForm=pick&interests[]=PHP&interests[]=Nope&days[]=Monday' . $pickLists,
              'padForm=pick&interests[]=Maps&interests[]=Music&days[]=Tuesday&days[]=Monday' . $pickLists ] as $pickPost ) {

    $pickCurl = padCurl ( [ 'url'  => $padGoExt . 'request/form_rules_multiselect&padInclude',
                            'post' => $pickPost ] );

    $pickData = preg_replace ( '~<(style|script)\b[^>]*>.*?</\1>|<link\b[^>]*>~s', '', $pickCurl ['data'] );
    $pickData = preg_replace ( '/ value="[0-9a-f]{64}"/', '', $pickData );

    $pickResult .= $pickCurl ['result'] . "\n" . trim ( $pickData ) . "\n\n";

  }

?>
{!pickResult}
