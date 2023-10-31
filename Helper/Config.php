<?php

namespace ML\DeveloperTest\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Config extends AbstractHelper
{
    const BLOCK_PRODUCT_BY_COUNTRY_ATTR = 'block_product_by_country';
    const MODULE_STATUS_PATH = 'ml_block_product/general/enable';
    const ERROR_MSG_PATH = 'ml_block_product/general/error_message';
    const ACCESS_TOKEN_PATH = 'ml_block_product/ipinfo/access_token';

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig
    )
    {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Return status
     *
     * @return boolean
     */
    public function getModuleStatus(): bool
    {
        return $this->scopeConfig->getValue(
            self::MODULE_STATUS_PATH,
            ScopeInterface::SCOPE_WEBSITE
        );
    }

    /**
     * Return message
     *
     * @return null|string
     */
    public function getErrorMessage(): ?string
    {
        return $this->scopeConfig->getValue(
            self::ERROR_MSG_PATH,
            ScopeInterface::SCOPE_WEBSITE
        );
    }

    /**
     * Return access token
     *
     * @return null|string
     */
    public function getAccessToken(): ?string
    {
        return $this->scopeConfig->getValue(
            self::ACCESS_TOKEN_PATH,
            ScopeInterface::SCOPE_WEBSITE
        );
    }

    /**
     * Check for status and access token
     *
     * @return bool
     */
    public function isEnable(): bool
    {
        if ($this->getModuleStatus()
            && !empty($this->getAccessToken())
        ) {
            return true;
        }
        return false;
    }
}
