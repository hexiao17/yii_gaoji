<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\CommentExtra;

/* @var $this yii\web\View */
/* @var $model common\models\CommentExtra */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="comment-extra-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'content')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'status')->dropDownList(CommentExtra::getStatusArray(),['prompt'=>'请选择状态'])?>
 
    <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'url')->textInput(['maxlength' => true]) ?> 
  
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord?'创建':"修改", ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
