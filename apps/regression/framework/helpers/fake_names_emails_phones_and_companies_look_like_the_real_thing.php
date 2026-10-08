<?php

  padFakeSeed ( 7 );

  $r = json_encode ( [
    'first'     => padFakeFirstName (),
    'last'      => padFakeLastName (),
    'name'      => padFakeName (),
    'email'     => padFakeEmail (),
    'fromName'  => padFakeEmail ( "Zoë O'Neil" ),
    'phone'     => padFakePhone (),
    'company'   => padFakeCompany (),
    'city'      => padFakeCity (),
    'country'   => padFakeCountry (),
    'street'    => padFakeStreet (),
    'words'     => padFakeWords ( 4 ),
    'sentence'  => padFakeSentence ( 5 ),
    'paragraph' => padFakeParagraph ( 2 ),
    'pick'      => padFakePick ( [ 'red', 'green', 'blue' ] ),
    'picks'     => padFakePicks ( [ 1, 2, 3, 4, 5 ], 3 ),
  ], JSON_UNESCAPED_UNICODE );

?>
