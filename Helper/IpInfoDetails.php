<?php

namespace ML\DeveloperTest\Helper;

use ipinfo\ipinfo\Details;
use ipinfo\ipinfo\IPinfo;
use ipinfo\ipinfo\IPinfoException;
use ML\DeveloperTest\Helper\Config as ConfigHelper;

class IpInfoDetails
{
    /**
     * @var IPinfo
     */
    protected $ipInfoApi;
    /**
     * @var Config
     */
    private $configHelper;

    /**
     * @param IPinfo $ipInfoApi
     * @param Config $configHelper
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
     * Return country id from ipinfo
     *
     * @return string|null
     * @throws IPinfoException
     */
    public function getCountryIdByCustomerIp(): ?string
    {
        $details = $this->getIpInfo();
        return $details->country;
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
