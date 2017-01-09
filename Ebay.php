<?php

namespace common\widgets\yii2ebay;

/**
 * This is just an example.
 */
class Ebay extends \yii\base\Widget
{
	  const FINDING_API_URL = 'http://svcs.ebay.com/services/search/FindingService/v1?';

    public $appId;
    public $productId;
    public $type;

    public function run()
    {

      if ($this->type == "keywords") {
          return $this->findByKeywords($this->productId);
      }else{
          return $this->findByProduct($this->productId, $this->type);
      }
    }

    public function findByProduct($id, $type = 'EAN')
    {
        $params = array(
            'productId.@type' => $type,
            'productId' => $id,
        );

        return $this->doApiRequest('findItemsByProduct', $params);
    }

    public function findByKeywords($keywords)
    {
        $params = array(
            'keywords' => $keywords,
        );

        return $this->doApiRequest('findItemsByKeywords', $params);
    }


    private function doApiRequest($operationName, $payload)
    {

        $global = array(
            'OPERATION-NAME' => $operationName,
            'SECURITY-APPNAME' => $this->appId,
            //'GLOBAL-ID' => 'EBAY-IT',
            'SERVICE-VERSION' => '1.0.0',
            'MESSAGE-ENCODING' => 'UTF-8',
            'RESPONSE-DATA-FORMAT' => 'JSON',
        );

        $ret = file_get_contents(
            self::FINDING_API_URL . http_build_query($global) . '&REST-PAYLOAD&' . http_build_query($payload)
        );

        //var_dump($ret);
        //return json_encode($ret);
        return $this->generateListByProduct($ret);
        return $this->generateListByKeywords($ret);
    }

    public function generateListByKeywords($json)
    {
        $list = "<ul>";
        $count = "@count";

        $data = \yii\helpers\Json::decode($json, false);


        for ($i=0; $i < intval($data->findItemsByKeywordsResponse[0]->searchResult[0]->$count); $i++) { 
          echo $data->findItemsByKeywordsResponse[0]->searchResult[0]->item[$i]->title[0]."<br />";
        }
    }
    public function generateListByProduct($json)
    {
        $list = "<ul>";
        $count = "@count";
        $type = "@type";

        $data = \yii\helpers\Json::decode($json, false);

        if($data->findItemsByProductResponse[0]->ack[0] != "Success")
        {
            var_dump($data->findItemsByProductResponse[0]->ack[0]);
            var_dump($data->findItemsByProductResponse[0]->errorMessage[0]);
        }
        else
        {
            for ($i=0; $i < $data->findItemsByProductResponse[0]->searchResult[0]->$count; $i++) { 
                echo '<div>';
                echo $data->findItemsByProductResponse[0]->searchResult[0]->item[$i]->productId[0]->$type.'-';
                echo $data->findItemsByProductResponse[0]->searchResult[0]->item[$i]->productId[0]->__value__;
                echo "<img src=".$data->findItemsByProductResponse[0]->searchResult[0]->item[$i]->galleryURL[0]."/>";
                echo $data->findItemsByProductResponse[0]->searchResult[0]->item[$i]->primaryCategory[0]->categoryName[0];
                echo '</div>';
            }
        }
    }
}
