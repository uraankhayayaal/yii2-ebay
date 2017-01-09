<?php

namespace common\widgets\yii2ebay;

/**
 * This is just an example.
 */
class AutoloadExample extends \yii\base\Widget
{
	//private $wsdl = 'http://svcs.sandbox.ebay.com/services/search/FindingService/v1';
	private $wsdl = 'http://developer.ebay.com/webservices/finding/latest/FindingService.wsdl';
    private $endpoint_uri = 'http://svcs.ebay.com/services/search/FindingService/v1';
    private $ns = 'http://www.ebay.com/marketplace/search/v1/services';
    private $appId = '3innova1c-796c-4410-8001-7359ec44d55';
    private $keywords = 'Maybelline';
    private $productId = '1597306f-2d70-a7e3-aa51-33f1f984a18f';

    public function run()
    {
        return $this->ebayFindByProduct();
        //return $this->ebay();
    }

    public function ebayFindByProduct()
    {
   		$operation = 'findItemsByProduct';

        $http_headers = implode(PHP_EOL, [
          "X-EBAY-SOA-OPERATION-NAME: $operation",
          "X-EBAY-SOA-SECURITY-APPNAME: ".$this->appId,
          "X-EBAY-SOA-GLOBAL-ID: EBAY-IT",
          /*"productId.@type: ReferenceID",
          "productId: 53039031",*/
          //"X-Ebay-Soa-Request-Id: 1597306f-2d70-a7e3-aa51-33f1f984a18f",
        ]);

        $options = [
          'trace' => true,
          'cache' => WSDL_CACHE_NONE,
          'exceptions' => true,
          'location' => $this->endpoint_uri,
          //'uri' => 'ns1',
          'stream_context' => stream_context_create([
            'http' => [
              'method' => 'GET',
              'header' => $http_headers,
            ]
          ]),
        ];

        $client = new \SoapClient($this->wsdl, $options);

        try {
          $wrapper = new \StdClass;
          $wrapper->productId = new \SoapVar($this->productId, XSD_STRING,
            null, null, null, $this->ns);

          $result = $client->$operation(new \SoapVar($wrapper, SOAP_ENC_OBJECT));
          /*$text = var_dump($result);
          return $text;*/
        } catch (Exception $e) {
          return $e->getMessage();
        }

        if ($result->ack == "Success") 
        {
        	if (empty($result->searchResult->item))
        		return "Ebay have no this product!";
		    // If the response was loaded, parse it and build links
		    return $result->searchResult->item->itemId . " - " . $result->searchResult->item->sellingStatus->currentPrice;
		}
		else 
		{
		  	$results  = "<p>$result->ack</p>" . "<h3>Oops! The request was not successful. Make sure you are using a valid ";
		  	$results .= "AppID for the Production environment.</h3>";
		  	return $results;
		}
    }

    public function ebay()
    {
        $operation = 'findItemsByKeywords';

        $http_headers = implode(PHP_EOL, [
          "X-EBAY-SOA-OPERATION-NAME: $operation",
          "X-EBAY-SOA-SECURITY-APPNAME: ".$this->appId,
        ]);

        $options = [
          'trace' => true,
          'cache' => WSDL_CACHE_NONE,
          'exceptions' => true,
          'location' => $this->endpoint_uri,
          //'uri' => 'ns1',
          'stream_context' => stream_context_create([
            'http' => [
              'method' => 'POST',
              'header' => $http_headers,
            ]
          ]),
        ];

        $client = new \SoapClient($this->wsdl, $options);

        try {
          $wrapper = new \StdClass;
          $wrapper->keywords = new \SoapVar($this->keywords, XSD_STRING,
            null, null, null, $this->ns);

          $result = $client->$operation(new \SoapVar($wrapper, SOAP_ENC_OBJECT));
          /*$text = var_dump($result);
          return $text;*/
        } catch (Exception $e) {
          return $e->getMessage();
        }

        if ($result->ack == "Success") 
        {
        	if (empty($result->searchResult->item))
        		return "Ebay have no this product!";
		    // If the response was loaded, parse it and build links
		    return $this->keywords . "<br />" . $this->renderList($result->searchResult->item);
		}
		else 
		{
		  	$results  = "<h3>Oops! The request was not successful. Make sure you are using a valid ";
		  	$results .= "AppID for the Production environment.</h3>";
		  	return $results;
		}
    }

    public function renderList($list)
    {
    	$items = '';
		foreach($list as $item) {
			$items .= $this->renderItem($item);
	    }
	    return $items;
    }

    public function renderItem($item)
    {		
	    $pic   = $item->galleryURL;
	    $link  = $item->viewItemURL;
	    $title = $item->title;

	    return "<div><div><img src=\"$pic\"></div><div><a href=\"$link\">$title</a></div></div>";
    }
}
