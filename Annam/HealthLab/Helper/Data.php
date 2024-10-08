<?php
declare(strict_types=1);
namespace Annam\HealthLab\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Catalog\Api\CategoryRepositoryInterface;

class Data extends AbstractHelper
{
    //General
    const GENERAL_HEADER_LOGO = 'healthlab_general/header/logo';
    const GENERAL_FOOTER_QR_IMAGE = 'healthlab_general/footer/image';
    const GENERAL_FOOTER_QR_TEXT = 'healthlab_general/footer/text';
    const GENERAL_FOOTER_EMAIL = 'healthlab_general/footer/email';
    const GENERAL_FOOTER_PHONE = 'healthlab_general/footer/phone';
    const GENERAL_NO_RESULT = 'healthlab_general/no_result/text';
    const GENERAL_HEALTHLAB_BRAND = 'healthlab_general/healthlab_brand/brand';

    //Product Catalog
    const PRODUCT_CATALOG_ATTRIBUTE = 'healthlab_product_catalog/attribute/search_by_keyword';
    const PRODUCT_CATALOG_CONTENT_TITLE_SM = 'healthlab_product_catalog/content/title_sm';
    const PRODUCT_CATALOG_CONTENT_TITLE_LARGE = 'healthlab_product_catalog/content/title_lg';
    const PRODUCT_CATALOG_CONTENT_DESCRIPTION = 'healthlab_product_catalog/content/description';
    const PRODUCT_CATALOG_CONTENT_PLACEHOLDER = 'healthlab_product_catalog/content/placeholder';
    const PRODUCT_CATALOG_CONTENT_INPUT_EXAMPLE = 'healthlab_product_catalog/content/input_example';
    const PRODUCT_CATALOG_CONTENT_IMAGE = 'healthlab_product_catalog/content/image';
    const PRODUCT_CATALOG_CONTENT_BANNER_RIGHT_TITLE = 'healthlab_product_catalog/content/banner_right/title';
    const PRODUCT_CATALOG_CONTENT_BANNER_RIGHT_DESCRIPTION = 'healthlab_product_catalog/content/banner_right/description';
    const PRODUCT_CATALOG_CONTENT_BANNER_RIGHT_IMAGE = 'healthlab_product_catalog/content/banner_right/image';
    const PRODUCT_CATALOG_CONTENT_BANNER_LEFT_TITLE = 'healthlab_product_catalog/content/banner_left/title';
    const PRODUCT_CATALOG_CONTENT_BANNER_LEFT_DESCRIPTION = 'healthlab_product_catalog/content/banner_left/description';
    const PRODUCT_CATALOG_CONTENT_BANNER_LEFT_IMAGE = 'healthlab_product_catalog/content/banner_left/image';
    const PRODUCT_CATALOG_SELECT_CATEGORY =  'healthlab_product_catalog/category/select_category';
    const PRODUCT_CATALOG_MAIN_CATEGORY =  'healthlab_product_catalog/category/sub_main_categories';
    const PRODUCT_CATALOG_HEALTH_CONCERNS =  'healthlab_product_catalog/category/sub_health_concerns';

    //Meal Plan
    const MEAL_PLAN_ATTRIBUTE = 'healthlab_meal_plan/attribute/meal';
    const MEAL_PLAN_TITLE = 'healthlab_meal_plan/attribute/title';
    const MEAL_PLAN_DES = 'healthlab_meal_plan/attribute/des';
    const MEAL_PLAN_IMAGE = 'healthlab_meal_plan/attribute/image';
    const MEAL_PLAN_TEMPLATE = 'healthlab_meal_plan/mail/template';
    const MEAL_PLAN_EMAIL = 'healthlab_meal_plan/mail/email';
    const MEAL_PLAN_NAME = 'healthlab_meal_plan/mail/name';

    const NAME_CUSTOMER_SUPPORT = 'trans_email/ident_support/name';
    const EMAIL_CUSTOMER_SUPPORT = 'trans_email/ident_support/email';
    const ATTRIBUTE_INFOGRAPHIC = 'healthlab_insight/attribute/insight';

    const INSIGHT_TITLE = 'healthlab_insight/attribute/title';
    const INSIGHT_DES = 'healthlab_insight/attribute/des';
    const INSIGHT_IMAGE = 'healthlab_insight/attribute/image';

    /**
     * @var StoreManagerInterface
     */
    protected StoreManagerInterface $_storeManager;

    /**
     * @var CategoryRepositoryInterface
     */
    public CategoryRepositoryInterface $categoryRepository;

    /**
     * @var RedirectInterface
     */
    protected RedirectInterface $redirect;

    /**
     * @param Context $context
     * @param StoreManagerInterface $storeManagerInterface
     * @param RedirectInterface $redirect
     * @param CategoryRepositoryInterface $categoryRepository
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManagerInterface,
        RedirectInterface $redirect,
        CategoryRepositoryInterface $categoryRepository
    )
    {
        parent::__construct($context);
        $this->_storeManager = $storeManagerInterface;
        $this->categoryRepository = $categoryRepository;
        $this->redirect = $redirect;
    }

    /**
     * @param $categoryId
     * @return string|null
     */
    public function getCategoryUrlById(int $categoryId): ?string
    {
        try {
            $category = $this->categoryRepository->get($categoryId);
            return $category->getUrl();
        } catch (NoSuchEntityException $e) {
            return null;
        }
    }

    //General
    public function headerLogo(): string
    {
        return $this->getValueByPath(self::GENERAL_HEADER_LOGO);
    }

    public function footerQRImage(): string
    {
        return $this->getValueByPath(self::GENERAL_FOOTER_QR_IMAGE);
    }

    public function footerQRText(): string
    {
        return $this->getValueByPath(self::GENERAL_FOOTER_QR_TEXT);
    }

    public function emailTemplate(): string
    {
        return $this->getValueByPath(self::MEAL_PLAN_TEMPLATE);
    }

    public function emailAddressMealPlan(): string
    {
        return $this->getValueByPath(self::MEAL_PLAN_EMAIL);
    }

    public function emailNameMealPlan(): string
    {
        return $this->getValueByPath(self::MEAL_PLAN_NAME);
    }

    public function emailAddress(): string
    {
        return $this->getValueByPath(self::GENERAL_FOOTER_EMAIL);
    }

    public function phoneNumber(): string
    {
        return $this->getValueByPath(self::GENERAL_FOOTER_PHONE);
    }

    public function noResult(): string
    {
        return $this->getValueByPath(self::GENERAL_NO_RESULT);
    }

    public function brand(): string
    {
        return $this->getValueByPath(self::GENERAL_HEALTHLAB_BRAND);
    }

    //Product Catalog
    public function searchByKeyword(): string
    {
        return $this->getValueByPath(self::PRODUCT_CATALOG_ATTRIBUTE);
    }

    public function contentTitleSm(): string
    {
        return $this->getValueByPath(self::PRODUCT_CATALOG_CONTENT_TITLE_SM);
    }

    public function contentTitleLg(): string
    {
        return $this->getValueByPath(self::PRODUCT_CATALOG_CONTENT_TITLE_LARGE);
    }

    public function contentDescription(): string
    {
        return $this->getValueByPath(self::PRODUCT_CATALOG_CONTENT_DESCRIPTION);
    }

    public function contentPlaceholder(): string
    {
        return $this->getValueByPath(self::PRODUCT_CATALOG_CONTENT_PLACEHOLDER);
    }

    public function contentInputExample(): string
    {
        return $this->getValueByPath(self::PRODUCT_CATALOG_CONTENT_INPUT_EXAMPLE);
    }

    public function contentImage(): string
    {
        return $this->getValueByPath(self::PRODUCT_CATALOG_CONTENT_IMAGE);
    }

    public function contentBannerRightTitle(): string
    {
        return $this->getValueByPath(self::PRODUCT_CATALOG_CONTENT_BANNER_RIGHT_TITLE);
    }

    public function contentBannerRightDescription(): string
    {
        return $this->getValueByPath(self::PRODUCT_CATALOG_CONTENT_BANNER_RIGHT_DESCRIPTION);
    }

    public function contentBannerRightImage(): string
    {
        return $this->getValueByPath(self::PRODUCT_CATALOG_CONTENT_BANNER_RIGHT_IMAGE);
    }

    public function contentBannerLeftTitle(): string
    {
        return $this->getValueByPath(self::PRODUCT_CATALOG_CONTENT_BANNER_LEFT_TITLE);
    }

    public function contentBannerLeftDescription(): string
    {
        return $this->getValueByPath(self::PRODUCT_CATALOG_CONTENT_BANNER_LEFT_DESCRIPTION);
    }

    public function contentBannerLeftImage(): string
    {
        return $this->getValueByPath(self::PRODUCT_CATALOG_CONTENT_BANNER_LEFT_IMAGE);
    }

    public function healthlabCategory(): string
    {
        return $this->getValueByPath(self::PRODUCT_CATALOG_SELECT_CATEGORY);
    }

    public function mainCategory(): string
    {
        return $this->getValueByPath(self::PRODUCT_CATALOG_MAIN_CATEGORY);
    }

    public function concernsCategory(): string
    {
        return $this->getValueByPath(self::PRODUCT_CATALOG_HEALTH_CONCERNS);
    }

    //Meal Plan
    public function mealPlanAttribute(): string
    {
        return $this->getValueByPath(self::MEAL_PLAN_ATTRIBUTE);
    }

    public function mealPlanTitle(): string
    {
        return $this->getValueByPath(self::MEAL_PLAN_TITLE);
    }

    public function mealPlanDescription(): string
    {
        return $this->getValueByPath(self::MEAL_PLAN_DES);
    }

    public function mealPlanImage(): string
    {
        return $this->getValueByPath(self::MEAL_PLAN_IMAGE);
    }

    //Base
    private function getValueByPath($path): string
    {
        return trim((string)$this->scopeConfig->getValue($path,ScopeInterface::SCOPE_STORE,$this->getStoreCode()));
    }

    public function getStoreCode(): string
    {
        return $this->_storeManager->getStore()->getCode();
    }

    /** General */
    /**
     * @return string
     * @throws NoSuchEntityException
     */
    public function getMediaBaseUrl(): string
    {
        return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
    }

    /**
     * @param string $param
     * @return string
     */
    public function getParameterValue(string $param): string
    {
        $result = '';
        try {
            $result = $this->_getRequest()->getParam($param);
        }catch (\Exception $e){}

        return $result;
    }


    /**
     * @return string
     * @throws NoSuchEntityException
     */
    public function getNameCustomerSupport(): string
    {
        return $this->getValueByPath(self::NAME_CUSTOMER_SUPPORT);
    }

    /**
     * @return string
     * @throws NoSuchEntityException
     */
    public function getAttributeInfographic(): string
    {
        return $this->getValueByPath(self::ATTRIBUTE_INFOGRAPHIC);
    }

    public function insightTitle(): string
    {
        return $this->getValueByPath(self::INSIGHT_TITLE);
    }

    public function insightDescription(): string
    {
        return $this->getValueByPath(self::INSIGHT_DES);
    }

    public function insightImage(): string
    {
        return $this->getValueByPath(self::INSIGHT_IMAGE);
    }

    /**
     * @return string
     * @throws NoSuchEntityException
     */
    public function getEmailCustomerSupport(): string
    {
        return $this->getValueByPath(self::EMAIL_CUSTOMER_SUPPORT);
    }

    /**
     * @return string
     */
    public function previousPage(): string
    {
        return $this->redirect->getRefererUrl();
    }

    /**
     * @return string
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function getCurrentCurrency(): string
    {
        $store = $this->_storeManager->getStore();
        return $store->getCurrentCurrency()->getCurrencySymbol();
    }

    /**
     * @param array $listStore
     * @return bool
     * @throws NoSuchEntityException
     */
    public function isShowPerStore(array $listStore): bool
    {
        $store = $this->_storeManager->getStore()->getId();
        if(in_array( 0 , $listStore) || in_array( $store , $listStore))
        {
            return true;
        }

        return false;
    }
}
