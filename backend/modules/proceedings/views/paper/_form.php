<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\UploadFile;

$model->file_controller = 'paper';

/* @var $this yii\web\View */
/* @var $model backend\modules\proceedings\models\Paper */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="paper-form">

    <?php $form = ActiveForm::begin(); ?>

 
    <?= $form->field($model, 'paper_title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'author')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'paper_no')->textInput() ?>

    <?= $form->field($model, 'paper_page')->textInput(['maxlength' => true]) ?>

    <?=UploadFile::fileInput($model, 'paper')?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>