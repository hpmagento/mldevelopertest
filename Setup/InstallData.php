<?php

namespace ML\DeveloperTest\Setup;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Setup\CategorySetupFactory;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use ML\DeveloperTest\Model\Config\Source\CountryOptions;
use ML\DeveloperTest\Helper\Config as HelperConfig;

class InstallData implements InstallDataInterface
{
    private $categorySetupFactory;

    /**
     * @param CategorySetupFactory $categorySetupFactory
     */
    public function __construct(
        CategorySetupFactory $categorySetupFactory
    )
    {
        $this->categorySetupFactory = $categorySetupFactory;
    }

    /**
     * Return option array
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return array
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        $categorySetup = $this->categorySetupFactory->create(['setup' => $setup]);

        $categorySetup->addAttribute(
            Product::ENTITY,
            HelperConfig::BLOCK_PRODUCT_BY_COUNTRY_ATTR,
            [
                'type' => 'varchar',
                'label' => 'Block product by countries',
                'input' => 'multiselect',
                'source' => CountryOptions::class,
                'required' => false,
                'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                'group' => 'General',
                'sort_order' => 30,
                'visible' => true,
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => false,
                'used_in_product_listing' => true,
                'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
            ]
        );

        $setup->endSetup();
    }
}
