<?php

namespace ML\DeveloperTest\Plugin;

use Magento\Checkout\Model\Cart;
use Magento\Framework\Message\ManagerInterface;
use ML\DeveloperTest\Helper\Config as HelperConfig;
use ML\DeveloperTest\Model\IpInfoDetails as IpInfoDetailsHelper;

class PreventAddToCartByCountry
{
    /**
     * @var ManagerInterface
     */
    protected ManagerInterface $messageManager;
    /**
     * @var HelperConfig
     */
    protected HelperConfig $helperConfig;
    /**
     * @var IpInfoDetailsHelper
     */
    protected IpInfoDetailsHelper $ipInfoDetailsHelper;

    /**
     * @param ManagerInterface $messageManager
     * @param HelperConfig $helperConfig
     * @param IpInfoDetailsHelper $ipInfoDetailsHelper
     */
    public function __construct(
        ManagerInterface    $messageManager,
        HelperConfig        $helperConfig,
        IpInfoDetailsHelper $ipInfoDetailsHelper

    )
    {
        $this->messageManager = $messageManager;
        $this->helperConfig = $helperConfig;
        $this->ipInfoDetailsHelper = $ipInfoDetailsHelper;
    }

    /**
     * use plugin for addProduct function
     * check product allowed or not to buy
     *
     * @param Cart $subject
     * @param $productInfo
     * @param $requestInfo
     * @return array
     * @throws \ipinfo\ipinfo\IPinfoException
     */
    public function beforeAddProduct(
        Cart $subject,
             $productInfo,
             $requestInfo = null
    )
    {
        $productInfo->getCustomAttribute('block_product_by_country');
        if ($this->ipInfoDetailsHelper->isEnable()) {
            $getIpInfo = $this->ipInfoDetailsHelper->getIpInfo();
            if (!isset($getIpInfo->error)
                && isset($getIpInfo->country)
                && !$this->isProductAllowed($productInfo, $getIpInfo->country)
            ) {
                try {
                    $errorMsg = $this->getErrorMessage($getIpInfo->region);
                    throw new \Exception($errorMsg);
                } catch (\Exception $e) {
                    $this->messageManager->addErrorMessage($e->getMessage());
                }
                die;
            }
        }
        return [$productInfo, $requestInfo];
    }

    /**
     * check for product is allowed to buy or not
     *
     * @param $productInfo
     * @param $getCountryByIp
     * @return bool
     */
    protected function isProductAllowed($productInfo, $getCountryByIp): bool
    {
        if (!empty($productInfo) && !empty($getCountryByIp)) {
            $customAttribute = $productInfo->getCustomAttribute(HelperConfig::BLOCK_PRODUCT_BY_COUNTRY_ATTR);
            if (!empty($customAttribute) && !empty($customAttribute->getValue())) {
                $blockCountryIds = $customAttribute->getValue();
                $blockCountryIdsAry = explode(',', $blockCountryIds);
                if (in_array($getCountryByIp, $blockCountryIdsAry)) {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * Return error message
     *
     * @param $countryName
     * @return string
     */
    public function getErrorMessage($countryName): string
    {
        $configMsg = $this->ipInfoDetailsHelper->getErrorMessage();
        if ($configMsg) {
            if (str_contains($configMsg, 'COUNTRY_NAME')) {
                return str_replace('COUNTRY_NAME', $countryName, $configMsg);
            }
            return $configMsg;
        }
        return "We can't add this item to your shopping cart right now.";
    }
}
