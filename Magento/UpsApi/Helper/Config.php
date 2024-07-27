<?php 

namespace Meticulosity\UpsApi\Helper;

use Meticulosity\UpsApi\Model\UpsTokenFactory;
class Config extends \Magento\Ups\Helper\Config
{

    protected $UpsTokenFactory;

    protected $_catalogSession;
 
    public function __construct(
        UpsTokenFactory $UpsTokenFactory,
        \Magento\Catalog\Model\Session $catalogSession
    )
    {
        $this->UpsTokenFactory = $UpsTokenFactory;
        $this->_catalogSession = $catalogSession;

    }
    public function saveToken(array $tokenData){

        $model = $this->UpsTokenFactory->create();
        $model->setData($tokenData)->save();
        
    }



    public function setTokenSession(array $tokenData){
       
       
        if( isset( $tokenData ) && !empty( $tokenData ) ){
            $this->_catalogSession->setUpsToken($tokenData['access_token']);

        }else{
                $this->_catalogSession->setUpsToken("");

        }
    }


    public function getTokenSession(){
       
        return $this->_catalogSession->getUpsToken();

    }



}

?>