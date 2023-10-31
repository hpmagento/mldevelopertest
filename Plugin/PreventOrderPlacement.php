<?php

namespace ML\DeveloperTest\Plugin;

use Magento\Framework\Message\ManagerInterface;
use Magento\Quote\Model\QuoteManagement;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address;
use Magento\Framework\Controller\ResultFactory;
use ML\DeveloperTest\Helper\Config as HelperConfig;
use ML\DeveloperTest\Helper\IpInfoDetails as IpInfoDetailsHelper;
use Magento\Quote\Model\QuoteRepository;
use Magento\Framework\Exception\LocalizedException;

class PreventOrderPlacement
{
    /**
     * @var ManagerInterface
     */
    protected $messageManager;
    /**
     * @var ResultFactory
     */
    protected $resultFactory;
    /**
     * @var IpInfoDetailsHelper
     */
    protected $ipInfoDetailsHelper;
    /**
     * @var QuoteRepository
     */
    protected $quoteRepository;

    /**
     * @param ManagerInterface $messageManager
     * @param ResultFactory $resultFactory
     * @param IpInfoDetailsHelper $ipInfoDetailsHelper
     * @param QuoteRepository $quoteRepository
     */
    public function __construct(
        ManagerInterface    $messageManager,
        ResultFactory       $resultFactory,
        IpInfoDetailsHelper $ipInfoDetailsHelper,
        QuoteRepository     $quoteRepository
    )
    {
        $this->messageManager = $messageManager;
        $this->resultFactory = $resultFactory;
        $this->ipInfoDetailsHelper = $ipInfoDetailsHelper;
        $this->quoteRepository = $quoteRepository;
    }

    /**
     * Check blocked items from quote and prevent place order
     *
     * @param QuoteManagement $subject
     * @param $cartId
     * @param Address|null $billingAddress
     * @return array|void
     * @throws \ipinfo\ipinfo\IPinfoException
     */
    public function beforePlaceOrder(QuoteManagement $subject, $cartId, Address $billingAddress = null)
    {
        if ($this->ipInfoDetailsHelper->isEnable()) {
            $getIpInfo = $this->ipInfoDetailsHelper->getIpInfo();
            if (!isset($getIpInfo->error)
                && isset($getIpInfo->country)
            ) {
                $getBlockedItems = $this->getBlockedItems($cartId, $getIpInfo->country);
                if (!empty($getBlockedItems)) {
                    $errorMsg = $this->getErrorMessage($getIpInfo->region, $getBlockedItems);
                    throw new LocalizedException(__($errorMsg));
                    /*
                    try {
                        $errorMsg = $this->getErrorMessage($getIpInfo->region, $getBlockedItems);
                        throw new \Magento\Framework\Exception\LocalizedException(__($errorMsg));
                    } catch (\Magento\Framework\Exception\LocalizedException $e) {
                        $this->messageManager->addErrorMessage($e->getMessage());
                        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
                        $resultRedirect->setPath('checkout/cart'); // Redirect to the cart page
                        die;
                    }*/
                }
            }
        }

        return [$cartId, $billingAddress];
    }

    /**
     * Return blocked quote items
     *
     * @param $cartId
     * @param $getCountryByIp
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function getBlockedItems($cartId, $getCountryByIp)
    {
        $blockedItemName = [];
        if (!empty($cartId) && !empty($getCountryByIp)) {
            $quote = $this->quoteRepository->getActive($cartId);
            $quoteItems = $quote->getAllVisibleItems();
            foreach ($quoteItems as $quoteItem) {
                $product = $quoteItem->getProduct();
                $customAttribute = $product->getCustomAttribute(HelperConfig::BLOCK_PRODUCT_BY_COUNTRY_ATTR);
                if (!empty($customAttribute) && !empty($customAttribute->getValue())) {
                    $blockCountryIds = $customAttribute->getValue();
                    $blockCountryIdsAry = explode(',', $blockCountryIds);
                    if (in_array($getCountryByIp, $blockCountryIdsAry)) {
                        $blockedItemName[] = $quoteItem->getName();
                    }
                }
            }
        }
        return $blockedItemName;
    }

    /**
     * @param $countryName
     * @param $getBlockedItems
     * @return string
     */
    public function getErrorMessage($countryName, $getBlockedItems): string
    {
        return "I’m sorry, these products " . implode(" ,", $getBlockedItems) . " cannot be ordered from " . $countryName;
    }
}
