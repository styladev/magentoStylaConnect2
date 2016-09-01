<?php
namespace Styla\Connect2\Model\Api\Converter\Type\Product;

use Styla\Connect2\Model\Api\Converter\Type as ConverterType;

class Gallery extends ConverterType\AbstractType
{
    const SEPARATOR = '||';
    const GALLERY_KEY_VALUES = 'all_gallery_images_values';
    const GALLERY_KEY = 'all_gallery_images';
    const MAIN_IMAGE_CAPTION_KEY = 'main_image_caption';
    
    protected function _addCollectionRequirements($collection) {
        /** @var \Zend_Db_Select $select */
        $select = $collection->getSelect();
        
        //TODO: load also only from the proper store view!!!!!!!//
        //
        //
        //
        //
        //
        
        
        //add all the gallery values for this product
        $select->joinLeft(
            [self::GALLERY_KEY_VALUES => 'catalog_product_entity_media_gallery_value'], 
            self::GALLERY_KEY_VALUES . '.entity_id = e.entity_id', 
            [
                self::GALLERY_KEY_VALUES . '.label as ' . self::MAIN_IMAGE_CAPTION_KEY
            ]
        );
        
        //add the image locations for the gallery values
        $select->joinLeft(
            [self::GALLERY_KEY => 'catalog_product_entity_media_gallery'], 
            self::GALLERY_KEY . '.value_id = ' . self::GALLERY_KEY_VALUES . '.value_id', 
            [
                new \Zend_Db_Expr('GROUP_CONCAT(' .self::GALLERY_KEY . '.value SEPARATOR "' . self::SEPARATOR . '") as ' . self::GALLERY_KEY_VALUES)
            ]
        );
        
        //bugfix! if i apply the ->group on select, the page and offset will be broken. i need to save them, first
        $pageSize = $collection->getPageSize();
        $pageOffset = $collection->getCurPage();
        
        $select->group('e.entity_id');
        $select->limit($pageSize, $pageOffset);
    }
    
    protected function _convertItem($item) {
        $value = explode(self::SEPARATOR, $item->getData($this->getMagentoField()));
        
        $this->_convertedValue = count($value) == 1 ? reset($value) : $value;
    }
}

