<?php
/**
 * Created by PhpStorm.
 * User: deserts
 * Date: 2017/11/23
 * Time: 17:10
 */
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
$this->title ='创建';

$this->params['breadcrumbs'][] = ['label' => '文章', 'url' => ['post/index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-lg-9">
        <div class="panel-title box-title">
            <span>创建文章</span>
        </div>
        <div class="panel-body">
            <?php $form = ActiveForm::begin() ?>

            <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'cat_id')-> dropDownList( $cat)?>
            <?= $form->field($model, 'label_img')-> widget('common\widgets\file_upload\FileUpload',[
                'config'=>[]
                ]) ?>
            <?= $form->field($model, 'content')->widget('common\widgets\ueditor\Ueditor',[
                'options'=>[
                    'initialFrameWidth' => 750,
                    'initialFrameHeight' => 500,
//                    'toolbars' =>[]
                ]
            ]) ?>
            <?= $form->field($model, 'tags')->widget('common\widgets\tags\TagWidget') ?>

            <div class="form-group">
                <?=Html::submitButton('发布', ['class' => 'btn btn-success']) ?>
            </div>
            <?php ActiveForm::end() ?>
        </div>
    </div>

    <div class = 'col-lg-3'>
        <div class="panel-title box-title">
            <span>注意事项</span>
        </div>
        <div class="'panel-body">
            <p>
                <pre>
1.用事实说话

2.Less But Better精深原则

3.必须亲自实践进行验证，确保可靠可行

4.突出主题,分清主次逻辑清楚层次分明
                </pre>
            </p>
        </div>
    </div>
</div>


