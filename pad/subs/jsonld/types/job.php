<?php

  // {jsonld:job data='vacancy'/} or {jsonld:job title='Baker', description=$text,
  // posted='2026-11-01', company='The Loaf', city='Utrecht', country='NL', employment='full
  // time', salary='3200-3800', currency='EUR', per='month'/} - a JobPosting: a vacancy, what
  // the work is, who offers it, where and for what pay.
  //
  // The fields, by their PAD name and the Schema.org property each fills - the required and
  // recommended properties of Google's job posting rich result: title (title, required -
  // the title of the job alone), description (description, required - the whole text, which
  // may hold <p>, <ul> and <li>), posted (datePosted, a date, required), expires
  // (validThrough, a date - left out when it does not), employment (employmentType, one or
  // more of FULL_TIME, PART_TIME, CONTRACTOR, TEMPORARY, INTERN, VOLUNTEER, PER_DIEM and
  // OTHER, in any case or spacing: full time), company (hiringOrganization, an Organization
  // by its name, required - confidential for one that stays unnamed), companyurl (its
  // sameAs, the company's own site) and logo (its logo), the place of work as the
  // PostalAddress of a Place: street, city, region, postcode and country (addressCountry,
  // which Google needs whenever the place is given), remote (yes writes jobLocationType
  // TELECOMMUTE, a job done entirely from home) with countries (applicantLocationRequirements,
  // the countries a remote worker may live in, each a Country by its name - beside a remote
  // that is no they are no use, an error as countries without a remote is), and the pay as a
  // MonetaryAmount: salary (an amount, 4200, or a range, 3200-3800), currency (ISO 4217)
  // and per (its unitText: HOUR, DAY, WEEK, MONTH or YEAR) - a salary needs both, as Google
  // asks for the actual pay of a period. Not min= and max=: those are options every tag
  // takes - a range is written in salary itself.

  return [

    'type'    => 'JobPosting',

    'fields'  => [
      'title'       => [ 'title',                                         'text',  TRUE ],
      'description' => [ 'description',                                   'text',  TRUE ],
      'posted'      => [ 'datePosted',                                    'date',  TRUE ],
      'expires'     => [ 'validThrough',                                  'date'        ],
      'employment'  => [ 'employmentType',                                [ 'words', [ 'FULL_TIME', 'PART_TIME', 'CONTRACTOR', 'TEMPORARY',
                                                                                       'INTERN', 'VOLUNTEER', 'PER_DIEM', 'OTHER' ] ] ],
      'company'     => [ 'hiringOrganization.name',                       'text',  TRUE ],
      'companyurl'  => [ 'hiringOrganization.sameAs',                     'url'         ],
      'logo'        => [ 'hiringOrganization.logo',                       'url'         ],
      'street'      => [ 'jobLocation.address.streetAddress',             'text'        ],
      'city'        => [ 'jobLocation.address.addressLocality',           'text'        ],
      'region'      => [ 'jobLocation.address.addressRegion',             'text'        ],
      'postcode'    => [ 'jobLocation.address.postalCode',                'text'        ],
      'country'     => [ 'jobLocation.address.addressCountry',            'text'        ],
      'remote'      => [ 'jobLocationType',                               [ 'flag', 'TELECOMMUTE' ] ],
      'countries'   => [ 'applicantLocationRequirements',                 [ 'names', 'Country' ] ],
      'currency'    => [ 'baseSalary.currency',                           'currency'    ],
      'salary'      => [ 'baseSalary.value',                              'salary'      ],
      'per'         => [ 'baseSalary.value.unitText',                     [ 'word', [ 'HOUR', 'DAY', 'WEEK', 'MONTH', 'YEAR' ] ] ],
    ],

    'objects' => [ 'hiringOrganization' => 'Organization', 'jobLocation' => 'Place', 'jobLocation.address' => 'PostalAddress',
                   'baseSalary' => 'MonetaryAmount', 'baseSalary.value' => 'QuantitativeValue' ],

    'needs'   => [ 'street' => 'country', 'city' => 'country', 'region' => 'country', 'postcode' => 'country',
                   'salary' => [ 'currency', 'per' ], 'currency' => 'salary', 'per' => 'salary', 'countries' => 'remote' ],

  ];

?>
