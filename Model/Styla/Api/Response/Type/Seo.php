<?php

class Styla_Connect_Model_Styla_Api_Response_Type_Seo extends Styla_Connect_Model_Styla_Api_Response_Type_Abstract
{
    protected $_contentType = self::CONTENT_TYPE_JSON;

    /**
     * Same as parent class, but doesn't throw exceptions if there's no result for this page
     * @return string
     * @throws Styla_Connect_Exception
     */
    public function getResult()
    {
        //if the request returned 404 - that's an exception and we will not process anything further
        if ($this->getHttpStatus() == 404) {
            throw new Styla_Connect_Exception(
                'The Styla Api SEO request failed: ' . $this->getHttpStatus() . ' - ' . $this->getError()
            );
        }

        $result = array();

        //if there was an invalid result - we'll return an empty seo result and move on
        if ($this->getHttpStatus() != 200) {
            return $result;
        }

        //if all's ok - we'll process and return the seo data
        $result = $this->getProcessedResult();

        return $result;
    }
}