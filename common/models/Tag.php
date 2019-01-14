<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "tag".
 *
 * @property int $id
 * @property string $name
 * @property int $frequency
 */
class Tag extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tag';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['frequency'], 'integer'],
            [['name'], 'string', 'max' => 128],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'frequency' => 'Frequency',
        ];
    }
    
    
    
    
    /**
     *把字符串转换成数组，用正则表达式
     * 分隔符应该有多个：(,|，| |、)?
     *$tags 为字符串
     */
    public static function  string2array($tags) {
        return preg_split('/\s*(,|，| |、)\s*/', trim($tags),-1,PREG_SPLIT_NO_EMPTY) ;
    }
    /**
     * 数组转换成字符串
     * @param  array $arrtags
     * @return string
     */
    public static function array2string($arrtags) {
        return implode(',', $arrtags);
    }
    /**
     * 根据标签数组，到数据库添加标签
     * @param array $tags
     */
    public static function addTags($tags) {
        //为空，就不变
        if(empty($tags))return;
        
        //tags不为空数组
        foreach ($tags as $name){
            $aTag = Tag::find()->where(['name'=>$name])->one();
            $aTagCount = Tag::find()->where(['name'=>$name])->count();
            //如果标签不存在，就新建
            if(!$aTagCount){
                $tag  = new Tag();
                $tag->name = $name;
                $tag->frequency =1;
                $tag->save();
            }
            else {
                //更新
                $aTag->frequency +=1;
                $aTag->save();
            }
        }
    }
    /**
     *  根据数组删除标签
     * @param array $tags
     */
    public static function removeTags($tags) {
        //为空，就不变
        if(empty($tags))return;
        
        //tags不为空数组
        foreach ($tags as $name){
            $aTag = Tag::find()->where(['name'=>$name])->one();
            $aTagCount = Tag::find()->where(['name'=>$name])->count();
            //如果标签不存在，就新建
            if($aTagCount){
                if($aTagCount && $aTag->frequency<=1){
                    $aTag->delete();
                }
                else{
                    $aTag->frequency -=1;
                    $aTag->save();
                }
                
                
            }
        }
    }
    /**
     * 更加新老字符串修改标签库
     * @param string $oldTags
     * @param string $newTags
     */
    public static function updateFrequency($oldTags,$newTags) {
        
        if(!empty($oldTags) || !empty($newTags)){
            $oldTagsArray = self::string2array($oldTags);
            $newTagsArray = self::string2array($newTags);
            //要双向取差，因为他既有增加的部分，也有删除部分
            self::addTags(array_values(array_diff($newTagsArray, $oldTagsArray)));
            self::removeTags(array_values(array_diff($oldTagsArray, $newTagsArray)));
        }
    }
    
    public static function findTagWeights($limit=20) {
        //标签分5个档次
        $tag_size_level = 5;
        $models = Tag::find()->orderBy('frequency desc')->limit($limit)->all();
        $total = Tag::find()->limit($limit)->count();
        
        $stepper = ceil($total/$tag_size_level);
        
        $tags = array();
        $counter = 1;
        
        if($total>0){
            foreach ($models as $model ) {
                $weight = ceil($counter/$stepper)+1;
                $tags[$model->name] = $weight;
                $counter++;
            }
            //按字母排序
            ksort($tags);
        }
        return $tags;
        
    }
    
    
}
