<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\conference\models\ConferenceSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Conferences';
$this->params['breadcrumbs'][] = $this->title;
?>
    <p>
        <?= Html::a('Create Conference', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
<div class="box">
<div class="box-header"></div>
<div class="box-body"><div class="conference-index">




    <div class="card shadow mb-4">

            <div class="card-body"><?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'conf_name',
            
            'date_start:date',
            'conf_venue',
            //'conf_url:url',
			
			

            ['class' => 'yii\grid\ActionColumn',
                 'contentOptions' => ['style' => 'width: 13%'],
                'template' => '{update} {delete}',
                //'visible' => false,
                'buttons'=>[
                    'update'=>function ($url, $model) {
                        return Html::a('<span class="fa fa-edit"></span>',['conference/update/', 'conf' => $model->id],['class'=>'btn btn-warning btn-sm']);
                    },
                    'delete'=>function ($url, $model) {
                        return Html::a('<span class="fa fa-trash"></span>', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger btn-sm',
            'data' => [
                'confirm' => 'Are you sure you want to delete this conference?',
                'method' => 'post',
            ],
        ]) 
;
                    }
                ],
            
            ],

        ],
    ]); ?></div>
</div>
</div>
</div>
</div>