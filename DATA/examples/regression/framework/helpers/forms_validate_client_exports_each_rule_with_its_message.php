<?php

  // The rules as the browser's checker reads them: a field's readable name, whether it is a
  // number and a list, and per rule its argument and the message padValidate would give -
  // a message of the page's own included, the same: and in: arguments as the message
  // shows them.

  $out = json_encode ( padValidateClient ( [ 'age'       => 'integer|min:18',
                                             'tags[]'    => 'in:a,  b',
                                             'confirm'   => 'same:pass_word' ],
                                           [ 'age.min'   => ':label: :n or older' ] ),
                       JSON_UNESCAPED_SLASHES );

?>
