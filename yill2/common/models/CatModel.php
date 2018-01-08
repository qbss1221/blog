<?php

namespace common\models;

use common\models\base\BaseModel;
use Yii;

/**
 * This is the model class for table "Cats".
 *
 * @property integer $id
 * @property string $cat_name
 */
class CatModel extends BaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'Cats';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['cat_name'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'cat_name' => Yii::t('app', 'Cat Name'),
        ];
    }

    public static function getAllCats()
    {
        $cat = ['0' => Yii::t('common','no cat')];
        $res = self::find() -> asArray() -> all();
//        var_dump($res);exit;
        if($res)
        {
            foreach($res as $k => $list)
            {
                $cat[$list['id']] = $list['cat_name'];
            }
        }
        return $cat;
    }

}
