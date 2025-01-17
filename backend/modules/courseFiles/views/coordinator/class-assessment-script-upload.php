<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use common\models\UploadFile;

/* @var $this yii\web\View */
/* @var $model backend\modules\teachingLoad\models\CourseOffered */
$course = $model->course;
$title = 'Continuous Assessment Script';
$this->title = $course->course_code . ' ' . $course->course_name;
$this->params['breadcrumbs'][] = ['label' => 'My Course File', 'url' => ['/course-files/default/teaching-assignment']];
$this->params['breadcrumbs'][] = ['label' => 'Coordinator', 'url' => ['/course-files/default/teaching-assignment-coordinator', 'id' => $model->id]];
$this->params['breadcrumbs'][] = $title;

?>
<h4><?=$model->semester->longFormat()?></h4>
<h4><?=$title?></h4>

<div class="form-group"><?php $form = ActiveForm::begin(); 
	
	$addFile->file_number = 1;
	echo $form->field($addFile, 'file_number', [
                    'template' => 'Add Files: {input}',
                    'options' => [
						
                        'tag' => false, // Don't wrap with "form-group" div
                    ]])->textInput(['style' => 'width:50px', 'type' => 'number', 'class' => ''])->label(false);

	echo Html::submitButton('Go', ['class' => 'btn btn-sm btn-default']);
	ActiveForm::end(); ?></div>
	
<?php $form = ActiveForm::begin(); ?>	
  <div class="box">

<div class="box-body">

<table class="table table-striped table-hover">

  <thead>
  <tr>
  <th width="5%">#</th>
  <th width="40%">Document Title</th>
  <th>Upload File</th>
  </tr>
</thead>

<tbody>
	<?php 
	$applicable = false;
	if($files){
		$i=1;
		foreach($files as $x=>$file){
			$file->scenario = 'saveall';
			$file->file_controller = 'coordinator-assessment-script-file';
			?>
			<tr>
			<td><?=$i?>. </td>
			<td>
			<?=Html::activeHiddenInput($file, "[{$x}]id");?>
			<?=Html::activeHiddenInput($model, "id");?>
			<?= $form->field($file, "[{$x}]file_name")->label(false) ?>
			
			</td>
				<td><?=UploadFile::fileInput($file, 'path', false, true, 'material-item')?></td>
			</tr>
			<?php
		$i++;
		
			
		}
	}else{
		echo '<tr><td colspan="3">No Files</td></tr>';
		$applicable = true;
	}
	
	?>
</tbody>
</table>

</div></div>

<?php 
$check_na = $model->na_cont_script == 1 ? 'checked' : ''; 
$check_complete = $model->prg_cont_script == 1 ? 'checked' : ''; 
?>

<?php if(!$applicable){ ?>
<div class="form-group"><label>
<input type="checkbox" id="complete" name="complete" value="1" <?=$check_complete?> /> Mark as complete
</label></div>
<?php } ?>

<?php if($applicable){ ?>
<div class="form-group"><label>
<input type="checkbox" id="na" name="na" value="1" <?=$check_na?> /> Mark as not applicable
</label></div>
<?php } ?>

 <div class="form-group">
  <?=$form->field($model, 'updated_at')->hiddenInput(['value' => time()])->label(false)?>
        <?= Html::submitButton('<span class="glyphicon glyphicon-floppy-disk"></span> Save', ['class' => 'btn btn-success']) ?>
    </div>
	
    <?php ActiveForm::end(); ?>