<?php

declare(strict_types=1);

namespace Rozumbunch\Directory\Service;

use Rozumbunch\Directory\Domain\Model\Organisation;
use Rozumbunch\Directory\Domain\Model\Person;
use Rozumbunch\Directory\PageTitle\DirectoryPageTitleProvider;
use TYPO3\CMS\Core\MetaTag\MetaTagManagerRegistry;

final class DirectorySeoWriter
{
    public function __construct(
        private readonly DirectoryPageTitleProvider $titleProvider,
        private readonly MetaTagManagerRegistry $metaTagManagerRegistry,
    ) {}

    public function apply(Person|Organisation $record): void
    {
        $title = $record->getSeoTitle();
        if ($title === '') {
            $title = $record instanceof Person ? $record->getFullName() : $record->getName();
        }
        if ($title !== '') {
            $this->titleProvider->setTitle($title);
        }

        $description = $record->getSeoDescription() !== ''
            ? $record->getSeoDescription()
            : $record->getShortDescription();
        if ($description !== '') {
            $this->metaTagManagerRegistry
                ->getManagerForProperty('description')
                ->addProperty('description', $description);
        }
    }
}
