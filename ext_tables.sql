#
# Table structure for table 'tx_directory_domain_model_organisation'
#
CREATE TABLE tx_directory_domain_model_organisation (
    identifier varchar(255) DEFAULT '' NOT NULL,
    name varchar(255) DEFAULT '' NOT NULL,
    short_name varchar(100) DEFAULT '' NOT NULL,
    organisation_type varchar(32) DEFAULT '' NOT NULL,
    short_description text,
    description mediumtext,
    email text,
    phone text,
    website text,
    social_profile text,
    logo int(11) unsigned DEFAULT 0 NOT NULL,
    media int(11) unsigned DEFAULT 0 NOT NULL,
    locations int(11) unsigned DEFAULT 0 NOT NULL,
    contact_persons int(11) unsigned DEFAULT 0 NOT NULL,
    business_units int(11) unsigned DEFAULT 0 NOT NULL,
    slug varchar(2048),
    seo_title varchar(255) DEFAULT '' NOT NULL,
    seo_description text,
    sorting int(11) DEFAULT 0 NOT NULL,

    KEY slug (slug(185), uid)
);

#
# Table structure for table 'tx_directory_domain_model_location'
#
CREATE TABLE tx_directory_domain_model_location (
    identifier varchar(255) DEFAULT '' NOT NULL,
    name varchar(255) DEFAULT '' NOT NULL,
    location_type varchar(32) DEFAULT 'private' NOT NULL,
    street varchar(255) DEFAULT '' NOT NULL,
    house_number varchar(50) DEFAULT '' NOT NULL,
    address_addition varchar(255) DEFAULT '' NOT NULL,
    post_office_box varchar(100) DEFAULT '' NOT NULL,
    zip varchar(20) DEFAULT '' NOT NULL,
    city varchar(255) DEFAULT '' NOT NULL,
    district varchar(255) DEFAULT '' NOT NULL,
    region varchar(255) DEFAULT '' NOT NULL,
    country varchar(8) DEFAULT 'DE' NOT NULL,
    short_description text,
    description mediumtext,
    email text,
    phone text,
    website text,
    opening_hours text,
    special_hours_note text,
    directions mediumtext,
    parking text,
    accessibility text,
    latitude decimal(10,8) DEFAULT NULL,
    longitude decimal(11,8) DEFAULT NULL,
    media int(11) unsigned DEFAULT 0 NOT NULL,
    slug varchar(2048),
    seo_title varchar(255) DEFAULT '' NOT NULL,
    seo_description text,
    sorting int(11) DEFAULT 0 NOT NULL,

    KEY location_type (location_type),
    KEY slug (slug(185), uid)
);

#
# Table structure for table 'tx_directory_domain_model_person'
#
CREATE TABLE tx_directory_domain_model_person (
    identifier varchar(255) DEFAULT '' NOT NULL,
    salutation varchar(32) DEFAULT '' NOT NULL,
    first_name varchar(255) DEFAULT '' NOT NULL,
    last_name varchar(255) DEFAULT '' NOT NULL,
    title varchar(100) DEFAULT '' NOT NULL,
    display_name varchar(255) DEFAULT '' NOT NULL,
    position varchar(255) DEFAULT '' NOT NULL,
    short_description text,
    description mediumtext,
    email text,
    phone text,
    mobile text,
    website text,
    social_profile text,
    image int(11) unsigned DEFAULT 0 NOT NULL,
    media int(11) unsigned DEFAULT 0 NOT NULL,
    organisations int(11) unsigned DEFAULT 0 NOT NULL,
    locations int(11) unsigned DEFAULT 0 NOT NULL,
    slug varchar(2048),
    seo_title varchar(255) DEFAULT '' NOT NULL,
    seo_description text,
    sorting int(11) DEFAULT 0 NOT NULL,

    KEY slug (slug(185), uid)
);

#
# Table structure for table 'tx_directory_person_location_mm'
#
CREATE TABLE tx_directory_person_location_mm (
    uid_local int(11) unsigned DEFAULT 0 NOT NULL,
    uid_foreign int(11) unsigned DEFAULT 0 NOT NULL,
    sorting int(11) unsigned DEFAULT 0 NOT NULL,
    sorting_foreign int(11) unsigned DEFAULT 0 NOT NULL,

    KEY uid_local (uid_local),
    KEY uid_foreign (uid_foreign)
);

#
# Table structure for table 'tx_directory_organisation_person_mm'
#
CREATE TABLE tx_directory_organisation_person_mm (
    uid_local int(11) unsigned DEFAULT 0 NOT NULL,
    uid_foreign int(11) unsigned DEFAULT 0 NOT NULL,
    sorting int(11) unsigned DEFAULT 0 NOT NULL,
    sorting_foreign int(11) unsigned DEFAULT 0 NOT NULL,

    KEY uid_local (uid_local),
    KEY uid_foreign (uid_foreign)
);

#
# Table structure for table 'tx_directory_person_organisation_mm'
#
CREATE TABLE tx_directory_person_organisation_mm (
    uid_local int(11) unsigned DEFAULT 0 NOT NULL,
    uid_foreign int(11) unsigned DEFAULT 0 NOT NULL,
    sorting int(11) unsigned DEFAULT 0 NOT NULL,
    sorting_foreign int(11) unsigned DEFAULT 0 NOT NULL,

    KEY uid_local (uid_local),
    KEY uid_foreign (uid_foreign)
);

#
# Table structure for table 'tx_directory_organisation_organisation_mm'
#
CREATE TABLE tx_directory_organisation_organisation_mm (
    uid_local int(11) unsigned DEFAULT 0 NOT NULL,
    uid_foreign int(11) unsigned DEFAULT 0 NOT NULL,
    sorting int(11) unsigned DEFAULT 0 NOT NULL,
    sorting_foreign int(11) unsigned DEFAULT 0 NOT NULL,

    KEY uid_local (uid_local),
    KEY uid_foreign (uid_foreign)
);

#
# Table structure for table 'tx_directory_organisation_location_mm'
#
CREATE TABLE tx_directory_organisation_location_mm (
    uid_local int(11) unsigned DEFAULT 0 NOT NULL,
    uid_foreign int(11) unsigned DEFAULT 0 NOT NULL,
    sorting int(11) unsigned DEFAULT 0 NOT NULL,
    sorting_foreign int(11) unsigned DEFAULT 0 NOT NULL,

    KEY uid_local (uid_local),
    KEY uid_foreign (uid_foreign)
);
