<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Post;

/**
 * PostSearch represents the model behind the search form of `common\models\Post`.
 */
class PostFrontSearch extends Post
{
    /**
     * 添加模型的属性，用于搜索
     * {@inheritDoc}
     * @see \yii\db\ActiveRecord::attributes()
     */
    public function attributes(){
        return array_merge(parent::attributes(),['authorName']);
    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'status', 'create_time', 'update_time', 'author_id'], 'integer'],
            [['title', 'content', 'tags','authorName'], 'safe'], //添加规则
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Post::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination'=>['pageSize'=>6],
            'sort'=>[
                    'defaultOrder'=>[
                        'id'=>SORT_DESC,
                    ],
                    //允许排序的属性
                    'attributes'=>['id','title'],
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
             $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            //id,status 连个表都有，存在二义性，加表名
            'post.id' => $this->id,
            'post.status' => $this->status,
            'create_time' => $this->create_time,
            'update_time' => $this->update_time,
            'author_id' => $this->author_id,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'content', $this->content])
            ->andFilterWhere(['like', 'tags', $this->tags]);
        
        //链接表关系
        $query->join('INNER JOIN','adminuser','post.author_id = adminuser.id');
        //增加查询条件
        $query->andFilterWhere(['like','adminuser.nickname',$this->authorName]);
        //给该列增加排序功能
        $dataProvider->sort->attributes['authorName']=[
            'asc'=>['adminuser.nickname'=>SORT_ASC],
            'desc'=>['adminuser.nickname'=>SORT_DESC],
        ];
            
        return $dataProvider;
    }
}
