<?php
namespace Styla\Connect2\Model\Api\Converter\Type\Product;

class GalleryCaption extends Gallery
{
    protected function _convertItem($item) {
        $caption = $item->getData(self::MAIN_IMAGE_CAPTION_KEY); //get the main_image_caption, which is loaded in the parent class on collection requirements step
        
        //if image caption is missing, get the product name instead
        $this->_convertedValue = $caption ? $caption : $item->getName();
    }
}
