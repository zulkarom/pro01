<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\modules\postgrad\models\Student */

$this->title = $model->user->fullname;
$this->params['breadcrumbs'][] = ['label' => 'Postgraduate Students', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="student-post-grad-view">

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

<div class="box">
<div class="box-body">  
  
  
  <div class="row">
	<div class="col-md-6"> <?= DetailView::widget([
        'model' => $model,
        'attributes' => [

            [
                'label' => 'Name',
                'value' => function($model){
                    return $model->user->fullname;
                }
            ],
            'matric_no',
            'nric',
            [
                'label' => 'Emel Pelajar',
                'value' => function($model){
                return $model->user->email;
                }
            ],
            'current_sem',
            'campus.campus_name',
            'program_code',
            [
                'label' => 'Taraf Pengajian',
                'value' => function($model){
                return $model->studyModeText;
                }
            ],
            'admission_year',
            [
                'label' => 'Tahun Kemasukan ',
                'value' => function($model){
                return date('d F Y', strtotime($model->admission_date));
                }
                ],
            [
                'label' => 'Pembiayaan Sendiri',
                'value' => function($model){
                return $model->sponsor;
                }
            ],
            [
                'label' => 'Sesi Masuk',
                'value' => function($model){
                return $model->semester->longFormat();
                }
            ],
            
            [
                'label' => 'Status Pelajar',
                'value' => function($model){
                return $model->stdStatusText;
                }
            ],
            [
                'label' => 'Negara Asal',
                'value' => function($model){
                return $model->country->country_name;
                }
                ],

            
        ],
    ]) ?></div>
	<div class="col-md-6">
	
	
	 <?= DetailView::widget([
        'model' => $model,
        'attributes' => [

            [
                'label' => 'Tarikh Lahir',
                'value' => function($model){
                    return date('d F Y', strtotime($model->date_birth));
                }
            ],
            [
                'label' => 'Jantina',
                'value' => function($model){
                    return $model->genderText;
                }
            ],
            [
                'label' => 'Taraf Perkahwinan',
                'value' => function($model){
                    return $model->maritalText;
                }
            ],
       
            [
                'label' => 'Kewarganegaraan',
                'value' => function($model){
                    return $model->citizenText;
                }
            ],
            
            'address',
            'city',
            'phone_no',
            'personal_email',
           
            [
                'label' => 'Agama',
                'value' => function($model){
                    return $model->religionText;
                }
            ],
            [
                'label' => 'Bangsa',
                'value' => function($model){
                    return $model->raceText;
                }
            ],
            'bachelor_name',
            'bachelor_university',
            'bachelor_cgpa',
            'bachelor_year',

            
        ],
    ]) ?>
	
	 </div>
</div>
  
  
</div>
</div>
</div>