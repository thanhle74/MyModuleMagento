<?php
declare(strict_types=1);
namespace Annam\HealthLab\Model;

use Annam\HealthLab\Api\ProductHelperInterface;
use Magento\Catalog\Helper\Image;

class ProductHelper implements ProductHelperInterface
{
    /**
     * @var Image
     */
    protected Image $imageHelper;

    /**
     * @param Image $imageHelper
     */
    public function __construct
    (
        Image $imageHelper
    )
    {
        $this->imageHelper = $imageHelper;
    }

    /**
     * @param $product
     * @return string
     */
    public function getUrlImage($product): string
    {
        $image = '';
        if($product->getImage())
        {
            $image = $this->imageHelper->init($product, 'product_page_image_large')->setImageFile($product->getImage())->getUrl();
        }

        return $image;
    }

}
