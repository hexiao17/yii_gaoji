<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\Poststatus;
use yii\helpers\ArrayHelper;
 
/* @var $this yii\web\View */
/* @var $model common\models\Post */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="post-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'content')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'tags')->textarea(['rows' => 6]) ?>
	<?php 
	//第一种方法
	$psObjs = Poststatus::find()->all();
	$allStatus = ArrayHelper::map($psObjs, 'id', 'name');
	//第二种
	$psArray = Yii::$app->db->createCommand("select id,name from poststatus")->queryAll();
	$allStatus = ArrayHelper::map($psArray, 'id', 'name');
	?>
    <?= $form->field($model, 'status')->dropDownList($allStatus,
                            ['prompt'=>'请选择状态']    
     );
    
     
    //第三种方法
    //$allStatus = (new \yii\db\Query())
    $allStatus =  Poststatus::find()
            ->select(['name','id'])  //name为第一列了
           // ->from('poststatus')
            ->indexBy('id') //按id重新建立索引
            ->column();    //取得第一列的值
//     echo "<hr/><pre>";
//     print_r($allStatus);
//     echo "<hr/></pre>";
//     exit(0);   
    ?>

    <?= $form->field($model, 'create_time')->textInput() ?>

    <?= $form->field($model, 'update_time')->textInput() ?>

    <?= $form->field($model, 'author_id')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
