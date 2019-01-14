<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\CommentExtra */

$this->title = 'Create Comment Extra';
$this->params['breadcrumbs'][] = ['label' => 'Comment Extras', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="comment-extra-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
