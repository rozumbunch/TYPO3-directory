<?php

declare(strict_types=1);

namespace Rozumbunch\Directory\Controller;

use TYPO3\CMS\Core\View\ViewInterface;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

trait ContentObjectDataViewTrait
{
    public function initializeView(ViewInterface $view): void
    {
        $view->assign('data', $this->getContentObjectData());
    }

    /**
     * @return array<string, mixed>
     */
    protected function getContentObjectData(): array
    {
        $contentObject = $this->request->getAttribute('currentContentObject');
        if (!$contentObject instanceof ContentObjectRenderer) {
            return [];
        }

        return $contentObject->data;
    }
}
