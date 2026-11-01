<?php

  // {jsonld:business data='shop'/} or {jsonld:business type='Bakery', name='The Loaf',
  // street='Main Street 1', city='Utrecht', country='NL', hours='Mo-Fr 08:00-18:00; Sa
  // 08:00-16:00'/} - a LocalBusiness: a shop, a practice, a garage - a place people come to,
  // where it is, how to reach it and when it is open. A restaurant, a cafe or a bakery that
  // says what it serves is the restaurant kind.
  //
  // The fields, by their PAD name and the Schema.org property each fills - the required and
  // recommended properties of Google's local business rich result: type (the @type: the
  // most specific of the 152 Schema.org types of a local business fits best, Dentist,
  // BookStore, Bakery - LocalBusiness when left out), name (name, required), description
  // (description), image (image, one address or more), url (url, the page of this place),
  // phone (telephone, with the country code), email (email), its address as a PostalAddress:
  // street (streetAddress), city (addressLocality, required), region (addressRegion),
  // postcode (postalCode) and country (addressCountry, NL or Netherlands), its place on the
  // map as GeoCoordinates: latitude (-90 to 90) and longitude (-180 to 180), the one no use
  // without the other, hours (openingHoursSpecification: Mo-Fr 09:00-17:30; Sa
  // 10:00-16:00) and prices (priceRange, $$ or 10-25 EUR, under 100 characters for Google).
  //
  // The types are those of schema.org V30.1 (2026-09-16) under LocalBusiness, read from its
  // schemaorg-current-https-types.csv (CC BY-SA 3.0).

  return [

    'type'    => 'LocalBusiness',

    'fields'  => [
      'type'        => [ '@type',                    [ 'word', [
        'LocalBusiness', 'AccountingService', 'AdultEntertainment', 'AmusementPark',
        'AnimalShelter', 'ArchiveOrganization', 'ArtGallery', 'Attorney', 'Audiology',
        'AutoBodyShop', 'AutoDealer', 'AutoPartsStore', 'AutoRental', 'AutoRepair', 'AutoWash',
        'AutomatedTeller', 'AutomotiveBusiness', 'Bakery', 'BankOrCreditUnion', 'BarOrPub',
        'BeautySalon', 'BedAndBreakfast', 'BikeStore', 'BookStore', 'BowlingAlley', 'Brewery',
        'CafeOrCoffeeShop', 'Campground', 'Casino', 'ChildCare', 'ClothingStore', 'ComedyClub',
        'CommunityHealth', 'ComputerStore', 'ConvenienceStore', 'CovidTestingFacility', 'DaySpa',
        'Dentist', 'DepartmentStore', 'Dermatology', 'DietNutrition', 'Distillery',
        'DryCleaningOrLaundry', 'Electrician', 'ElectronicsStore', 'Emergency', 'EmergencyService',
        'EmploymentAgency', 'EntertainmentBusiness', 'ExerciseGym', 'FastFoodRestaurant',
        'FinancialService', 'FireStation', 'Florist', 'FoodEstablishment', 'FurnitureStore',
        'GardenStore', 'GasStation', 'GeneralContractor', 'Geriatric', 'GolfCourse',
        'GovernmentOffice', 'GroceryStore', 'Gynecologic', 'HVACBusiness', 'HairSalon',
        'HardwareStore', 'HealthAndBeautyBusiness', 'HealthClub', 'HobbyShop',
        'HomeAndConstructionBusiness', 'HomeGoodsStore', 'Hospital', 'Hostel', 'Hotel',
        'HousePainter', 'IceCreamShop', 'IndividualPhysician', 'InsuranceAgency', 'InternetCafe',
        'JewelryStore', 'LegalService', 'Library', 'LiquorStore', 'Locksmith', 'LodgingBusiness',
        'MedicalBusiness', 'MedicalClinic', 'MensClothingStore', 'Midwifery', 'MobilePhoneStore',
        'Motel', 'MotorcycleDealer', 'MotorcycleRepair', 'MovieRentalStore', 'MovieTheater',
        'MovingCompany', 'MusicStore', 'NailSalon', 'NightClub', 'Notary', 'Nursing', 'Obstetric',
        'OfficeEquipmentStore', 'Oncologic', 'Ophthalmology', 'Optician', 'Optometric',
        'Otolaryngologic', 'OutletStore', 'PawnShop', 'Pediatric', 'PetStore', 'Pharmacy',
        'Physician', 'PhysiciansOffice', 'Physiotherapy', 'PlasticSurgery', 'Plumber', 'Podiatric',
        'PoliceStation', 'PostOffice', 'PrimaryCare', 'ProfessionalService', 'Psychiatric',
        'PublicHealth', 'PublicSwimmingPool', 'RadioStation', 'RealEstateAgent', 'RecyclingCenter',
        'Resort', 'Restaurant', 'RoofingContractor', 'SelfStorage', 'ShoeStore', 'ShoppingCenter',
        'SkiResort', 'SportingGoodsStore', 'SportsActivityLocation', 'SportsClub',
        'StadiumOrArena', 'Store', 'TattooParlor', 'TelevisionStation', 'TennisComplex',
        'TireShop', 'TouristInformationCenter', 'ToyStore', 'TravelAgency', 'VacationRental',
        'WholesaleStore', 'Winery' ] ] ],
      'name'        => [ 'name',                     'text',                      TRUE ],
      'description' => [ 'description',              'text'                            ],
      'image'       => [ 'image',                    'urls'                            ],
      'url'         => [ 'url',                      'url'                             ],
      'phone'       => [ 'telephone',                'text'                            ],
      'email'       => [ 'email',                    'text'                            ],
      'street'      => [ 'address.streetAddress',    'text'                            ],
      'city'        => [ 'address.addressLocality',  'text',                      TRUE ],
      'region'      => [ 'address.addressRegion',    'text'                            ],
      'postcode'    => [ 'address.postalCode',       'text'                            ],
      'country'     => [ 'address.addressCountry',   'text'                            ],
      'latitude'    => [ 'geo.latitude',             [ 'number', [ -90, 90 ] ]         ],
      'longitude'   => [ 'geo.longitude',            [ 'number', [ -180, 180 ] ]       ],
      'hours'       => [ 'openingHoursSpecification', 'hours'                          ],
      'prices'      => [ 'priceRange',               'text'                            ],
    ],

    'objects' => [ 'address' => 'PostalAddress', 'geo' => 'GeoCoordinates' ],

    'needs'   => [ 'latitude' => 'longitude', 'longitude' => 'latitude' ],

  ];

?>
