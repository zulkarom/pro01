<?php
use yii\widgets\ActiveForm;
use backend\modules\students\models\DownloadCategory;
use yii\helpers\ArrayHelper;

$this->title = 'Upload Documents';
$model->category = DownloadCategory::getDefaultCategory()->id;

$this->params['breadcrumbs'][] = ['label' => 'Downloads', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Uploads';
?>

<div class="box">
<div class="box-header"></div>
<div class="box-body">

<?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]) ?>

	<?= $form->field($model, 'category')->dropDownList(
       ArrayHelper::map(DownloadCategory::find()->orderBy('created_at DESC')->all(),'id','category_name'), ['prompt' => 'Select Category']
    ) ?>

    <?= $form->field($model, 'imageFiles[]')->fileInput(['multiple' => true, 'accept' => '.pdf']) ?>
	
	<i>
	* pdf file only<br />
	* file names = matric number<br />
	* multiple files can be uploaded (max 10 files)<br /><br /></i>

    <button class="btn btn-success"><i class="fa fa-upload"></i> Upload</button>

<?php ActiveForm::end() ?></div>
</div>
