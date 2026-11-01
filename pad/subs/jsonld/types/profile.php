<?php

  // {jsonld:profile data='member'/} or {jsonld:profile name='Ann Smith', handle='ann',
  // image='avatars/ann.jpg', followers=$followers, created=$joined/} - a ProfilePage: the
  // page of one person, or one organization, on a site of many - a forum's member, an
  // author's page, a shop's seller - with the person as its mainEntity.
  //
  // The fields, by their PAD name and the Schema.org property each fills - the required and
  // recommended properties of Google's profile page rich result: type (the mainEntity's
  // @type, Person when left out, or Organization), name (mainEntity.name, required),
  // handle (mainEntity.alternateName, the name the site knows them by), identifier
  // (mainEntity.identifier, an id of the site that stays when the handle changes),
  // description (mainEntity.description, a byline), image (mainEntity.image, one address
  // or more), profiles (mainEntity.sameAs, their pages elsewhere), followers
  // (mainEntity.interactionStatistic, an InteractionCounter of the FollowAction), posts
  // (mainEntity.agentInteractionStatistic, an InteractionCounter of the WriteAction, what
  // they wrote on the site), created (dateCreated) and modified (dateModified, a change
  // someone made, not one of the site's own) - the dates of the page itself. A row's
  // alternateName and sameAs are read as handle and profiles.

  return [

    'type'    => 'ProfilePage',

    'fields'  => [
      'type'        => [ 'mainEntity.@type',                                      [ 'word', [ 'Person', 'Organization' ] ] ],
      'name'        => [ 'mainEntity.name',                                       'text',  TRUE ],
      'handle'      => [ 'mainEntity.alternateName',                              'text',  FALSE, [ 'alternateName' ] ],
      'identifier'  => [ 'mainEntity.identifier',                                 'text'        ],
      'description' => [ 'mainEntity.description',                                'text'        ],
      'image'       => [ 'mainEntity.image',                                      'urls'        ],
      'profiles'    => [ 'mainEntity.sameAs',                                     'urls',  FALSE, [ 'sameAs' ] ],
      'followers'   => [ 'mainEntity.interactionStatistic.userInteractionCount',  'count'       ],
      'posts'       => [ 'mainEntity.agentInteractionStatistic.userInteractionCount', 'count'   ],
      'created'     => [ 'dateCreated',                                           'date'        ],
      'modified'    => [ 'dateModified',                                          'date'        ],
    ],

    'objects' => [ 'mainEntity'                           => 'Person',
                   'mainEntity.interactionStatistic'      => [ '@type' => 'InteractionCounter', 'interactionType' => 'https://schema.org/FollowAction' ],
                   'mainEntity.agentInteractionStatistic' => [ '@type' => 'InteractionCounter', 'interactionType' => 'https://schema.org/WriteAction' ] ],

  ];

?>
