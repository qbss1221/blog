<?php
/**
 * Created by PhpStorm.
 * User: deserts
 * Date: 2017/11/23
 * Time: 15:47
 */
namespace frontend\models;
use common\models\RelationPostTagModel;
use yii\base\Model;
use Yii;
use common\models\PostModel;
use yii\db\Query;
use yii\web\NotAcceptableHttpException;

class PostForm extends Model
{
    public $id;
    public $title;
    public $content;
    public $label_img;
    public $cat_id;
    public $tags;

    public $_lastError = "";

    /**
     * 定义场景
     */
    const SCENARIO_CREATE = 'create'; //创建场景
    const SCENARIO_UPDATE = 'update'; //更新场景
    /**
     * 定义事件
     */
    const EVENT_AFTER_CREATE = 'eventAfterCreate';  //创建后事件
    const EVENT_AFTER_UPDATE = 'eventAfterUpdate'; //更新后事件
    /**
     * 场景设置
     * @return array
     */
    public function scenarios()
    {
        $scenarios = [
            self::SCENARIO_CREATE => ['title','content','label_img','cat_id','tags'],
            self::SCENARIO_UPDATE => ['title','content','label_img','cat_id','tags'],
        ];
        return array_merge(parent::scenarios(),$scenarios);
    }

    public function rules()
    {
        return [
            [['id', 'title', 'content', 'cat_id'], 'required'],
            [['id', 'cat_id'], 'integer'],
            ['title', 'string', 'min'=>2, 'max' => 50],
        ];
    }

    public function attributeLabels()
    {
        return [
            'title' => Yii::t('common','Title'),
            'cat_id' => Yii::t('common','Cat_id'),
            'label_img' => Yii::t('common','Label_img'),
            'content' => Yii::t('common','Content'),
            'tags' => Yii::t('common','Tags'),
        ];
    }

    /**
     * 文章创建
     */
    public function create()
    {
        $transaction = Yii::$app->db->beginTransaction();
        try{
            $model = new PostModel();
            $model->setAttributes($this->attributes);
           //var_dump($model->getAttributes());exit;
            $model->summary = $this->_getSummary();//生成摘要
            $model->user_id = Yii::$app->user->identity->id;
            $model->user_name = Yii::$app->user->identity->username;
            $model->is_valid = PostModel::IS_VALID;
            $model->created_at = time();
            $model->updated_at = time();
            if(!$model->save())
                throw new \Exception('文章保存失败!');

            $this->id = $model->id;

            //调用事件
            $data = array_merge($this->getAttributes(), $model->getAttributes());
            $this->_eventAfterCreate($data);

            $transaction->commit();
            return true;
        }catch(\Exception $e){
            $transaction->rollBack();
            $this->_lastError = $e->getMessage();
            return false;
        }
    }

    public function getViewById($id)
    {
        $res = PostModel::find()->with('relate.tag')->where(['id' => $id])->asArray()->one();
        if(!$res)
        {
            throw new NotAcceptableHttpException('文章不存在');
        }
        //处理标签格式
        $res['tag'] = [];
        if(isset($res['relate']) && !empty($res['relate']))
        {
            foreach ($res['relate'] as $list)
            {
                $res['tag'][] = $list['tag']['tag_name'];
            }
        }
        unset($res['relate']);
      //  print_r($res);
        return $res;

    }

//    public  function  getViewById($id)
//    {
//        $res = PostModel::find()->with('relate.tag','extend')->where(['id' => $id])->asArray()->one();
//        if (!$res)
//        {
//            throw new NotFoundHttpException('文章不存在');
//        }
//        $res['tag'] = [];
//        if (isset($res['relate']) && !empty($res['relate']))
//        {
//            foreach ($res['relate'] as $list){
//                $res['tag'][] = $list['tag']['tag_name'];
//            }
//        }
//        unset($res['relate']);
//        return $res;
//    }


    /**
     * 截取文章摘要
     */
    private function _getSummary($s = 0, $e = 90, $char = 'utf-8')
    {
        if (empty($this->content)) {
            return null;
        }
        return mb_substr(str_replace('&nbsp;', '', strip_tags($this->content)), $s, $e, $char);
    }

    /**
     * 创建完成后调用
     */
    public function _eventAfterCreate($data)
    {
        //添加事件
        $this->on(self::EVENT_AFTER_CREATE, [$this, '_eventAddTag'], $data);
       // $this->on(self::EVENT_AFTER_CREATE, [$this, '_eventAddOne'], $data);
        //触发事件
        $this->trigger(self::EVENT_AFTER_CREATE);
    }

    /**
     * 添加标签
     */
    public function _eventAddTag ($event)
    {
        //保存标签
        $tag = new TagForm();
        $tag->tags = $event->data['tags']; //$tag是一个数组保存了所有标签
        $tagids = $tag->saveTags(); //在TagForm表单模型中实现

        //删除原先的关联关系
        RelationPostTagModel::deleteAll(['post_id' => $event->data['id']]);

        //批量保存文章和标签的关联关系
        if (!empty($tagids)){
            foreach ($tagids as $k=>$id){
                $row[$k]['post_id'] = $this->id;
                $row[$k]['tag_id'] = $id;
            }
            //批量插入
            $res = (new Query())->createCommand()
                ->batchInsert(RelationPostTagModel::tableName(),['post_id','tag_id'],$row)
                ->execute();
            if (!$res)
                throw new \yii\base\Exception('关联关系保存失败');
        }

    }

}