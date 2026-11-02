<?php

  // The providers are the files of pad/subs/oauth/types/, as the engine reads them (padOAuthKinds).

  $tagAbout   = 'Writes a "Log in with ..." link styled as a button, for the OAuth login of padOAuthStart and padOAuthUser.';
  $tagGroup   = 'access';
  $tagForm    = 'single';

  $tagSyntax  = <<<'PAD'
{pad:oauth 'github'/}
{pad:oauth 'oidc', href='?login/company', label='Log in with your company account'/}
PAD;

  $tagParms   = [
    'provider' => 'The provider, a file of <code>pad/subs/oauth/types/</code>: '
                    . implode ( ', ', array_map ( fn ( $kind ) => "<code>$kind</code>", padOAuthKinds () ) ) . '.' ];

  $tagOptions = [
    'href'  => 'Where it links - <code>oauth/&lt;provider&gt;</code> of the application by default, the page that calls <code>padOAuthStart</code> and <code>padOAuthUser</code>.',
    'label' => 'Its text, <code>Log in with &lt;name&gt;</code> by default.' ];

  $tagSee     = [ 'auth', 'guest', 'form' ];

  $subsystemKindLinks = appSubsystemLinks ( 'oauth' );

?>
