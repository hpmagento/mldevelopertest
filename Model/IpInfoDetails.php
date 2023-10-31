<?php

namespace ML\DeveloperTest\Model;

use Exception;
use ipinfo\ipinfo\Details;
use ipinfo\ipinfo\IPinfoException;
use ML\DeveloperTest\Helper\Config as ConfigHelper;
use Magento\Framework\ObjectManagerInterface;

class IpInfoDetails
{
    /**
     * @var ConfigHelper
     */
    private ConfigHelper $configHelper;
    /**
     * @var ObjectManagerInterface
     */
    protected ObjectManagerInterface $objectManager;

    /**
     * @param ConfigHelper $configHelper
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(
        ConfigHelper           $configHelper,
        ObjectManagerInterface $objectManager
    )
    {
        $this->configHelper = $configHelper;
        $this->objectManager = $objectManager;
    }

    /**
     * get current customer IP details
     *
     * @return Details
     * @throws Exception
     */
    public function getIpInfo(): Details
    {
        try {
            $token = $this->configHelper->getAccessToken();
            //$client = new IPinfo($token);
            $client = $this->objectManager->create('ipinfo\ipinfo\IPinfo', [$token]);
            return $client->getDetails();
        } catch (IPinfoException|Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Check for status and access token
     *
     * @return bool
     */
    public function isEnable(): bool
    {
        return $this->configHelper->isEnable();
    }

    /**
     * @return string|null
     */
    public function getErrorMessage(): ?string
    {
        return $this->configHelper->getErrorMessage();
    }
}
