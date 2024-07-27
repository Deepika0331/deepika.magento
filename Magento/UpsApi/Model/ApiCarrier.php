<?php

namespace Meticulosity\UpsApi\Model;


class ApiCarrier extends \Magento\Ups\Model\Carrier
{
    private $tokenDataJson = [];
    private $response = [];


	 /**
     * Get UPS rates by API
     *
     * @return Result
     */

    const XML_PATH_CLIENT_ID = 'carriers/ups/client_id';
    const XML_PATH_CLIENT_SECRET = 'carriers/ups/client_secret';

     /**
     * Do remote request for  and handle errors
     *
     * @return Result|null
     */
    protected function _getQuotes()
    {
        switch ($this->getConfigData('type')) {
            case 'UPS':
                return $this->_getCgiQuotes();
            case 'UPS_XML':
                return $this->_getXmlQuotes();
            case 'UPS_API':
                return $this->_getApiQuotes();
            default:
                break;
        }

        return null;
    }

    protected function _getApiQuotes()
    {

    	
       
        $version = "v1";
        //$requestoption = "shop";

        $rowRequest = $this->_rawRequest;


        if (self::USA_COUNTRY_ID == $rowRequest->getDestCountry()) {
            $destPostal = substr((string)$rowRequest->getDestPostal(), 0, 5);
        } else {
            $destPostal = $rowRequest->getDestPostal();
        }
        $params = [
            'accept_UPS_license_agreement' => 'yes',
            '10_action' => $rowRequest->getAction(),
            '13_product' => $rowRequest->getProduct(),
            '14_origCountry' => $rowRequest->getOrigCountry(),
            '15_origPostal' => $rowRequest->getOrigPostal(),
            'origCity' => $rowRequest->getOrigCity(),
            'origRegionCode' => $rowRequest->getOrigRegionCode(),
            '19_destPostal' => $destPostal,
            '22_destCountry' => $rowRequest->getDestCountry(),
            'destRegionCode' => $rowRequest->getDestRegionCode(),
            '23_weight' => $rowRequest->getWeight(),
            '47_rate_chart' => $rowRequest->getPickup(),
            '48_container' => $rowRequest->getContainer(),
            '49_residential' => $rowRequest->getDestType(),
        ];
        $params['23_weight'] = strval($params['23_weight']);
        if ($params['10_action'] == '4') {
            $params['10_action'] = 'Shop';
            $serviceCode = null;
        } else {
            $params['10_action'] = 'Rate';
            $serviceCode = $rowRequest->getProduct() ? $rowRequest->getProduct() : null;
        }
        $serviceDescription = $serviceCode ? $this->getShipmentByCode($serviceCode) : '';
        

        if ($rowRequest->getIsReturn()) {
            $shipperCity = '';
            $shipperPostalCode = $params['19_destPostal'];
            $shipperCountryCode = $params['22_destCountry'];
            $shipperStateProvince = $params['destRegionCode'];
        } else {
            $shipperCity = $params['origCity'];
            $shipperPostalCode = $params['15_origPostal'];
            $shipperCountryCode = $params['14_origCountry'];
            $shipperStateProvince = $params['origRegionCode'];
        }

        $shipperCity = $shipperCity ? $shipperCity : '';
        
        $payload = array(
          "RateRequest" => array(
            "Shipment" => array(
              "Shipper" => array(
                "Address" => array(
                  "City" => $shipperCity,
                  "StateProvinceCode" => $shipperStateProvince,
                  "PostalCode" => $shipperPostalCode,
                  "CountryCode" => $shipperCountryCode
                )
              ),
              "ShipTo" => array(
                "Address" => array(
                  "StateProvinceCode" => $params['destRegionCode'],
                  "PostalCode" => $params['19_destPostal'],
                  "CountryCode" => $params['22_destCountry'],
                  "ResidentialAddress" => $params['49_residential']
                )
              ),
              "ShipFrom" => array(
                "Address" => array(
                  "StateProvinceCode" => $params['origRegionCode'],
                  "PostalCode" => $params['15_origPostal'],
                  "CountryCode" => $params['14_origCountry']
                )
              ),
             "Package" => array(
                "PackagingType" => array(
                  "Code" => $params['48_container']
                ),
                "PackageWeight" => array(
                  "UnitOfMeasurement" => array(
                    "Code" => $rowRequest->getUnitMeasure()
                  ),
                  "Weight" => $params['23_weight']
                )
              )
            )
          )
        );
        if ($params['49_residential'] === '01') {
            $payload['RateRequest']['Shipment']['ShipTo']['ResidentialAddressIndicator']['Address'] = $params['49_residential'];
        }        

        $payload = json_encode($payload);
        if(!empty($this->configHelper->getTokenSession())) {
            $access_token = $this->configHelper->getTokenSession();
        }else{

            if( isset( $this->tokenDataJson ) && empty( $this->tokenDataJson ) ):

                $this->tokenDataJson = $this->getAccessToken();
            endif;
           

            if( isset( $this->tokenDataJson ) && !empty( $this->tokenDataJson ) ):
              
                $tokenData = json_decode($this->tokenDataJson,true);


                $tokenData = $this->checkExpriy($tokenData);


                $this->configHelper->setTokenSession($tokenData); 

                $access_token = $tokenData['access_token'];

               

            endif;

        }

            try{
    		    $curl = curl_init();
                curl_setopt_array($curl, [
                CURLOPT_HTTPHEADER => [
                    "Authorization: Bearer ".$access_token,
                    "Content-Type: application/json",
                    "transId: Tran123",
                    "transactionSrc: XOLT"
                  ],
                  CURLOPT_POSTFIELDS =>$payload,
                  CURLOPT_URL => "https://wwwcie.ups.com/api/rating/" . $version . "/" . $params['10_action'],
                  CURLOPT_RETURNTRANSFER => true,
                  CURLOPT_CUSTOMREQUEST => "POST",
                ]);
               
                    $apiResponse = curl_exec($curl);
                    $error = curl_error($curl);
                    curl_close($curl);
                    // if ($error) {
                    //   echo "cURL Error #:" . $error;
                    // } 
            }catch (\Throwable $e) {
                    $apiResponse['result'] = ['error' => $e->getMessage(), 'code' => $e->getCode()];
                    $responseBody = '';
                }
                $this->_debug($apiResponse);

                $this->response = json_decode($apiResponse,true);
                   
                return $this->_parseApiResponse($this->response);

    }


     public function getAccessToken(){


        $objectManager =  \Magento\Framework\App\ObjectManager::getInstance();        
        $storeManager  = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
        $storeID       = $storeManager->getStore()->getStoreId();

        $ups_secret = $this->_scopeConfig->getValue(
                'carriers/ups/client_secret',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeID
            );
        $ups_id = $this->_scopeConfig->getValue(
                'carriers/ups/client_id',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeID
            );

        $credentials = base64_encode("$ups_id:$ups_secret");
        $url = 'https://wwwcie.ups.com/security/v1/oauth/token';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'accept: application/json',
            "x-merchant-id: $ups_id",
            "Authorization: Basic $credentials",
            'Content-Type: application/x-www-form-urlencoded',
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, 'grant_type=client_credentials');
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }

    public function checkExpriy(array $tokenData){

      
        $expires_in = $tokenData['expires_in'];
        $time = time();
        $expires_time = $time + $expires_in;

        if($time > $expires_time){
            $tokenDataJson = $this->getAccessToken();
            $tokenData = json_decode($tokenDataJson,true);
        }
        return $tokenData;


    }



	/**
     * Prepare shipping rate result based on response
     *
     * @param string $response
     * @return Result
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function _parseApiResponse($response)
    {
        

        $costArr = [];
        $priceArr = [];
        
        if(isset($response['RateResponse']['Response']['ResponseStatus'])){

            $arr = $response['RateResponse']['Response']['ResponseStatus']['Code'];
            $success = (int)$arr;
        	if($success === 1){

                $arr = $response['RateResponse']['RatedShipment'];
                $allowedMethods = explode(",", $this->getConfigData('allowed_methods'));


                $allowedCurrencies = $this->_currencyFactory->create()->getConfigAllowCurrencies();
                foreach ($arr as $shipElement) {
                        $this->apiProcessShippingRateForItem(
                            $shipElement,
                            $allowedMethods,
                            $allowedCurrencies,
                            $costArr,
                            $priceArr
                        );

                    }

                }
            }else{
                    $arr = $response['response']['errors'][0]['message'];
                    $errorTitle = (string)$arr;
                    $error = $this->_rateErrorFactory->create();
                    $error->setCarrier('ups');
                    $error->setCarrierTitle($this->getConfigData('title'));
                    $error->setErrorMessage($this->getConfigData('specificerrmsg'));
                }

        $result = $this->_rateFactory->create();

        if (empty($priceArr)) {
            $error = $this->_rateErrorFactory->create();
            $error->setCarrier('ups');
            $error->setCarrierTitle($this->getConfigData('title'));
            if ($this->getConfigData('specificerrmsg') !== '') {
                $errorTitle = $this->getConfigData('specificerrmsg');
            }
            if (!isset($errorTitle)) {
                $errorTitle = __('Cannot retrieve shipping rates');
            }
            $error->setErrorMessage($errorTitle);
            $result->append($error);
        } else {
            foreach ($priceArr as $method => $price) {
                $rate = $this->_rateMethodFactory->create();
                $rate->setCarrier('ups');
                $rate->setCarrierTitle($this->getConfigData('title'));
                $rate->setMethod($method);
                $methodArr = $this->getShipmentByCode($method);
                $rate->setMethodTitle($methodArr);
                $rate->setCost($costArr[$method]);
                $rate->setPrice($price);
                $result->append($rate);
            }
        }

       
        return $result;

    }

    /**
     * Processing rate for ship element
     *
     * @param array $allowedMethods
     * @param array $allowedCurrencies
     * @param array $costArr
     * @param array $priceArr
     * @param bool $negotiatedActive
     */
    private function apiProcessShippingRateForItem(
        array $shipElement,
        array $allowedMethods,
        array $allowedCurrencies,
        array &$costArr,
        array &$priceArr
    ): void {
         
        $code = (string)$shipElement['Service']['Code'];
      
        if (in_array($code, $allowedMethods)) {
            
            $cost = $shipElement['TotalCharges']['MonetaryValue'];
           
            $responseCurrencyCode = $this->mapCurrencyCode(
                        (string)$shipElement['TotalCharges']['CurrencyCode']
                    );

            //convert price with Origin country currency code to base currency code
            $successConversion = true;
            if ($responseCurrencyCode) {
                if (in_array($responseCurrencyCode, $allowedCurrencies)) {
                    $cost = (double)$cost * $this->_getBaseCurrencyRate($responseCurrencyCode);
                } else {
                    $errorTitle = __(
                        'We can\'t convert a rate from "%1-%2".',
                        $responseCurrencyCode,
                        $this->_request->getPackageCurrency()->getCode()
                    );
                    $error = $this->_rateErrorFactory->create();
                    $error->setCarrier('ups');
                    $error->setCarrierTitle($this->getConfigData('title'));
                    $error->setErrorMessage($errorTitle);
                    $successConversion = false;
                }
            }

            if ($successConversion) {
                $costArr[$code] = $cost;
                $priceArr[$code] = $this->getMethodPrice((float)$cost, $code);
            }

        }
    }


    /**
     * Map currency alias to currency code
     *
     * @param string $code
     * @return string
     */
    private function mapCurrencyCode($code)
    {
        $currencyMapping = [
            'RMB' => 'CNY',
            'CNH' => 'CNY'
        ];

        return isset($currencyMapping[$code]) ? $currencyMapping[$code] : $code;
    }


}

?>