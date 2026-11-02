<?php

include_once COMMON . '_lib/subsystemCatalog.php';
$helpers = appSubsystemLinks ( [ 'oauth', 'rule', 'channel', 'theme', 'palette' ] );
$named = appSubsystemLinks ( 'ai' );
$custom = appSubsystemLinks ( [ 'ai', 'oauth' ], '/docs/' );
$ok = TRUE;
foreach ( [ '?kind/oauth/mastodon', '?kind/rule/ulid', '?kind/channel/zulip', '?kind/theme/projection', '?kind/palette/amber' ] as $href )
  $ok = $ok && str_contains ( $helpers, 'href="' . $href . '"' );
$ok = $ok && str_contains ( $named, 'href="?tag/ai/azure"' )
  && str_contains ( $custom, 'href="/docs/ai/azure"' )
  && str_contains ( $custom, 'href="/docs/oauth/mastodon"' );
preg_match_all ( '/href="([^"]+)"/', $helpers . $named, $links );
foreach ( $links [1] as $href )
  $ok = $ok && preg_match ( '~^\?(kind|tag)/[a-z]+/[a-z0-9_-]+$~D', $href );
$answer = $ok ? 'helper, named and explicit-base catalogue links retain their routes' : 'FAIL';

?>
