<?php

/**
 * Class Styla_Connect_Model_Styla_Api_Response_Type_Version
 *
 */
class Styla_Connect_Model_Styla_Api_Response_Type_Version extends Styla_Connect_Model_Styla_Api_Response_Type_Abstract
{
    public function getResult()
    {
        try {
            $result = parent::getResult();
        } catch (Styla_Connect_Exception $e) {
            $result = '1';
        }

        return $result;
    }
}