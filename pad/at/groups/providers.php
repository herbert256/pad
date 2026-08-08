<?php

  // The providers group: look the path $names up in the data a {reactData} provider
  // returned, so a template can reach the same records it handed to React.
  //
  // The store is keyed by the id= each {reactData} parked its result under - not by level,
  // which is what this read until the misc/react test asked and got nothing - so the whole
  // of it is searched, whatever level asks, and a store nothing filled is a miss.

  global $padProviders;

  return padAtSearch ( $padProviders ?? [], $names );

?>