<?php

  // slack - Sign in with Slack, OpenID Connect: a Slack app with its redirect URL set under
  // OAuth & Permissions, its client id and secret in SLACK_CLIENT_ID and SLACK_CLIENT_SECRET.
  // As docs.slack.dev has it (Sign in with Slack; the methods openid.connect.token and
  // openid.connect.userInfo): the scopes openid, email and profile, a nonce the id_token
  // carries back, the issuer https://slack.com, no PKCE - Slack takes it only from an app
  // switched to a public client, for good - and the user the userinfo answer, its email
  // verified when its email_verified says so. A Slack user id is unique within its workspace
  // alone (the user object's reference: store it with the team_id), so the sub is the pair,
  // T0R7GR/U0R7JM - the workspace from the id_token's https://slack.com/team_id claim; a token
  // without one logs no one in.
  //
  // The option team or SLACK_TEAM - a workspace's id, T0123ABC456 - limits the login to that
  // workspace: Slack is asked for it (team=, its sign-in page skipped when the visitor is
  // signed in there) and an id_token of any other workspace is refused.

  $team = trim ( (string) ( $options ['team'] ?? padEnv ( 'SLACK_TEAM', '' ) ) );

  return [
    'name'      => 'Slack',
    'icon'      => 'slack',
    'authorize' => 'https://slack.com/openid/connect/authorize',
    'token'     => 'https://slack.com/api/openid.connect.token',
    'userinfo'  => 'https://slack.com/api/openid.connect.userInfo',
    'issuer'    => 'https://slack.com',
    'claims'    => ( $team !== '' ) ? [ 'https://slack.com/team_id' => $team ] : [],
    'oidc'      => TRUE,
    'scopes'    => 'openid email profile',
    'pkce'      => FALSE,
    'params'    => ( $team !== '' ) ? [ 'team' => $team ] : [],
    'needs'     => [ 'client_id' => 'SLACK_CLIENT_ID', 'client_secret' => 'SLACK_CLIENT_SECRET' ],
    'user'      => [ 'id' => 'sub', 'name' => 'name|given_name', 'email' => 'email', 'verified' => 'email_verified', 'avatar' => 'picture', 'username' => '' ],
    'more'      => function ( $user, $answer ) {
      $team = $answer ['https://slack.com/team_id'] ?? NULL;
      return [ 'sub' => ( is_string ( $team ) and preg_match ( '/^[A-Z0-9]+$/D', $team ) and $user ['sub'] !== '' ) ? "$team/" . $user ['sub'] : '' ] + $user;
    },
  ];

?>
