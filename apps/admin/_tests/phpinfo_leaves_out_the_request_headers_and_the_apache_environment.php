<?php

  // The page phpinfo () writes under mod_php: besides the modules, the Apache environment
  // and every request header - the console's session cookie and a SetEnv secret among them.

  $infoPage = "<!DOCTYPE html><html><head><style>x</style></head><body><div class=\"center\">\n"
            . "<h2>apache2handler</h2>\n<table>\n<tr><td class=\"e\">Apache Version </td><td class=\"v\">Apache/2.4 </td></tr>\n</table>\n"
            . "<h2>Apache Environment</h2>\n<table>\n<tr><td class=\"e\">HTTP_COOKIE </td><td class=\"v\">PHPSESSID=secretsession </td></tr>\n"
            . "<tr><td class=\"e\">DB_PASSWORD </td><td class=\"v\">secretenv </td></tr>\n</table>\n"
            . "<h2>HTTP Headers Information</h2>\n<table>\n<tr class=\"h\"><th colspan=\"2\">HTTP Request Headers</th></tr>\n"
            . "<tr><td class=\"e\">Cookie </td><td class=\"v\">PHPSESSID=secretsession </td></tr>\n</table>\n"
            . "<h2><a name=\"module_session\">session</a></h2>\n<table>\n<tr><td class=\"e\">session.name</td><td class=\"v\">PHPSESSID</td></tr>\n</table>\n"
            . "</div></body></html>";

  $infoBody    = adminPhpinfoBody ( $infoPage );
  $infoSecrets = preg_match ( '/secretsession|secretenv/', $infoBody ) ? 'shown' : 'left out';
  $infoModules = ( str_contains ( $infoBody, 'Apache Version' ) and str_contains ( $infoBody, 'session.name' ) ) ? 'shown' : 'missing';

?>
