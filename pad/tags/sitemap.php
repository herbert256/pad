<?php

  // {sitemap} ... {/sitemap}: the pages of the application as rows - page, url, lastmod -
  // generated from the file tree by lib/sitemap.php, for an HTML sitemap or a menu. A first
  // parameter names a directory, {sitemap 'docs'}, and lists the pages below it only.

  $padSitemapDir = trim ( (string) ( $padParm ?? '' ), '/' );

  // A directory is named the way a page is - no _ directory, nothing that leaves the
  // application.

  if ( $padSitemapDir !== ''
       and ( ! preg_match ( '/^[a-zA-Z0-9][a-zA-Z0-9_-]*(\/[a-zA-Z0-9][a-zA-Z0-9_-]*)*$/D', $padSitemapDir )
             or ! is_dir ( APP . $padSitemapDir ) ) ) {
    if ( $padCheckSyntax )
      padError ( "there is no directory '" . padMakeSafe ( $padSitemapDir, 60 ) . "' for {sitemap}" );
    return FALSE;
  }

  return padSitemapPages ( $padSitemapDir );

?>