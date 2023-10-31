<?php

namespace ML\DeveloperTest\Model\Config\Source;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;
use Magento\Directory\Model\ResourceModel\Country\CollectionFactory as CountryCollectionFactory;

class CountryOptions extends AbstractSource
{
    /**
     * @var CountryCollectionFactory
     */
    protected CountryCollectionFactory $countryCollectionFactory;

    /**
     * @param CountryCollectionFactory $countryCollectionFactory
     */
    public function __construct(CountryCollectionFactory $countryCollectionFactory)
    {
        $this->countryCollectionFactory = $countryCollectionFactory;
    }

    /**
     * Return option array
     * @return array
     */
    public function getAllOptions(): array
    {
        $options = [];
        $countryCollection = $this->countryCollectionFactory->create();
        foreach ($countryCollection as $country) {
            $options[] = [
                'value' => $country->getCountryId(),
                'label' => $country->getName(),
            ];
        }
        return $options;
    }
}
