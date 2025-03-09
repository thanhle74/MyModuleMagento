<?php
declare(strict_types=1);

namespace Forix\CatalogImportExport\Plugin;

class CorrectFileName
{
    /**
     * Summary of aroundCorrectFileNameCase
     * @param \Magento\Framework\File\Uploader $subject
     * @param callable $proceed
     * @param string $fileName
     * @return string
     */
    public function aroundCorrectFileNameCase(
        \Magento\Framework\File\Uploader $subject,
        callable $proceed,
        string $fileName
    ): string 
    {
        if (strpos($fileName, 'youtubeImage') !== false && strpos($fileName, '___') !== false) {
            return $fileName;
        }

        return $proceed($fileName);
    }
}
