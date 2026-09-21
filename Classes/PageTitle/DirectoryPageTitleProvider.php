<?php

declare(strict_types=1);

namespace Rozumbunch\Directory\PageTitle;

use TYPO3\CMS\Core\PageTitle\AbstractPageTitleProvider;

final class DirectoryPageTitleProvider extends AbstractPageTitleProvider
{
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }
}
