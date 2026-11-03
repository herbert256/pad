<?php
  $padCheckSyntax = FALSE; $report = [];
  $options = [ 'client_id' => 'fixture', 'client_secret' => 'secret', 'redirect' => 'https://shop.example/oauth/mastodon' ];
  foreach ( [ 'https://one.example', 'https://two.example' ] as $base ) {
    padCurlFake ( [ "$base/oauth/token" => [ 'data' => [ 'access_token' => 'fixture', 'token_type' => 'Bearer' ] ],
      "$base/api/v1/accounts/verify_credentials" => [ 'data' => [ 'id' => '42', 'username' => 'ann', 'acct' => 'ann', 'display_name' => 'Ann', 'email' => 'untrusted@example.org' ] ] ] );
    parse_str ( parse_url ( padOAuthUrl ( 'mastodon', [ 'base' => $base ] + $options ), PHP_URL_QUERY ), $asked );
    $_GET = [ 'code' => 'fixture-code', 'state' => $asked ['state'] ];
    $user = padOAuthUser ( 'mastodon', [ 'base' => $base ] + $options );
    $report ['users'] [] = [ $user ['id'], $user ['name'], $user ['email'], $user ['email_verified'], $asked ['code_challenge_method'] ];
  }
  padCurlFake ( [] );
  parse_str ( parse_url ( padOAuthUrl ( 'mastodon', [ 'base' => 'https://one.example' ] + $options ), PHP_URL_QUERY ), $asked );
  $_GET = [ 'code' => 'fixture-code', 'state' => $asked ['state'] ];
  $report ['mixup_rejected'] = ! padOAuthUser ( 'mastodon', [ 'base' => 'https://two.example' ] + $options ) && count ( padCurlRecorded () ) === 0;
  $_GET = [ 'code' => 'fixture-code', 'state' => 'not-kept-here' ];
  $report ['bad_state_rejected'] = ! padOAuthUser ( 'mastodon', [ 'base' => 'https://one.example' ] + $options ) && count ( padCurlRecorded () ) === 0;
  $report ['unsafe_base_rejected'] = padOAuthUrl ( 'mastodon', [ 'base' => 'https://one.example?redirect=evil' ] + $options ) === '';
  $json = json_encode ( $report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );
?>
