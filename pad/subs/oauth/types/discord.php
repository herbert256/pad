<?php

  // discord - Log in with Discord: an application of the developer portal, its id and secret
  // in DISCORD_CLIENT_ID and DISCORD_CLIENT_SECRET. The user is GET /users/@me (scopes
  // identify and email), the address verified when its verified says so; the avatar is the
  // CDN address its hash names.

  return [
    'name'      => 'Discord',
    'icon'      => 'discord',
    'authorize' => 'https://discord.com/oauth2/authorize',
    'token'     => 'https://discord.com/api/oauth2/token',
    'userinfo'  => 'https://discord.com/api/users/@me',
    'scopes'    => 'identify email',
    'pkce'      => TRUE,
    'needs'     => [ 'client_id' => 'DISCORD_CLIENT_ID', 'client_secret' => 'DISCORD_CLIENT_SECRET' ],
    'user'      => [ 'id' => 'id', 'name' => 'global_name|username', 'email' => 'email', 'verified' => 'verified', 'avatar' => '', 'username' => 'username' ],
    'more'      => function ( $user, $answer ) {
      $hash = $answer ['avatar'] ?? '';
      if ( is_string ( $hash ) and preg_match ( '/^(a_)?[0-9a-f]+$/D', $hash ) and preg_match ( '/^\d+$/D', $user ['sub'] ) )
        $user ['avatar'] = 'https://cdn.discordapp.com/avatars/' . $user ['sub'] . "/$hash.png";
      return $user;
    },
  ];

?>
