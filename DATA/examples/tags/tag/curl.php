<?php

  $tagAbout   = 'Fetches a URL and answers the body of the response, optionally kept in a cache for a number of seconds.';
  $tagGroup   = 'network';
  $tagForm    = 'single';

  $tagSyntax  = <<<'PAD'
{curl 'https://example.com/data.json'}
{curl url='https://example.com/data.json', ttl=600}
{curl 'https://example.com/api', post='a=1&b=2', user='name', password=$secret}
{curl 'SELF://shop/?orders', $status='open'}
{curl 'https://example.com/api', get=$query, cookies=$jar, headers=$headers, options=$curlOptions}
PAD;

  $tagParms   = [
    'url' => 'The address - also written <code>url=</code>. <code>SELF://</code> at its start is this site\'s own host and mount prefix.' ];

  $tagOptions = [
    'ttl'      => 'Keep the answer this many seconds; when the source fails afterwards the last good copy is served and the failure logged.',
    'post'     => 'The body to post - the request is then a POST.',
    'user'     => 'The user name of basic authentication.',
    'password' => 'The password of basic authentication.',
    'get'      => 'An array of query values added to the URL.',
    'cookies'  => 'An array of cookies to send.',
    'headers'  => 'An array of request headers.',
    'options'  => 'An array of curl options by name.' ];

  $tagSee     = [ 'get', 'pad', 'data', 'mail' ];

?>
