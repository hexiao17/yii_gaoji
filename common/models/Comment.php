<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "comment".
 *
 * @property int $id
 * @property string $content
 * @property int $status
 * @property int $create_time
 * @property int $userid
 * @property string $email
 * @property string $url
 * @property int $post_id
 * @property int $remind 0:未提醒1：已提醒
 *
 * @property Post $post
 * @property Commentstatus $status0
 * @property User $user
 */
class Comment extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'comment';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['content', 'status', 'userid', 'email', 'post_id'], 'required'],
            [['content'], 'string'],
            [['status', 'create_time', 'userid', 'post_id', 'remind'], 'integer'],
            [['email', 'url'], 'string', 'max' => 128],
            [['post_id'], 'exist', 'skipOnError' => true, 'targetClass' => Post::className(), 'targetAttribute' => ['post_id' => 'id']],
            [['status'], 'exist', 'skipOnError' => true, 'targetClass' => Commentstatus::className(), 'targetAttribute' => ['status' => 'id']],
            [['userid'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['userid' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'content' => '内容',
            'status' => '状态',
            'create_time' => '创建时间',
            'userid' => '作者',
            'email' => '邮箱',
            'url' => 'Url',
            'post_id' => 'Post ID',
            'remind' => 'Remind',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPost()
    {
        return $this->hasOne(Post::className(), ['id' => 'post_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStatus0()
    {
        return $this->hasOne(Commentstatus::className(), ['id' => 'status']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'userid']);
    }
    
    
    private $_Content;
    
    public function getDesc(){
        $tmpStr = strip_tags($this->content);
        $tmplen =mb_strlen($tmpStr);
        $dot = ($tmplen>20)?'...':'';
        return mb_substr($tmpStr, 0,20,'utf-8').$dot;
    }
 
    /**
     * 审核
     * @return boolean
     */
    public function approve() {
        $this->status =2;;  //设置评论状态已审核
        return ($this->save())?true:false;
    }
    /**
     * 返回待审核的条数
     * @return number|string
     */
    public static  function getPengdingCommentCount(){
        return Comment::find()->where(['status'=>1])->count();
    }
    
    public function beforeSave($insert) {
        if(parent::beforeSave($insert)){
            if($insert){
                $this->create_time = time();
            }
            return true;
        }
        else{
            return false;
        }
    }
    
    public static function getStatusArray(){
        return Commentstatus::find()
        ->select(['name','id'])
        ->orderBy('position')
        ->indexBy('id')
        ->column() ;
    }
    /**
     * 最近的回复
     * @param number $limit
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function findRecentComments($limit=10)
    {
        return Comment::find()->where(['status'=>2])->orderBy('create_time DESC')
        ->limit($limit)->all();
    }
    
    
    
    
}
