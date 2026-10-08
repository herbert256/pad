<?php

  // {asset 'charts.css'}: the address of a file of www/<application>/ with ?v= and a hash
  // of its contents (padAsset, lib/asset.php) - <link rel="stylesheet" href="{asset
  // 'charts.css'}">. {asset 'app.js', tag} writes the element itself: <link> for .css,
  // <script defer> for .js, <script type="module"> for .mjs, with the nonce when $padCsp
  // asks for one.

  if ( padTagParm ( 'tag', FALSE ) )
    return padAssetTag ( $padParm );

  return padAsset ( $padParm );

?>
