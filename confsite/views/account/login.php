<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Sign Up/Sign In';
?>

<h4>LOGIN OR REGISTER TO SUBMIT OR UPDATE YOUR PAPER.</h4>

<div class="row">
    <div class="col-md-6 col-lg-6"> 
    <?php $form = ActiveForm::begin([
    'validateOnChange' => false
]); ?>

        <h3>LOGIN</h3>
        <br />

        
            <?= $form->field($modelLogin, 'username')->textInput(['class' => 'form-control form-control-lg'])
            ?>
        
          <?= $form->field($modelLogin, 'password')->passwordInput(['class' => 'form-control form-control-lg'])
            ?>

          <div class="form-group">
            <?= Html::submitButton('Log in', ['value' => '1', 'class' => 'btn btn-primary']) ?>
          </div>

          <p>
                <?= Html::a('Forget Password?',
                           ['/site/request-password-reset'],['class' => 'field-label text-muted mb10',]
                                ) ?>
            </p>
        
        
            <?php ActiveForm::end(); ?>
        
    </div>





    <div class="col-md-6 col-lg-6"> 
    <?php $form = ActiveForm::begin([
    'validateOnChange' => false
]); ?>
      <h3>REGISTER</h3>
       <br />
      <?php if(true){?>
 

            <?= $form->field($model, 'fullname')->textInput(['class' => 'form-control form-control-lg'])
            ?>

   <?= $form->field($model, 'email')->textInput(['class' => 'form-control form-control-lg'])?>
            
            
            <?= $form->field($model, 'institution')->textInput(['class' => 'form-control form-control-lg'])
            ?>
        
          <?= $form->field($model, 'password')->passwordInput(['class' => 'form-control form-control-lg'])
            ?>
            <?= $form->field($model, 'password_repeat')->passwordInput(['class' => 'form-control form-control-lg'])
            ?>

          <div class="form-group">
            <?= Html::submitButton('Register', ['value' => '2', 'class' => 'btn btn-primary']) ?>
          </div>
        
        
     
         <?php }else{
			 
			 echo '<p>Kindly be informed that the new registration has been closed </p>';
		 } ?>
      
      <?php ActiveForm::end(); ?>
    </div>
    

</div>