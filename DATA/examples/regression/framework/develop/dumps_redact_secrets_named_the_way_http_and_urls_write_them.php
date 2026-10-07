<?php

  // The reports redacted a secret by its name, written as PHP writes names: a request
  // header is X-Api-Key, with a hyphen, the curl option of a fetch with a login is USERPWD,
  // the headers PHP has sent come as a list of lines - Set-Cookie: carries the session -
  // and a DSN in the environment is a URL with the password inside it. All went out in clear.

  $redacted = json_encode ( padRedact ( [
    'X-Api-Key'    => 'k1',
    'X-Real-IP'    => '10.0.0.1',
    'USERPWD'      => 'shop:pw1',
    'passphrase'   => 'p1',
    'cookies'      => [ 'remember' => 'r1' ],
    'out'          => [ 'Set-Cookie: remember=r2; path=/', 'Content-Type: text/html', 'Proxy-Authorization: Basic eHl6' ],
    'DATABASE_URL' => 'mysql://shop:dbpw@db.example/shop',
    'site'         => 'https://user@example.com/a?b=c'
  ] ), JSON_UNESCAPED_SLASHES );

?>
