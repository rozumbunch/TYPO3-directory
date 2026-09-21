<?php

declare(strict_types=1);

namespace Rozumbunch\Directory\Dummy;

use Rozumbunch\Directory\Domain\Model\Location;
use Rozumbunch\Directory\Domain\Model\Organisation;

/**
 * Fixture records for local dummy data. NEW* keys are DataHandler placeholders.
 *
 * @internal
 */
final class DummyDirectoryFixtures
{
    public const ID_PREFIX = 'dummy-';

    /**
     * @return array{
     *     locations: array<string, array<string, mixed>>,
     *     organisations: array<string, array<string, mixed>>,
     *     persons: array<string, array<string, mixed>>
     * }
     */
    public static function get(): array
    {
        $locations = [];
        $organisations = [];

        $locations['NEWLOCCOMPFFM'] = self::publicLocation(
            'loc-company-ffm',
            'Standort Frankfurt',
            'Hanauer Landstraße',
            '150',
            '60314',
            'Frankfurt am Main',
            'Hessen',
            50.11452,
            8.70418,
            'info@rheinmain-bildung.example',
            '+4969123400',
            'https://www.rheinmain-bildung.example',
            'Zentrale des Bildungsforum Rhein-Main in der Frankfurter Innenstadt.',
            ['flyer']
        );
        $locations['NEWLOCCOMPBER'] = self::publicLocation(
            'loc-company-ber',
            'Standort Berlin',
            'Friedrichstraße',
            '180',
            '10117',
            'Berlin',
            'Berlin',
            52.52081,
            13.38891,
            'info@hauptstadt-akademie.example',
            '+4930123400',
            'https://www.hauptstadt-akademie.example',
            'Berliner Standort der Hauptstadt Akademie nahe dem Bahnhof Friedrichstraße.',
            ['report']
        );

        $ffmDepartments = [
            'NEWORGDEPTFFM01' => ['Personal Rhein-Main', 'RMB-HR', 'Personal und Organisation'],
            'NEWORGDEPTFFM02' => ['Finanzen Rhein-Main', 'RMB-FIN', 'Finanzen und Controlling'],
            'NEWORGDEPTFFM03' => ['Kommunikation Rhein-Main', 'RMB-KOM', 'Kommunikation und Öffentlichkeitsarbeit'],
            'NEWORGDEPTFFM04' => ['Bildung Rhein-Main', 'RMB-BIL', 'Bildung und Programme'],
        ];
        $ffmUnits = [
            'NEWORGUNITFFM01' => ['Projektbüro Rhein-Main', 'RMB-PRO', 'Koordination laufender Projekte'],
            'NEWORGUNITFFM02' => ['Qualitätsmanagement Rhein-Main', 'RMB-QM', 'Qualitätssicherung und Evaluation'],
        ];
        $berDepartments = [
            'NEWORGDEPTBER01' => ['Personal Hauptstadt', 'HSA-HR', 'Personal und Organisation'],
            'NEWORGDEPTBER02' => ['Finanzen Hauptstadt', 'HSA-FIN', 'Finanzen und Controlling'],
            'NEWORGDEPTBER03' => ['Kommunikation Hauptstadt', 'HSA-KOM', 'Kommunikation und Öffentlichkeitsarbeit'],
            'NEWORGDEPTBER04' => ['Bildung Hauptstadt', 'HSA-BIL', 'Bildung und Programme'],
        ];
        $berUnits = [
            'NEWORGUNITBER01' => ['Campus Ost', 'HSA-OST', 'Standortübergreifende Campusarbeit'],
            'NEWORGUNITBER02' => ['Transferstelle Politik', 'HSA-TRF', 'Transfer zwischen Bildung und Politik'],
        ];

        foreach ([...$ffmDepartments, ...$berDepartments] as $newId => $row) {
            $isFfm = str_starts_with($newId, 'NEWORGDEPTFFM');
            $nestedUnits = match ($newId) {
                'NEWORGDEPTFFM04' => array_keys($ffmUnits),
                'NEWORGDEPTBER04' => array_keys($berUnits),
                default => [],
            };
            $organisations[$newId] = self::organisation(
                'org-' . self::slug($row[1]),
                $row[0],
                $row[1],
                Organisation::TYPE_DEPARTMENT,
                $row[2],
                'info@' . strtolower($row[1]) . '.example',
                $isFfm ? '+4969123410' : '+4930123410',
                $isFfm
                    ? 'https://www.rheinmain-bildung.example'
                    : 'https://www.hauptstadt-akademie.example',
                $isFfm ? 'NEWLOCCOMPFFM' : 'NEWLOCCOMPBER',
                $nestedUnits,
                [],
                true,
                []
            );
        }
        foreach ([...$ffmUnits, ...$berUnits] as $newId => $row) {
            $organisations[$newId] = self::organisation(
                'org-' . self::slug($row[1]),
                $row[0],
                $row[1],
                Organisation::TYPE_ORGUNIT,
                $row[2],
                'info@' . strtolower($row[1]) . '.example',
                str_starts_with($newId, 'NEWORGUNITFFM') ? '+4969123420' : '+4930123420',
                str_starts_with($newId, 'NEWORGUNITFFM')
                    ? 'https://www.rheinmain-bildung.example'
                    : 'https://www.hauptstadt-akademie.example',
                str_starts_with($newId, 'NEWORGUNITFFM') ? 'NEWLOCCOMPFFM' : 'NEWLOCCOMPBER',
                [],
                [],
                true,
                []
            );
        }

        $organisations['NEWORGCOMPFFM'] = self::organisation(
            'org-company-ffm',
            'Bildungsforum Rhein-Main GmbH',
            'RMB',
            Organisation::TYPE_COMPANY,
            'Trägergesellschaft für Bildung, Beratung und religionspädagogische Programme in Frankfurt.',
            'info@rheinmain-bildung.example',
            '+4969123400',
            'https://www.rheinmain-bildung.example',
            'NEWLOCCOMPFFM',
            array_keys($ffmDepartments),
            ['NEWPER01', 'NEWPER02'],
            true,
            ['flyer', 'report']
        );
        $organisations['NEWORGCOMPBER'] = self::organisation(
            'org-company-ber',
            'Hauptstadt Akademie GmbH',
            'HSA',
            Organisation::TYPE_COMPANY,
            'Akademie für Fortbildung und Bildungsentwicklung mit Sitz in Berlin-Mitte.',
            'info@hauptstadt-akademie.example',
            '+4930123400',
            'https://www.hauptstadt-akademie.example',
            'NEWLOCCOMPBER',
            array_keys($berDepartments),
            ['NEWPER03', 'NEWPER04'],
            true,
            ['flyer', 'report']
        );

        foreach (self::institutions() as $index => $institution) {
            $n = $index + 1;
            $locId = sprintf('NEWLOCINST%02d', $n);
            $orgId = sprintf('NEWORGINST%02d', $n);
            $emailLocal = self::slug($institution['name']);
            $locations[$locId] = self::publicLocation(
                sprintf('loc-institution-%02d', $n),
                'Standort ' . $institution['city'],
                $institution['street'],
                $institution['house_number'],
                $institution['zip'],
                $institution['city'],
                $institution['region'],
                $institution['lat'],
                $institution['lng'],
                'info@' . $emailLocal . '.example',
                $institution['phone'],
                'https://www.' . $emailLocal . '.example',
                $institution['short'],
                ['flyer']
            );
            $contact = $n <= 6 ? [sprintf('NEWPER%02d', $n + 8)] : [];
            $organisations[$orgId] = self::organisation(
                sprintf('org-institution-%02d', $n),
                $institution['name'],
                $institution['short_name'],
                Organisation::TYPE_INSTITUTION,
                $institution['short'],
                'info@' . $emailLocal . '.example',
                $institution['phone'],
                'https://www.' . $emailLocal . '.example',
                $locId,
                [],
                $contact,
                true,
                ['flyer']
            );
        }

        foreach (self::associations() as $index => $association) {
            $n = $index + 1;
            $locId = sprintf('NEWLOCASSOC%02d', $n);
            $orgId = sprintf('NEWORGASSOC%02d', $n);
            $emailLocal = self::slug($association['name']);
            $locations[$locId] = self::publicLocation(
                sprintf('loc-association-%02d', $n),
                'Geschäftsstelle ' . $association['city'],
                $association['street'],
                $association['house_number'],
                $association['zip'],
                $association['city'],
                'Hessen',
                $association['lat'],
                $association['lng'],
                'info@' . $emailLocal . '.example',
                $association['phone'],
                'https://www.' . $emailLocal . '.example',
                $association['short'],
                ['report']
            );
            $contact = $n <= 5 ? [sprintf('NEWPER%02d', $n + 15)] : [];
            $organisations[$orgId] = self::organisation(
                sprintf('org-association-%02d', $n),
                $association['name'],
                $association['short_name'],
                Organisation::TYPE_ASSOCIATION,
                $association['short'],
                'info@' . $emailLocal . '.example',
                $association['phone'],
                'https://www.' . $emailLocal . '.example',
                $locId,
                [],
                $contact,
                true,
                ['report']
            );
        }

        $persons = [];
        foreach (self::persons() as $index => $person) {
            $n = $index + 1;
            $locId = sprintf('NEWLOCPER%02d', $n);
            $perId = sprintf('NEWPER%02d', $n);
            $locations[$locId] = self::privateLocation(
                sprintf('loc-person-%02d', $n),
                'Privatadresse ' . $person['last_name'],
                $person['street'],
                $person['house_number'],
                $person['zip'],
                $person['city'],
                $person['region'],
                $person['lat'],
                $person['lng']
            );
            $pdfs = $n <= 10 ? ['flyer'] : ['flyer', 'report'];
            $persons[$perId] = self::person($person, $n, $locId, $pdfs);
        }

        return [
            'locations' => $locations,
            'organisations' => $organisations,
            'persons' => $persons,
        ];
    }

    /**
     * @param list<string> $pdfs
     * @return array<string, mixed>
     */
    private static function publicLocation(
        string $identifier,
        string $name,
        string $street,
        string $houseNumber,
        string $zip,
        string $city,
        string $region,
        float $lat,
        float $lng,
        string $email,
        string $phone,
        string $website,
        string $short,
        array $pdfs
    ): array {
        return [
            'identifier' => self::ID_PREFIX . $identifier,
            'name' => $name,
            'location_type' => Location::TYPE_COMPANY,
            'street' => $street,
            'house_number' => $houseNumber,
            'zip' => $zip,
            'city' => $city,
            'region' => $region,
            'country' => 'DE',
            'short_description' => $short,
            'description' => '<p>' . htmlspecialchars($short, ENT_QUOTES | ENT_HTML5, 'UTF-8') . '</p>',
            'email' => 'mailto:' . $email,
            'phone' => 'tel:' . $phone,
            'website' => $website,
            'opening_hours' => '<p>Montag bis Freitag, 09:00–17:00 Uhr</p>',
            'parking' => '<p>Öffentliche Parkplätze in der Umgebung.</p>',
            'accessibility' => '<p>Eingang ebenerdig, Aufzug vorhanden.</p>',
            'latitude' => (string)$lat,
            'longitude' => (string)$lng,
            'slug' => 'dummy-' . self::slug($name . '-' . $city),
            'seo_title' => $name . ' · ' . $city,
            'seo_description' => $short,
            '_pdfs' => $pdfs,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function privateLocation(
        string $identifier,
        string $name,
        string $street,
        string $houseNumber,
        string $zip,
        string $city,
        string $region,
        float $lat,
        float $lng
    ): array {
        return [
            'identifier' => self::ID_PREFIX . $identifier,
            'name' => $name,
            'location_type' => Location::TYPE_PRIVATE,
            'street' => $street,
            'house_number' => $houseNumber,
            'zip' => $zip,
            'city' => $city,
            'region' => $region,
            'country' => 'DE',
            'latitude' => (string)$lat,
            'longitude' => (string)$lng,
            'slug' => 'dummy-' . self::slug($name . '-' . $city),
        ];
    }

    /**
     * @param list<string> $businessUnits
     * @param list<string> $contactPersons
     * @param list<string> $pdfs
     * @return array<string, mixed>
     */
    private static function organisation(
        string $identifier,
        string $name,
        string $shortName,
        string $type,
        string $short,
        string $email,
        string $phone,
        string $website,
        string $locationId,
        array $businessUnits,
        array $contactPersons,
        bool $logo,
        array $pdfs
    ): array {
        return [
            'identifier' => self::ID_PREFIX . $identifier,
            'name' => $name,
            'short_name' => $shortName,
            'organisation_type' => $type,
            'short_description' => $short,
            'description' => '<p>' . htmlspecialchars($short, ENT_QUOTES | ENT_HTML5, 'UTF-8') . '</p>',
            'email' => 'mailto:' . $email,
            'phone' => 'tel:' . $phone,
            'website' => $website,
            'locations' => $locationId,
            'business_units' => implode(',', $businessUnits),
            'contact_persons' => implode(',', $contactPersons),
            'slug' => 'dummy-' . self::slug($name),
            'seo_title' => $name,
            'seo_description' => $short,
            '_logo' => $logo,
            '_pdfs' => $pdfs,
        ];
    }

    /**
     * @param array{
     *     salutation: string,
     *     first_name: string,
     *     last_name: string,
     *     title: string,
     *     position: string,
     *     organisations: list<string>,
     *     street: string,
     *     house_number: string,
     *     zip: string,
     *     city: string,
     *     region: string,
     *     lat: float,
     *     lng: float,
     *     phone: string,
     *     short: string
     * } $person
     * @param list<string> $pdfs
     * @return array<string, mixed>
     */
    private static function person(array $person, int $number, string $locationId, array $pdfs): array
    {
        $email = self::slug($person['first_name']) . '.' . self::slug($person['last_name']) . '@directory.example';
        $displayName = trim($person['title'] . ' ' . $person['first_name'] . ' ' . $person['last_name']);

        return [
            'identifier' => sprintf('%sper-%02d', self::ID_PREFIX, $number),
            'salutation' => $person['salutation'],
            'first_name' => $person['first_name'],
            'last_name' => $person['last_name'],
            'title' => $person['title'],
            'display_name' => $displayName,
            'position' => $person['position'],
            'short_description' => $person['short'],
            'description' => '<p>' . htmlspecialchars($person['short'], ENT_QUOTES | ENT_HTML5, 'UTF-8') . '</p>',
            'email' => 'mailto:' . $email,
            'phone' => 'tel:' . $person['phone'],
            'mobile' => 'tel:' . substr($person['phone'], 0, -2) . '88',
            'organisations' => implode(',', $person['organisations']),
            'locations' => $locationId,
            'slug' => 'dummy-' . self::slug($person['first_name'] . '-' . $person['last_name']),
            'seo_title' => $displayName,
            'seo_description' => $person['short'],
            '_image' => true,
            '_pdfs' => $pdfs,
        ];
    }

    /**
     * @return list<array{name: string, short_name: string, short: string, street: string, house_number: string, zip: string, city: string, region: string, lat: float, lng: float, phone: string}>
     */
    private static function institutions(): array
    {
        return [
            ['name' => 'Bildungszentrum Nord', 'short_name' => 'BZN', 'short' => 'Einrichtung für Fortbildung in Hamburg.', 'street' => 'Mönckebergstraße', 'house_number' => '7', 'zip' => '20095', 'city' => 'Hamburg', 'region' => 'Hamburg', 'lat' => 53.55108, 'lng' => 9.99368, 'phone' => '+4940123401'],
            ['name' => 'Religionspädagogisches Institut Bayern', 'short_name' => 'RPI-BY', 'short' => 'Landesweite Einrichtung in München.', 'street' => 'Ludwigstraße', 'house_number' => '27', 'zip' => '80539', 'city' => 'München', 'region' => 'Bayern', 'lat' => 48.14862, 'lng' => 11.58048, 'phone' => '+4989123402'],
            ['name' => 'Forum Lernen Rheinland', 'short_name' => 'FLR', 'short' => 'Lernort und Tagungshaus in Köln.', 'street' => 'Hohe Straße', 'house_number' => '52', 'zip' => '50667', 'city' => 'Köln', 'region' => 'Nordrhein-Westfalen', 'lat' => 50.93753, 'lng' => 6.96028, 'phone' => '+4922123403'],
            ['name' => 'Studienhaus Baden-Württemberg', 'short_name' => 'SH-BW', 'short' => 'Studienhaus für Lehrkräfte in Stuttgart.', 'street' => 'Königstraße', 'house_number' => '18', 'zip' => '70173', 'city' => 'Stuttgart', 'region' => 'Baden-Württemberg', 'lat' => 48.77844, 'lng' => 9.18001, 'phone' => '+4971123404'],
            ['name' => 'Medienwerkstatt Sachsen', 'short_name' => 'MWS', 'short' => 'Medienpädagogische Einrichtung in Leipzig.', 'street' => 'Petersstraße', 'house_number' => '36', 'zip' => '04109', 'city' => 'Leipzig', 'region' => 'Sachsen', 'lat' => 51.33970, 'lng' => 12.37307, 'phone' => '+4934123405'],
            ['name' => 'Campus Bildung Elbe', 'short_name' => 'CBE', 'short' => 'Campus für Fortbildung in Dresden.', 'street' => 'Prager Straße', 'house_number' => '10', 'zip' => '01069', 'city' => 'Dresden', 'region' => 'Sachsen', 'lat' => 51.04623, 'lng' => 13.73726, 'phone' => '+4935123406'],
            ['name' => 'Haus der Religionen Niedersachsen', 'short_name' => 'HRN', 'short' => 'Interreligiöse Einrichtung in Hannover.', 'street' => 'Georgstraße', 'house_number' => '24', 'zip' => '30159', 'city' => 'Hannover', 'region' => 'Niedersachsen', 'lat' => 52.37589, 'lng' => 9.73201, 'phone' => '+4951123407'],
            ['name' => 'Lernort Weser', 'short_name' => 'LOW', 'short' => 'Regionale Bildungseinrichtung in Bremen.', 'street' => 'Obernstraße', 'house_number' => '11', 'zip' => '28195', 'city' => 'Bremen', 'region' => 'Bremen', 'lat' => 53.07582, 'lng' => 8.80717, 'phone' => '+4942123408'],
            ['name' => 'Akademie Franken', 'short_name' => 'AKF', 'short' => 'Akademie für schulische Fortbildung in Nürnberg.', 'street' => 'Königstraße', 'house_number' => '70', 'zip' => '90402', 'city' => 'Nürnberg', 'region' => 'Bayern', 'lat' => 49.45210, 'lng' => 11.07667, 'phone' => '+4991123409'],
            ['name' => 'Bildungswerk Ruhr', 'short_name' => 'BWR', 'short' => 'Bildungswerk für Schule und Gemeinde in Dortmund.', 'street' => 'Westenhellweg', 'house_number' => '67', 'zip' => '44137', 'city' => 'Dortmund', 'region' => 'Nordrhein-Westfalen', 'lat' => 51.51359, 'lng' => 7.46530, 'phone' => '+4923123410'],
            ['name' => 'Forum Inklusion Ruhr', 'short_name' => 'FIR', 'short' => 'Einrichtung für inklusive Bildung in Essen.', 'street' => 'Kettwiger Straße', 'house_number' => '2', 'zip' => '45127', 'city' => 'Essen', 'region' => 'Nordrhein-Westfalen', 'lat' => 51.45564, 'lng' => 7.01156, 'phone' => '+4920123411'],
            ['name' => 'Studienzentrum Breisgau', 'short_name' => 'SZB', 'short' => 'Studienzentrum in Freiburg im Breisgau.', 'street' => 'Kaiser-Joseph-Straße', 'house_number' => '167', 'zip' => '79098', 'city' => 'Freiburg im Breisgau', 'region' => 'Baden-Württemberg', 'lat' => 47.99590, 'lng' => 7.85222, 'phone' => '+4976123412'],
            ['name' => 'Ostsee-Kolleg', 'short_name' => 'OSK', 'short' => 'Kolleg für Fortbildung in Rostock.', 'street' => 'Kröpeliner Straße', 'house_number' => '50', 'zip' => '18055', 'city' => 'Rostock', 'region' => 'Mecklenburg-Vorpommern', 'lat' => 54.08868, 'lng' => 12.14021, 'phone' => '+4938123413'],
            ['name' => 'Bildungsforum Saar', 'short_name' => 'BFS', 'short' => 'Landesforum für Bildung in Saarbrücken.', 'street' => 'Bahnhofstraße', 'house_number' => '40', 'zip' => '66111', 'city' => 'Saarbrücken', 'region' => 'Saarland', 'lat' => 49.23540, 'lng' => 6.99693, 'phone' => '+4968123414'],
            ['name' => 'Landesinstitut Sachsen-Anhalt', 'short_name' => 'LISA', 'short' => 'Landesinstitut für schulische Bildung in Magdeburg.', 'street' => 'Breiter Weg', 'house_number' => '31', 'zip' => '39104', 'city' => 'Magdeburg', 'region' => 'Sachsen-Anhalt', 'lat' => 52.13193, 'lng' => 11.63281, 'phone' => '+4939123415'],
        ];
    }

    /**
     * @return list<array{name: string, short_name: string, short: string, street: string, house_number: string, zip: string, city: string, lat: float, lng: float, phone: string}>
     */
    private static function associations(): array
    {
        return [
            ['name' => 'Verein für Religionspädagogik Frankfurt e.V.', 'short_name' => 'VRP-F', 'short' => 'Hessischer Fachverein mit Geschäftsstelle in Frankfurt.', 'street' => 'Braubachstraße', 'house_number' => '12', 'zip' => '60311', 'city' => 'Frankfurt am Main', 'lat' => 50.11092, 'lng' => 8.68213, 'phone' => '+4969123501'],
            ['name' => 'Hessischer Bildungsverein Wiesbaden e.V.', 'short_name' => 'HBV-WI', 'short' => 'Landesweiter Bildungsverein in der Landeshauptstadt.', 'street' => 'Wilhelmstraße', 'house_number' => '8', 'zip' => '65183', 'city' => 'Wiesbaden', 'lat' => 50.08200, 'lng' => 8.24165, 'phone' => '+4961133502'],
            ['name' => 'Forum Kirche und Schule Nordhessen e.V.', 'short_name' => 'FKS-KS', 'short' => 'Netzwerk für Schule und Gemeinde in Kassel.', 'street' => 'Königsstraße', 'house_number' => '46', 'zip' => '34117', 'city' => 'Kassel', 'lat' => 51.31545, 'lng' => 9.49712, 'phone' => '+4956133503'],
            ['name' => 'Religionspädagogischer Arbeitskreis Darmstadt e.V.', 'short_name' => 'RPA-DA', 'short' => 'Regionaler Arbeitskreis in Darmstadt.', 'street' => 'Luisenplatz', 'house_number' => '5', 'zip' => '64283', 'city' => 'Darmstadt', 'lat' => 49.87283, 'lng' => 8.65118, 'phone' => '+49615133504'],
            ['name' => 'Interkulturelles Bildungsnetz Offenbach e.V.', 'short_name' => 'IBN-OF', 'short' => 'Verein für interkulturelle Bildung in Offenbach.', 'street' => 'Frankfurter Straße', 'house_number' => '20', 'zip' => '63065', 'city' => 'Offenbach am Main', 'lat' => 50.10552, 'lng' => 8.76105, 'phone' => '+4969833505'],
            ['name' => 'Verein Lernorte Mittelhessen e.V.', 'short_name' => 'VLM-GI', 'short' => 'Träger von Lernorten in Gießen.', 'street' => 'Seltersweg', 'house_number' => '31', 'zip' => '35390', 'city' => 'Gießen', 'lat' => 50.58398, 'lng' => 8.67839, 'phone' => '+4964133506'],
            ['name' => 'Marburger Forum Religionspädagogik e.V.', 'short_name' => 'MFR-MR', 'short' => 'Fachforum an der Universitätsstadt Marburg.', 'street' => 'Gutenbergstraße', 'house_number' => '18', 'zip' => '35037', 'city' => 'Marburg', 'lat' => 50.80861, 'lng' => 8.77361, 'phone' => '+49642133507'],
            ['name' => 'Bildungsinitiative Osthessen e.V.', 'short_name' => 'BIO-FD', 'short' => 'Regionale Bildungsinitiative in Fulda.', 'street' => 'Friedrichstraße', 'house_number' => '22', 'zip' => '36037', 'city' => 'Fulda', 'lat' => 50.55359, 'lng' => 9.67518, 'phone' => '+4966133508'],
            ['name' => 'Verein Schule und Gemeinde Hanau e.V.', 'short_name' => 'VSG-HU', 'short' => 'Kooperationsverein in Hanau.', 'street' => 'Nürnberger Straße', 'house_number' => '15', 'zip' => '63450', 'city' => 'Hanau', 'lat' => 50.13333, 'lng' => 8.91667, 'phone' => '+49618133509'],
            ['name' => 'Mainspitze Bildungsforum e.V.', 'short_name' => 'MBF-RÜS', 'short' => 'Bildungsforum in Rüsselsheim am Main.', 'street' => 'Mainzer Straße', 'house_number' => '9', 'zip' => '65428', 'city' => 'Rüsselsheim am Main', 'lat' => 49.99472, 'lng' => 8.41194, 'phone' => '+49614233510'],
            ['name' => 'Lahn-Dill Lernwerkstatt e.V.', 'short_name' => 'LDL-WZ', 'short' => 'Lernwerkstatt in Wetzlar.', 'street' => 'Bahnhofstraße', 'house_number' => '4', 'zip' => '35578', 'city' => 'Wetzlar', 'lat' => 50.56117, 'lng' => 8.50494, 'phone' => '+49644133511'],
            ['name' => 'Hochtaunus Bildungsverein e.V.', 'short_name' => 'HBV-HG', 'short' => 'Bildungsverein in Bad Homburg.', 'street' => 'Louisenstraße', 'house_number' => '63', 'zip' => '61348', 'city' => 'Bad Homburg vor der Höhe', 'lat' => 50.22683, 'lng' => 8.61818, 'phone' => '+49617233512'],
            ['name' => 'Verein Religionspädagogik Limburg e.V.', 'short_name' => 'VRL-LM', 'short' => 'Fachverein im Limburger Domviertel.', 'street' => 'Barfüßerstraße', 'house_number' => '6', 'zip' => '65549', 'city' => 'Limburg an der Lahn', 'lat' => 50.38806, 'lng' => 8.06472, 'phone' => '+49643133513'],
            ['name' => 'Wetterau Schulforum e.V.', 'short_name' => 'WSF-FB', 'short' => 'Schulforum in Friedberg (Hessen).', 'street' => 'Kaiserstraße', 'house_number' => '14', 'zip' => '61169', 'city' => 'Friedberg (Hessen)', 'lat' => 50.33727, 'lng' => 8.75569, 'phone' => '+49603133514'],
            ['name' => 'Hersfeld-Rotenburg Bildungswerk e.V.', 'short_name' => 'HRB-HEF', 'short' => 'Bildungswerk in Bad Hersfeld.', 'street' => 'Klausstraße', 'house_number' => '21', 'zip' => '36251', 'city' => 'Bad Hersfeld', 'lat' => 50.86800, 'lng' => 9.70717, 'phone' => '+49662133515'],
            ['name' => 'Kinzigtal Bildungsinitiative e.V.', 'short_name' => 'KBI-GN', 'short' => 'Bildungsinitiative in Gelnhausen.', 'street' => 'Langgasse', 'house_number' => '8', 'zip' => '63571', 'city' => 'Gelnhausen', 'lat' => 50.20255, 'lng' => 9.19050, 'phone' => '+49605133516'],
            ['name' => 'Werra-Meißner Lernnetz e.V.', 'short_name' => 'WML-ESW', 'short' => 'Regionales Lernnetz in Eschwege.', 'street' => 'Marktstraße', 'house_number' => '37', 'zip' => '37269', 'city' => 'Eschwege', 'lat' => 51.18800, 'lng' => 10.05300, 'phone' => '+49565133517'],
            ['name' => 'Bergstraße Religionspädagogik e.V.', 'short_name' => 'BRP-HP', 'short' => 'Fachverein in Bensheim an der Bergstraße.', 'street' => 'Hauptstraße', 'house_number' => '39', 'zip' => '64625', 'city' => 'Bensheim', 'lat' => 49.68028, 'lng' => 8.62278, 'phone' => '+49625133518'],
            ['name' => 'Kreis Groß-Gerau Schulverein e.V.', 'short_name' => 'KGG-GG', 'short' => 'Schulverein im Kreis Groß-Gerau.', 'street' => 'Darmstädter Straße', 'house_number' => '12', 'zip' => '64521', 'city' => 'Groß-Gerau', 'lat' => 49.92139, 'lng' => 8.48250, 'phone' => '+49615233519'],
            ['name' => 'Waldecker Bildungsverein e.V.', 'short_name' => 'WBV-KB', 'short' => 'Bildungsverein in Korbach.', 'street' => 'Stechbahn', 'house_number' => '1', 'zip' => '34497', 'city' => 'Korbach', 'lat' => 51.27528, 'lng' => 8.87306, 'phone' => '+49563133520'],
        ];
    }

    /**
     * @return list<array{
     *     salutation: string,
     *     first_name: string,
     *     last_name: string,
     *     title: string,
     *     position: string,
     *     organisations: list<string>,
     *     street: string,
     *     house_number: string,
     *     zip: string,
     *     city: string,
     *     region: string,
     *     lat: float,
     *     lng: float,
     *     phone: string,
     *     short: string
     * }>
     */
    private static function persons(): array
    {
        return [
            ['salutation' => 'ms', 'first_name' => 'Anna', 'last_name' => 'Schneider', 'title' => 'Dr.', 'position' => 'Geschäftsführung', 'organisations' => ['NEWORGCOMPFFM', 'NEWORGDEPTFFM01'], 'street' => 'Eimsbütteler Chaussee', 'house_number' => '64', 'zip' => '20259', 'city' => 'Hamburg', 'region' => 'Hamburg', 'lat' => 53.57532, 'lng' => 9.95900, 'phone' => '+49401700101', 'short' => 'Leitung des Bildungsforum Rhein-Main.'],
            ['salutation' => 'mr', 'first_name' => 'Markus', 'last_name' => 'Weber', 'title' => '', 'position' => 'Leitung Finanzen', 'organisations' => ['NEWORGCOMPFFM', 'NEWORGDEPTFFM02'], 'street' => 'Tegernseer Landstraße', 'house_number' => '88', 'zip' => '81541', 'city' => 'München', 'region' => 'Bayern', 'lat' => 48.11690, 'lng' => 11.58200, 'phone' => '+49891700102', 'short' => 'Verantwortet Finanzen und Controlling in Frankfurt.'],
            ['salutation' => 'ms', 'first_name' => 'Julia', 'last_name' => 'Hoffmann', 'title' => '', 'position' => 'Geschäftsführung', 'organisations' => ['NEWORGCOMPBER', 'NEWORGDEPTBER01'], 'street' => 'Aachener Straße', 'house_number' => '220', 'zip' => '50931', 'city' => 'Köln', 'region' => 'Nordrhein-Westfalen', 'lat' => 50.93600, 'lng' => 6.92800, 'phone' => '+492211700103', 'short' => 'Leitung der Hauptstadt Akademie.'],
            ['salutation' => 'mr', 'first_name' => 'Thomas', 'last_name' => 'Becker', 'title' => 'Prof. Dr.', 'position' => 'Wissenschaftliche Leitung', 'organisations' => ['NEWORGCOMPBER', 'NEWORGDEPTBER04'], 'street' => 'Königstraße', 'house_number' => '44', 'zip' => '70173', 'city' => 'Stuttgart', 'region' => 'Baden-Württemberg', 'lat' => 48.78200, 'lng' => 9.17700, 'phone' => '+497111700104', 'short' => 'Wissenschaftliche Leitung Bildung Hauptstadt.'],
            ['salutation' => 'ms', 'first_name' => 'Laura', 'last_name' => 'Schäfer', 'title' => '', 'position' => 'Referentin Kommunikation', 'organisations' => ['NEWORGCOMPFFM', 'NEWORGDEPTFFM03'], 'street' => 'Karl-Liebknecht-Straße', 'house_number' => '12', 'zip' => '04107', 'city' => 'Leipzig', 'region' => 'Sachsen', 'lat' => 51.34000, 'lng' => 12.38100, 'phone' => '+493411700105', 'short' => 'Presse- und Öffentlichkeitsarbeit Rhein-Main.'],
            ['salutation' => 'mr', 'first_name' => 'Michael', 'last_name' => 'Wagner', 'title' => '', 'position' => 'Projektleitung', 'organisations' => ['NEWORGCOMPFFM', 'NEWORGUNITFFM01'], 'street' => 'Wiener Straße', 'house_number' => '41', 'zip' => '01219', 'city' => 'Dresden', 'region' => 'Sachsen', 'lat' => 51.03000, 'lng' => 13.75500, 'phone' => '+493511700106', 'short' => 'Koordiniert Projekte im Projektbüro Rhein-Main.'],
            ['salutation' => 'ms', 'first_name' => 'Sophie', 'last_name' => 'Bauer', 'title' => '', 'position' => 'Referentin Campus', 'organisations' => ['NEWORGCOMPBER', 'NEWORGUNITBER01'], 'street' => 'Lister Meile', 'house_number' => '4', 'zip' => '30161', 'city' => 'Hannover', 'region' => 'Niedersachsen', 'lat' => 52.37900, 'lng' => 9.74700, 'phone' => '+495111700107', 'short' => 'Campusarbeit der Hauptstadt Akademie.'],
            ['salutation' => 'mr', 'first_name' => 'Daniel', 'last_name' => 'Richter', 'title' => '', 'position' => 'Transfermanager', 'organisations' => ['NEWORGCOMPBER', 'NEWORGUNITBER02'], 'street' => 'Ostertorsteinweg', 'house_number' => '22', 'zip' => '28203', 'city' => 'Bremen', 'region' => 'Bremen', 'lat' => 53.07200, 'lng' => 8.81800, 'phone' => '+494211700108', 'short' => 'Transfer zwischen Bildung und Politik in Berlin.'],
            ['salutation' => 'ms', 'first_name' => 'Katharina', 'last_name' => 'Klein', 'title' => 'Dr.', 'position' => 'Institutsleitung', 'organisations' => ['NEWORGINST01'], 'street' => 'Königstraße', 'house_number' => '25', 'zip' => '90402', 'city' => 'Nürnberg', 'region' => 'Bayern', 'lat' => 49.45000, 'lng' => 11.07700, 'phone' => '+499111700109', 'short' => 'Leitung Bildungszentrum Nord in Hamburg.'],
            ['salutation' => 'mr', 'first_name' => 'Florian', 'last_name' => 'Wolf', 'title' => '', 'position' => 'Studienleitung', 'organisations' => ['NEWORGINST02'], 'street' => 'Rheinlanddamm', 'house_number' => '201', 'zip' => '44139', 'city' => 'Dortmund', 'region' => 'Nordrhein-Westfalen', 'lat' => 51.50400, 'lng' => 7.45100, 'phone' => '+492311700110', 'short' => 'Studienleitung am RPI Bayern.'],
            ['salutation' => 'ms', 'first_name' => 'Nina', 'last_name' => 'Neumann', 'title' => '', 'position' => 'Pädagogische Leitung', 'organisations' => ['NEWORGINST03'], 'street' => 'Rüttenscheider Straße', 'house_number' => '80', 'zip' => '45130', 'city' => 'Essen', 'region' => 'Nordrhein-Westfalen', 'lat' => 51.43800, 'lng' => 7.00400, 'phone' => '+492011700111', 'short' => 'Pädagogische Leitung Forum Lernen Rheinland.'],
            ['salutation' => 'mr', 'first_name' => 'Stefan', 'last_name' => 'Schwarz', 'title' => '', 'position' => 'Fortbildungsreferent', 'organisations' => ['NEWORGINST04'], 'street' => 'Kaiser-Joseph-Straße', 'house_number' => '260', 'zip' => '79098', 'city' => 'Freiburg im Breisgau', 'region' => 'Baden-Württemberg', 'lat' => 47.99400, 'lng' => 7.84800, 'phone' => '+497611700112', 'short' => 'Fortbildung am Studienhaus Baden-Württemberg.'],
            ['salutation' => 'ms', 'first_name' => 'Lisa', 'last_name' => 'Krüger', 'title' => '', 'position' => 'Medienpädagogin', 'organisations' => ['NEWORGINST05'], 'street' => 'Kröpeliner Straße', 'house_number' => '12', 'zip' => '18055', 'city' => 'Rostock', 'region' => 'Mecklenburg-Vorpommern', 'lat' => 54.09000, 'lng' => 12.13500, 'phone' => '+493811700113', 'short' => 'Medienpädagogik in der Medienwerkstatt Sachsen.'],
            ['salutation' => 'diverse', 'first_name' => 'Jan', 'last_name' => 'Hartmann', 'title' => '', 'position' => 'Campuskoordination', 'organisations' => ['NEWORGINST06'], 'street' => 'Bahnhofstraße', 'house_number' => '28', 'zip' => '66111', 'city' => 'Saarbrücken', 'region' => 'Saarland', 'lat' => 49.24100, 'lng' => 6.99000, 'phone' => '+496811700114', 'short' => 'Koordination am Campus Bildung Elbe.'],
            ['salutation' => 'ms', 'first_name' => 'Hannah', 'last_name' => 'Zimmermann', 'title' => '', 'position' => 'Vereinsvorsitz', 'organisations' => ['NEWORGASSOC01', 'NEWORGINST07'], 'street' => 'Breiter Weg', 'house_number' => '110', 'zip' => '39104', 'city' => 'Magdeburg', 'region' => 'Sachsen-Anhalt', 'lat' => 52.12700, 'lng' => 11.63500, 'phone' => '+493911700115', 'short' => 'Vorsitz im Frankfurter Fachverein, zusätzlich Haus der Religionen.'],
            ['salutation' => 'mr', 'first_name' => 'Patrick', 'last_name' => 'Braun', 'title' => '', 'position' => 'Geschäftsführung Verein', 'organisations' => ['NEWORGASSOC02'], 'street' => 'Holtenauer Straße', 'house_number' => '103', 'zip' => '24105', 'city' => 'Kiel', 'region' => 'Schleswig-Holstein', 'lat' => 54.33900, 'lng' => 10.13500, 'phone' => '+494311700116', 'short' => 'Geschäftsführung des Hessischen Bildungsvereins Wiesbaden.'],
            ['salutation' => 'ms', 'first_name' => 'Emma', 'last_name' => 'Schuster', 'title' => '', 'position' => 'Schriftführung', 'organisations' => ['NEWORGASSOC03'], 'street' => 'Anger', 'house_number' => '37', 'zip' => '99084', 'city' => 'Erfurt', 'region' => 'Thüringen', 'lat' => 50.97600, 'lng' => 11.03400, 'phone' => '+493611700117', 'short' => 'Schriftführung Forum Kirche und Schule Nordhessen.'],
            ['salutation' => 'mr', 'first_name' => 'Lukas', 'last_name' => 'Schmitt', 'title' => '', 'position' => 'Vorstand', 'organisations' => ['NEWORGASSOC04'], 'street' => 'Brandenburger Straße', 'house_number' => '20', 'zip' => '14467', 'city' => 'Potsdam', 'region' => 'Brandenburg', 'lat' => 52.39886, 'lng' => 13.06566, 'phone' => '+493311700118', 'short' => 'Vorstand des Darmstädter Arbeitskreises.'],
            ['salutation' => 'ms', 'first_name' => 'Clara', 'last_name' => 'Lange', 'title' => '', 'position' => 'Projektkoordination', 'organisations' => ['NEWORGASSOC05'], 'street' => 'Planken', 'house_number' => '15', 'zip' => '68161', 'city' => 'Mannheim', 'region' => 'Baden-Württemberg', 'lat' => 49.48700, 'lng' => 8.46600, 'phone' => '+496211700119', 'short' => 'Projektkoordination im Offenbacher Bildungsnetz.'],
            ['salutation' => 'mr', 'first_name' => 'Tim', 'last_name' => 'Lehmann', 'title' => '', 'position' => 'Beisitz', 'organisations' => ['NEWORGASSOC06'], 'street' => 'Pontstraße', 'house_number' => '11', 'zip' => '52062', 'city' => 'Aachen', 'region' => 'Nordrhein-Westfalen', 'lat' => 50.77800, 'lng' => 6.08300, 'phone' => '+492411700120', 'short' => 'Beisitz im Verein Lernorte Mittelhessen.'],
        ];
    }

    private static function slug(string $value): string
    {
        $value = strtr($value, [
            'ä' => 'ae',
            'ö' => 'oe',
            'ü' => 'ue',
            'Ä' => 'ae',
            'Ö' => 'oe',
            'Ü' => 'ue',
            'ß' => 'ss',
        ]);
        $value = strtolower($value);
        $value = (string)preg_replace('/[^a-z0-9]+/', '-', $value);

        return trim($value, '-');
    }
}
