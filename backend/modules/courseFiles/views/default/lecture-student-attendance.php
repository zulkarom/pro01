<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use kartik\grid\GridView;
use backend\assets\ExcelAsset;
use kartik\export\ExportMenu;


$offer = $lecture->courseOffered;
$course = $offer->course;
/* @var $this yii\web\View */
/* @var $model backend\modules\teachingLoad\models\CourseOffered */

$this->title = 'Lecture ['.$lecture->lec_name.']';
$this->params['breadcrumbs'][] = ['label' => 'Teaching Assignment', 'url' => ['/course-files/default/teaching-assignment']];
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['teaching-assignment-lecture', 'id' => $lecture->id]];
$this->params['breadcrumbs'][] = 'Student List';
?>

<h4><?=$course->course_code . ' ' . $course->course_name?></h4>
<h4><?=$offer->semester->longFormat()?></h4>
<br />

<div class="form-group"><?= Html::a('Manage Class Date', ['/course-files/default/lecture-student-attendance-date', 'id' => $lecture->id], ['class' => 'btn btn-success']) ?></div>


<div class="box">
        <div class="box-header">
          <div class="a">
            <div class="box-title"><b>Student Attendance</b></div>
          </div>
        </div>
          <div class="box-body">
            <?php $form = ActiveForm::begin() ?>
            <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                  <tr>
                    <th>No.</th>
                    <th>Matric No.</th>
                    <th>Name</th>
                  
                      <?php 
                      $attendance = json_decode($lecture->attendance_header);
                      if($attendance){
                        foreach($attendance as $attend){
                          echo'<th>'. date('d-m', strtotime($attend)) .'</th>';
                        }
                      }
                  echo'</tr>';
                  ?>

                  <?php
                    $i=1;
                    if($model->studentLecture){
                      foreach ($model->studentLecture as $student) {
                        if($student->lecture_id == $lecture->id){
                          echo'<tr><td>'.$i.'</td>
                          <td>'.$student->matric_no.'</td>
                          <td>'.$student->student->st_name.'</td>';

                            $attendance = json_decode($lecture->attendance_header);
                            if($attendance){
                              foreach($attendance as $attend){

                               
                                echo'<td>
                                <input type="hidden" class ="checkbxAtt" name="cbkAttendance" value='.date('d-m', strtotime($attend)).'(0)'.'/>
                                <input type="checkbox" class ="checkbxAtt" name="cbkAttendance" value='.date('d-m', strtotime($attend)).'(1)'.'/></td>';
                              }
                            }

                          $i++;
                        }
                      }
                    }

                  echo'</tr>
                </thead>
              </table>';
              ?>
            </div>
            <?=$form->field($model, 'attendance_json',['options' => ['tag' => false]])->hiddenInput(['value' => ''])->label(false)?>
              <div class="form-group">
                  <br/>
                  <?= Html::submitButton('<span class="glyphicon glyphicon-floppy-disk"></span>  Save', ['class' => 'btn btn-success']) ?>
              </div>

            <?php ActiveForm::end(); ?>
          </div>
        </div>


<?php
$js = "



function arrayChk(){ 
 
    var arrAn = [];  
  
    var m = $('.checkbxAtt'); 
 
    var arrLen = $('.checkbxAtt').length; 
      
    for ( var i= 0; i < arrLen ; i++){  
        var  w = m[i];                     
         if (w.checked == true){  
          arrAn.push( w.value );  
          console.log('Checkbox is checked.' ); 
        }
        if (w.checked == false){
          console.log('Checkbox is unchecked.' );
        }  
      }   
    
    var myJsonString = JSON.stringify(arrAn);  //convert javascript array to JSON string   

    $('#model-attendance_json').val(myJsonString);
   
   }


$('.checkbxAtt ').click(function(e, data){

  arrayChk();
 
   
});


";

$this->registerJs($js);


?>



