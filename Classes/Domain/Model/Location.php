<?php

declare(strict_types=1);

namespace Rozumbunch\Directory\Domain\Model;

use TYPO3\CMS\Extbase\Attribute\ORM\Lazy;
use TYPO3\CMS\Extbase\Domain\Model\FileReference;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

class Location extends AbstractEntity
{
    public const TYPE_PRIVATE = 'private';
    public const TYPE_COMPANY = 'company';
    public const TYPE_POI = 'poi';
    public const TYPE_OTHER = 'other';

    public const ALL_TYPES = [
        self::TYPE_PRIVATE,
        self::TYPE_COMPANY,
        self::TYPE_POI,
        self::TYPE_OTHER,
    ];

    public const PUBLIC_TYPES = [
        self::TYPE_COMPANY,
        self::TYPE_POI,
        self::TYPE_OTHER,
    ];

    protected string $identifier = '';
    protected string $name = '';
    protected string $locationType = self::TYPE_PRIVATE;
    protected string $street = '';
    protected string $houseNumber = '';
    protected string $addressAddition = '';
    protected string $postOfficeBox = '';
    protected string $zip = '';
    protected string $city = '';
    protected string $district = '';
    protected string $region = '';
    protected string $country = 'DE';
    protected string $shortDescription = '';
    protected string $description = '';

    protected string $email = '';
    protected string $phone = '';
    protected string $website = '';
    protected string $openingHours = '';
    protected string $specialHoursNote = '';
    protected string $directions = '';
    protected string $parking = '';
    protected string $accessibility = '';
    protected ?float $latitude = null;
    protected ?float $longitude = null;

    /**
     * @var ObjectStorage<FileReference>
     */
    #[Lazy]
    protected ObjectStorage $media;

    protected string $slug = '';
    protected string $seoTitle = '';
    protected string $seoDescription = '';
    protected int $sorting = 0;

    public function __construct()
    {
        $this->media = new ObjectStorage();
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

    public function getLocationType(): string
    {
        return $this->locationType;
    }

    public function setLocationType(string $locationType): void
    {
        $this->locationType = $locationType;
    }

    public function isPrivate(): bool
    {
        return $this->locationType === self::TYPE_PRIVATE;
    }

    public function isCompany(): bool
    {
        return $this->locationType === self::TYPE_COMPANY;
    }

    public function isPoi(): bool
    {
        return $this->locationType === self::TYPE_POI;
    }

    public function getStreet(): string
    {
        return $this->street;
    }

    public function setStreet(string $street): void
    {
        $this->street = $street;
    }

    public function getHouseNumber(): string
    {
        return $this->houseNumber;
    }

    public function setHouseNumber(string $houseNumber): void
    {
        $this->houseNumber = $houseNumber;
    }

    public function getAddressAddition(): string
    {
        return $this->addressAddition;
    }

    public function setAddressAddition(string $addressAddition): void
    {
        $this->addressAddition = $addressAddition;
    }

    public function getPostOfficeBox(): string
    {
        return $this->postOfficeBox;
    }

    public function setPostOfficeBox(string $postOfficeBox): void
    {
        $this->postOfficeBox = $postOfficeBox;
    }

    public function getZip(): string
    {
        return $this->zip;
    }

    public function setZip(string $zip): void
    {
        $this->zip = $zip;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function setCity(string $city): void
    {
        $this->city = $city;
    }

    public function getDistrict(): string
    {
        return $this->district;
    }

    public function setDistrict(string $district): void
    {
        $this->district = $district;
    }

    public function getRegion(): string
    {
        return $this->region;
    }

    public function setRegion(string $region): void
    {
        $this->region = $region;
    }

    public function getCountry(): string
    {
        return $this->country;
    }

    public function setCountry(string $country): void
    {
        $this->country = $country;
    }

    public function getFormattedAddress(): string
    {
        $street = trim($this->street . ' ' . $this->houseNumber);
        $city = trim($this->zip . ' ' . $this->city);

        return implode(', ', array_filter([$street, $this->addressAddition, $city]));
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

    public function getOpeningHours(): string
    {
        return $this->openingHours;
    }

    public function setOpeningHours(string $openingHours): void
    {
        $this->openingHours = $openingHours;
    }

    public function getSpecialHoursNote(): string
    {
        return $this->specialHoursNote;
    }

    public function setSpecialHoursNote(string $specialHoursNote): void
    {
        $this->specialHoursNote = $specialHoursNote;
    }

    public function getDirections(): string
    {
        return $this->directions;
    }

    public function setDirections(string $directions): void
    {
        $this->directions = $directions;
    }

    public function getParking(): string
    {
        return $this->parking;
    }

    public function setParking(string $parking): void
    {
        $this->parking = $parking;
    }

    public function getAccessibility(): string
    {
        return $this->accessibility;
    }

    public function setAccessibility(string $accessibility): void
    {
        $this->accessibility = $accessibility;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(?float $latitude): void
    {
        $this->latitude = $latitude;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(?float $longitude): void
    {
        $this->longitude = $longitude;
    }

    public function hasCoordinates(): bool
    {
        return $this->latitude !== null && $this->longitude !== null;
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
