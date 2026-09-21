<?php

declare(strict_types=1);

namespace Rozumbunch\Directory\Domain\Model;

use TYPO3\CMS\Extbase\Attribute\ORM\Lazy;
use TYPO3\CMS\Extbase\Domain\Model\FileReference;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

class Organisation extends AbstractEntity
{
    public const TYPE_COMPANY = 'company';
    public const TYPE_AUTHORITY = 'authority';
    public const TYPE_ASSOCIATION = 'association';
    public const TYPE_DEPARTMENT = 'department';
    public const TYPE_ORGUNIT = 'orgunit';
    public const TYPE_INSTITUTION = 'institution';
    public const TYPE_OTHER = 'other';

    public const ROOT_TYPES = [
        self::TYPE_COMPANY,
        self::TYPE_AUTHORITY,
        self::TYPE_ASSOCIATION,
        self::TYPE_INSTITUTION,
        self::TYPE_OTHER,
    ];

    public const UNIT_TYPES = [
        self::TYPE_DEPARTMENT,
        self::TYPE_ORGUNIT,
    ];

    public const ALL_TYPES = [
        self::TYPE_COMPANY,
        self::TYPE_AUTHORITY,
        self::TYPE_ASSOCIATION,
        self::TYPE_DEPARTMENT,
        self::TYPE_ORGUNIT,
        self::TYPE_INSTITUTION,
        self::TYPE_OTHER,
    ];

    public const DETAIL_SECTIONS = [
        'structure',
        'locations',
        'contacts',
    ];

    protected string $identifier = '';
    protected string $name = '';
    protected string $shortName = '';
    protected string $organisationType = '';
    protected string $shortDescription = '';
    protected string $description = '';
    protected string $email = '';
    protected string $phone = '';
    protected string $website = '';
    protected string $socialProfile = '';
    protected ?FileReference $logo = null;

    /**
     * @var ObjectStorage<FileReference>
     */
    #[Lazy]
    protected ObjectStorage $media;

    /**
     * @var ObjectStorage<Location>
     */
    #[Lazy]
    protected ObjectStorage $locations;

    /**
     * @var ObjectStorage<Person>
     */
    #[Lazy]
    protected ObjectStorage $contactPersons;

    /**
     * @var ObjectStorage<Organisation>
     */
    #[Lazy]
    protected ObjectStorage $businessUnits;

    protected string $slug = '';
    protected string $seoTitle = '';
    protected string $seoDescription = '';
    protected int $sorting = 0;

    public function __construct()
    {
        $this->media = new ObjectStorage();
        $this->locations = new ObjectStorage();
        $this->contactPersons = new ObjectStorage();
        $this->businessUnits = new ObjectStorage();
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function setIdentifier(string $identifier): void
    {
        $this->identifier = $identifier;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getShortName(): string
    {
        return $this->shortName;
    }

    public function setShortName(string $shortName): void
    {
        $this->shortName = $shortName;
    }

    public function getOrganisationType(): string
    {
        return $this->organisationType;
    }

    public function isStructuralUnit(): bool
    {
        return in_array($this->organisationType, self::UNIT_TYPES, true);
    }

    public function getInitials(): string
    {
        $shortName = trim($this->shortName);
        if ($shortName !== '') {
            return mb_strtoupper(mb_substr($shortName, 0, 3));
        }

        $name = trim($this->name);
        if ($name === '') {
            return '';
        }

        return mb_strtoupper(mb_substr($name, 0, 1));
    }

    public function setOrganisationType(string $organisationType): void
    {
        $this->organisationType = $organisationType;
    }

    public function getShortDescription(): string
    {
        return $this->shortDescription;
    }

    public function setShortDescription(string $shortDescription): void
    {
        $this->shortDescription = $shortDescription;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): void
    {
        $this->phone = $phone;
    }

    public function getWebsite(): string
    {
        return $this->website;
    }

    public function setWebsite(string $website): void
    {
        $this->website = $website;
    }

    public function getSocialProfile(): string
    {
        return $this->socialProfile;
    }

    public function setSocialProfile(string $socialProfile): void
    {
        $this->socialProfile = $socialProfile;
    }

    public function getLogo(): ?FileReference
    {
        return $this->logo;
    }

    public function setLogo(?FileReference $logo): void
    {
        $this->logo = $logo;
    }

    /**
     * @return ObjectStorage<FileReference>
     */
    public function getMedia(): ObjectStorage
    {
        return $this->media;
    }

    /**
     * @param ObjectStorage<FileReference> $media
     */
    public function setMedia(ObjectStorage $media): void
    {
        $this->media = $media;
    }

    /**
     * @return ObjectStorage<Location>
     */
    public function getLocations(): ObjectStorage
    {
        return $this->locations;
    }

    /**
     * @param ObjectStorage<Location> $locations
     */
    public function setLocations(ObjectStorage $locations): void
    {
        $this->locations = $locations;
    }

    public function addLocation(Location $location): void
    {
        $this->locations->attach($location);
    }

    public function removeLocation(Location $location): void
    {
        $this->locations->detach($location);
    }

    public function getPrimaryLocation(): ?Location
    {
        $first = null;
        foreach ($this->locations as $location) {
            $first ??= $location;
            if ($location->hasCoordinates()) {
                return $location;
            }
        }

        return $first;
    }

    /**
     * @return ObjectStorage<Person>
     */
    public function getContactPersons(): ObjectStorage
    {
        return $this->contactPersons;
    }

    /**
     * @param ObjectStorage<Person> $contactPersons
     */
    public function setContactPersons(ObjectStorage $contactPersons): void
    {
        $this->contactPersons = $contactPersons;
    }

    public function addContactPerson(Person $person): void
    {
        $this->contactPersons->attach($person);
        if ($person->getOrganisation() === null) {
            $person->addOrganisation($this);
        }
    }

    public function removeContactPerson(Person $person): void
    {
        $this->contactPersons->detach($person);
    }

    /**
     * @return ObjectStorage<Organisation>
     */
    public function getBusinessUnits(): ObjectStorage
    {
        return $this->businessUnits;
    }

    /**
     * @param ObjectStorage<Organisation> $businessUnits
     */
    public function setBusinessUnits(ObjectStorage $businessUnits): void
    {
        $this->businessUnits = $businessUnits;
    }

    public function addBusinessUnit(Organisation $organisation): void
    {
        if ($organisation === $this) {
            return;
        }
        $this->businessUnits->attach($organisation);
    }

    public function removeBusinessUnit(Organisation $organisation): void
    {
        $this->businessUnits->detach($organisation);
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): void
    {
        $this->slug = $slug;
    }

    public function getSeoTitle(): string
    {
        return $this->seoTitle;
    }

    public function setSeoTitle(string $seoTitle): void
    {
        $this->seoTitle = $seoTitle;
    }

    public function getSeoDescription(): string
    {
        return $this->seoDescription;
    }

    public function setSeoDescription(string $seoDescription): void
    {
        $this->seoDescription = $seoDescription;
    }

    public function getSorting(): int
    {
        return $this->sorting;
    }

    public function setSorting(int $sorting): void
    {
        $this->sorting = $sorting;
    }
}
