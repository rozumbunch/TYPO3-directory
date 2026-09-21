<?php

declare(strict_types=1);

namespace Rozumbunch\Directory\Domain\Model;

use TYPO3\CMS\Extbase\Attribute\ORM\Lazy;
use TYPO3\CMS\Extbase\Domain\Model\FileReference;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

class Person extends AbstractEntity
{
    protected string $identifier = '';
    protected string $salutation = '';
    protected string $firstName = '';
    protected string $lastName = '';
    protected string $title = '';
    protected string $displayName = '';
    protected string $position = '';
    protected string $shortDescription = '';
    protected string $description = '';
    protected string $email = '';
    protected string $phone = '';
    protected string $mobile = '';
    protected string $website = '';
    protected string $socialProfile = '';
    protected ?FileReference $image = null;

    /**
     * @var ObjectStorage<FileReference>
     */
    #[Lazy]
    protected ObjectStorage $media;

    /**
     * @var ObjectStorage<Organisation>
     */
    #[Lazy]
    protected ObjectStorage $organisations;

    /**
     * @var ObjectStorage<Location>
     */
    #[Lazy]
    protected ObjectStorage $locations;

    protected string $slug = '';
    protected string $seoTitle = '';
    protected string $seoDescription = '';
    protected int $sorting = 0;

    public function __construct()
    {
        $this->media = new ObjectStorage();
        $this->organisations = new ObjectStorage();
        $this->locations = new ObjectStorage();
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function setIdentifier(string $identifier): void
    {
        $this->identifier = $identifier;
    }

    public function getSalutation(): string
    {
        return $this->salutation;
    }

    public function setSalutation(string $salutation): void
    {
        $this->salutation = $salutation;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): void
    {
        $this->firstName = $firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): void
    {
        $this->lastName = $lastName;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getDisplayName(): string
    {
        return $this->displayName;
    }

    public function setDisplayName(string $displayName): void
    {
        $this->displayName = $displayName;
    }

    public function getFullName(): string
    {
        $displayName = trim($this->displayName);
        if ($displayName !== '') {
            return $displayName;
        }

        return trim(implode(' ', array_filter([
            trim($this->title),
            trim($this->firstName),
            trim($this->lastName),
        ])));
    }

    public function getPosition(): string
    {
        return $this->position;
    }

    public function setPosition(string $position): void
    {
        $this->position = $position;
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

    public function getMobile(): string
    {
        return $this->mobile;
    }

    public function setMobile(string $mobile): void
    {
        $this->mobile = $mobile;
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

    public function getImage(): ?FileReference
    {
        return $this->image;
    }

    public function setImage(?FileReference $image): void
    {
        $this->image = $image;
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
     * @return ObjectStorage<Organisation>
     */
    public function getOrganisations(): ObjectStorage
    {
        return $this->organisations;
    }

    /**
     * @param ObjectStorage<Organisation> $organisations
     */
    public function setOrganisations(ObjectStorage $organisations): void
    {
        $this->organisations = $organisations;
    }

    public function addOrganisation(Organisation $organisation): void
    {
        $this->organisations->attach($organisation);
    }

    public function removeOrganisation(Organisation $organisation): void
    {
        $this->organisations->detach($organisation);
    }

    public function getOrganisation(): ?Organisation
    {
        foreach ($this->organisations as $organisation) {
            return $organisation;
        }

        return null;
    }

    public function setOrganisation(?Organisation $organisation): void
    {
        $this->organisations = new ObjectStorage();
        if ($organisation instanceof Organisation) {
            $this->organisations->attach($organisation);
        }
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

    public function getPrimaryLocation(): ?Location
    {
        foreach ($this->locations as $location) {
            return $location;
        }

        return null;
    }

    public function getInitials(): string
    {
        $initials = '';
        foreach ([trim($this->firstName), trim($this->lastName)] as $part) {
            if ($part === '') {
                continue;
            }
            $initials .= mb_strtoupper(mb_substr($part, 0, 1));
        }

        return $initials;
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
