<?php 
    $currencyId = "@currencyId";
?>
<li>
  <div class="product-box" itemscope itemtype="http://data-vocabulary.org/Product">
        <div class="title">
          <span itemprop="name">
              <a href="#" target="_new"><?= $model->title[0] ?></a>
            </span>
        </div>
        <div class="media">
          <a href="#" target="_new">
              <img src="<?= $model->galleryURL[0] ?>" alt="<?= $product->productName ?>" itemprop="image" />
            </a>
        </div>
        <span itemprop="offerDetails" itemscope itemtype="http://data-vocabulary.org/Offer">
            <div class="price-box">

                <meta itemprop="priceCurrency" content="<?= $model->sellingStatus[0]->currentPrice[0]->$currencyId ?>" />
              <div class="Price">
                  <!--&dollar;-->&euro; <span itemprop="price"><?= $model->sellingStatus[0]->currentPrice[0]->__value__ ?></span>
              </div>
                <span itemprop="availability" content="in_stock"></span>

            </div>
        </span>
  </div>
</li>