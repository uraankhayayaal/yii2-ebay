<?php 

	echo \yii\widgets\ListView::widget([
        'dataProvider' => $dataProvider,
        'itemView' => 'item',
        'options' => [
            'class' => '',
            'tag' => 'ul',
        ],
        'emptyTextOptions' => [
            'style' => 'padding:20px;'
        ],
        'layout' => '{items}',
    ]);
?>

