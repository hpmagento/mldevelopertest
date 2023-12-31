<?php

namespace ML\DeveloperTest\Plugin;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Message\ManagerInterface;
use Magento\Quote\Model\QuoteManagement;
use Magento\Framework\Controller\ResultFactory;
use ML\DeveloperTest\Helper\Config as HelperConfig;
use ML\DeveloperTest\Model\IpInfoDetails as IpInfoDetailsHelper;
use Magento\Quote\Model\QuoteRepository;
use Magento\Framework\Exception\LocalizedException;

class PreventOrderPlacement
{
    /**
     * @var ManagerInterface
     */
    protected ManagerInterface $messageManager;
    /**
     * @var ResultFactory
     */
    protected ResultFactory $resultFactory;
    /**
     * @var IpInfoDetailsHelper
     */
    protected IpInfoDetailsHelper $ipInfoDetailsHelper;
    /**
     * @var QuoteRepository
     */
    protected QuoteRepository $quoteRepository;

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
     * @param $paymentMethod
     * @return array
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function beforePlaceOrder(QuoteManagement $subject, $cartId, $paymentMethod = null)
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
                }
            }
        }

        return [$cartId, $paymentMethod];
    }

    /**
     * Return blocked quote items
     *
     * @param $cartId
     * @param $getCountryByIp
     * @return array
     * @throws NoSuchEntityException
     */
    protected function getBlockedItems($cartId, $getCountryByIp): array
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
     * Return error message
     *
     * @param $countryName
     * @param $getBlockedItems
     * @return string
     */
    public function getErrorMessage($countryName, $getBlockedItems): string
    {
        return "I’m sorry, these products " . implode(" ,", $getBlockedItems) . " cannot be ordered from " . $countryName;
    }
}
