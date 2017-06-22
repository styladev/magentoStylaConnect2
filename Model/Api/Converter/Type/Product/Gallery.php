<?php
namespace Styla\Connect2\Model\Api\Converter\Type\Product;

use Styla\Connect2\Model\Api\Converter\Type as ConverterType;
use Magento\Catalog\Model\ResourceModel\Collection\AbstractCollection as Collection;
use Magento\Store\Api\Data\StoreInterface as Store;

class Gallery extends ConverterType\AbstractType
{
    const ALL_STORES = '0';

    const SEPARATOR              = '||';
    const GALLERY_KEY_VALUES     = 'all_gallery_images_values';
    const GALLERY_KEY            = 'all_gallery_images';
    const MAIN_IMAGE_CAPTION_KEY = 'main_image_caption';

    protected $converterHelper;

    public function __construct(\Styla\Connect2\Helper\Converter $converterHelper)
    {
        $this->converterHelper = $converterHelper;
    }

    /**
     * 
     * @param Collection $collection
     * @param Store $store
     */
    protected function _addCollectionRequirements(Collection $collection, Store $store = null)
    {
        $entityIdField = $this->converterHelper->getProductEntityIdField();
        
        /** @var \Zend_Db_Select $select */
        $select = $collection->getSelect();

        //images set to "any store" and "our current store"
        $storeIds = [self::ALL_STORES];
        if ($store !== null && $store->getId() != self::ALL_STORES) {
            $storeIds[] = $store->getId();
        }

        //add all the gallery values for this product
        $select->joinLeft(
            [self::GALLERY_KEY_VALUES => 'catalog_product_entity_media_gallery_value'],
            self::GALLERY_KEY_VALUES . '.' . $entityIdField . ' = e.' . $entityIdField . ' AND ' . self::GALLERY_KEY_VALUES . '.store_id IN(' . implode(',', $storeIds) . ')',
            [
                self::GALLERY_KEY_VALUES . '.label as ' . self::MAIN_IMAGE_CAPTION_KEY
            ]
        );

        //add the image locations for the gallery values
        $select->joinLeft(
            [self::GALLERY_KEY => 'catalog_product_entity_media_gallery'],
            self::GALLERY_KEY . '.value_id = ' . self::GALLERY_KEY_VALUES . '.value_id',
            [
                new \Zend_Db_Expr('GROUP_CONCAT(' . self::GALLERY_KEY . '.value SEPARATOR "' . self::SEPARATOR . '") as ' . self::GALLERY_KEY_VALUES)
            ]
        );

        //bugfix! if i apply the ->group on select, the page and offset will be broken. i need to save them, first
        $pageSize   = $collection->getPageSize();
        $pageOffset = $collection->getCurPage();
        
        $select->group('e.' . $entityIdField);
        $select->limit($pageSize, $pageOffset);
    }

    protected function _convertItem($item)
    {
        $value = explode(self::SEPARATOR, $item->getData($this->getMagentoField()));

        if ($this->getArgument('use_url')) {
            foreach ($value as &$singleValue) {
                $singleValue = $this->converterHelper->getUrlForMedia($singleValue);
            }
        }

        $this->_convertedValue = count($value) == 1 ? reset($value) : $value;
    }
}

