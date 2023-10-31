<?php

namespace ML\DeveloperTest\Model;

use ipinfo\ipinfo\Details;
use ipinfo\ipinfo\IPinfo;
use ipinfo\ipinfo\IPinfoException;
use ML\DeveloperTest\Helper\Config as ConfigHelper;

class IpInfoDetails
{
    /**
     * @var IPinfo
     */
    protected IPinfo $ipInfoApi;
    /**
     * @var ConfigHelper
     */
    private ConfigHelper $configHelper;

    /**
     * @param IPinfo $ipInfoApi
     * @param ConfigHelper $configHelper
     */
    public function __construct(
        IPinfo       $ipInfoApi,
        ConfigHelper $configHelper
    )
    {
        $this->ipInfoApi = $ipInfoApi;
        $this->configHelper = $configHelper;
    }

    /**
     * get current customer IP details
     *
     * @return Details
     * @throws IPinfoException
     */
    public function getIpInfo(): Details
    {
        $token = $this->configHelper->getAccessToken();
        //$client = $this->ipInfoApi->create($token);
        $client = new IPinfo($token);
        return $client->getDetails();
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
