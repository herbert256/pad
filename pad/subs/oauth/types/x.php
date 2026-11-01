<?php

  // x - Log in with X (Twitter): a Web App of the X developer console - a confidential
  // client, with OAuth 2.0 switched on and the login page as its callback URL - its client id
  // and secret in X_CLIENT_ID and X_CLIENT_SECRET. As docs.x.com has it (OAuth 2.0
  // Authorization Code Flow with PKCE; user access token; GET /2/users/me): the authorize page
  // x.com/i/oauth2/authorize with PKCE, which X requires, and the scopes tweet.read and
  // users.read that /2/users/me needs, the code exchanged at api.x.com/2/oauth2/token with the
  // id and secret as HTTP Basic, and the user the data of GET /2/users/me - its id, name,
  // username and profile_image_url. An address comes only with the scope users.email, which
  // the app must be allowed in the console (scopes='tweet.read users.read users.email'): then
  // the field confirmed_email is asked for too, verified as its name says - X's data
  // dictionary: the confirmed email of the authenticated user.
  //
  // Its icon is the log-in one: the x of subs/icons/ is a close mark, not X's.

  $scopes = $options ['scopes'] ?? 'tweet.read users.read';
  $scopes = is_array ( $scopes ) ? $scopes : preg_split ( '/\s+/', trim ( (string) $scopes ) );
  $fields = in_array ( 'users.email', $scopes, TRUE ) ? 'profile_image_url,confirmed_email' : 'profile_image_url';

  return [
    'name'      => 'X',
    'icon'      => '',
    'authorize' => 'https://x.com/i/oauth2/authorize',
    'token'     => 'https://api.x.com/2/oauth2/token',
    'userinfo'  => "https://api.x.com/2/users/me?user.fields=$fields",
    'scopes'    => 'tweet.read users.read',
    'pkce'      => TRUE,
    'auth'      => 'basic',
    'needs'     => [ 'client_id' => 'X_CLIENT_ID', 'client_secret' => 'X_CLIENT_SECRET' ],
    'user'      => [ 'id' => 'data.id', 'name' => 'data.name|data.username', 'email' => 'data.confirmed_email', 'avatar' => 'data.profile_image_url',
                     'username' => 'data.username' ],
    'more'      => fn ( $user ) => [ 'email_verified' => $user ['email'] !== '' ] + $user,
  ];

?>
