<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\postgrad\models\Module */

$this->title = 'Create Module';
$this->params['breadcrumbs'][] = ['label' => 'Modules', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="module-create">


    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
