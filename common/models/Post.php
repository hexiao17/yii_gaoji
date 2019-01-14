<?php

namespace common\models;

use Yii;
use yii\helpers\Html;

/**
 * This is the model class for table "post".
 *
 * @property int $id
 * @property string $title
 * @property string $content
 * @property string $tags
 * @property int $status
 * @property int $create_time
 * @property int $update_time
 * @property int $author_id
 *
 * @property Comment[] $comments
 * @property Adminuser $author
 * @property Poststatus $status0
 */
class Post extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'post';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'content', 'status', 'author_id'], 'required'],
            [['content', 'tags'], 'string'],
            [['status', 'create_time', 'update_time', 'author_id'], 'integer'],
            [['title'], 'string', 'max' => 128],
            [['author_id'], 'exist', 'skipOnError' => true, 'targetClass' => Adminuser::className(), 'targetAttribute' => ['author_id' => 'id']],
            [['status'], 'exist', 'skipOnError' => true, 'targetClass' => Poststatus::className(), 'targetAttribute' => ['status' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => '标题',
            'content' => '内容',
            'tags' => '标签',
            'status' => '状态',
            'create_time' => '创建时间',
            'update_time' => '修改时间',
            'author_id' => '作者',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getComments()
    {
        return $this->hasMany(Comment::className(), ['post_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAuthor()
    {
        return $this->hasOne(Adminuser::className(), ['id' => 'author_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStatus0()
    {
        return $this->hasOne(Poststatus::className(), ['id' => 'status']);
    }
    /**
     * 返回文章的链接
     * @return string
     */
    public function getUrl(){
        return \Yii::$app->urlManager->createUrl([
            'post/detail',
            'id'=>$this->id,
            'title'=>$this->title            
        ]);
    }
    /**
            * 摘取文章开头部分
     * @param number $length =288
     * @return string
     */
    public function getBeginning($length=288) {
        //剥去字符串中的 HTML 标签：
        $tmpStr = strip_tags($this->content);
        $tmpLen = mb_strlen($tmpStr);
        
        $tmpStr = mb_substr($tmpStr, 0,$length,'utf-8');
        return $tmpStr.($tmpLen > $length?'...':'');
    }
    /**
     * 返回标签的链接数组
     * @return string[]
     */
    public function getTagLinks() {
        $links = array();
        foreach (Tag::string2array($this->tags) as $tag){
            $links[] = Html::a(Html::encode($tag),array('post/index','PostFrontSearch[tags]'=>$tag));
        }
        return $links;
    }
    /**
     * 返回文章的评论条数
     * @return number|string
     */
    public function getCommentCount() {
       return Comment::find()->where(['post_id'=>$this->id,'status'=>2])->count() ;
    }
    
    
    private $_oldTags;
    /**
     * 返回当前主题的评论，按id 降序
     * @return \yii\db\ActiveQuery
     */
    public function getActiveComments()
    {
        return $this->hasMany(Comment::className(), ['post_id' => 'id'])
        ->where('status=:status',[':status'=>2])->orderBy('id DESC');
    }
    
    
    function beforeSave($insert) {
        
        if(parent::beforeSave($insert)){
            
            if($insert){
                $this->create_time = time();
                $this->update_time = time();
            }else {
                $this->update_time = time();
            }
            
            return true;
            
        }else {
            return false;
        }
        
    }
    
    /**
     * 查找之前初始化保存
     * {@inheritDoc}
     * @see \yii\db\BaseActiveRecord::afterFind()
     */
    public function afterFind() {
        parent::afterFind();
        $this->_oldTags = $this->tags;
    }
    /**
     * 修改或新增文章之后
     * {@inheritDoc}
     * @see \yii\db\BaseActiveRecord::afterSave()
     */
    public function afterSave($insert, $changedAttributes){
        parent::afterSave($insert, $changedAttributes);
        Tag::updateFrequency($this->_oldTags, $this->tags);
    }
    /**
     * 删除文章之后
     * {@inheritDoc}
     * @see \yii\db\BaseActiveRecord::afterDelete()
     */
    public function afterDelete() {
        parent::afterDelete();
        Tag::updateFrequency($this->_oldTags, "");
    }
    
    
}
