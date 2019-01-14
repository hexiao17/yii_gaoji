<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\Adminuser;
 

/* @var $this yii\web\View */
/* @var $model common\models\Adminuser */

$model = Adminuser::findOne($id);
$this->title = '权限设置: '.$model->username;
$this->params['breadcrumbs'][] = ['label' => '用户列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->username, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = '权限设置';
?>
<div class="adminuser-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="adminuser-privilege-form">

    <?php $form = ActiveForm::begin(); ?>

   <?=Html::checkboxList('newPri',$authAssignmentArray,$allPrivilegesArray)?>

   
    <div class="form-group">
        <?= Html::submitButton('修改', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

</div>
