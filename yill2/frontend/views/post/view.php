
<?php
/**
 * Created by PhpStorm.
 * User: deserts
 * Date: 2017/12/4
 * Time: 16:35
 */
$this->title = $data['title'];
$this->params['breadcrumbs'][] = ['label' => '文章', 'url' =>['post/index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-lg-9">
        <div class="page-title">
            <h1><?=$data['title']?></h1>
            <span>作者：<?=$data['user_name']?> </span>
            <span>发布时间：<?=date('Y-m-d H:m:s',$data['created_at'])?></span>
        </div>
        <?=$data['content']?>
        <div class="page-tag">
            标签:
            <?php foreach ($data['tag'] as $tag) : ?>
                <span class="btn btn-success btn-xs"><a href="#" style="color: whitesmoke" ><?=$tag?></a></span>
            <?php endforeach; ?>

    </div>

    </div>
    <div class="col-lg-3"></div>
</div>
