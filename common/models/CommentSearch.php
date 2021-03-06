<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
 

/**
 * CommentSearch represents the model behind the search form of `common\models\CommentExtra`.
 */
class CommentSearch extends Comment
{
    /**
     * 
     * {@inheritDoc}
     * @see \yii\db\ActiveRecord::attributes()
     */
    public function attributes(){
        return array_merge(parent::attributes(),['user.username']);
    }
    
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'status', 'create_time', 'userid', 'post_id', 'remind'], 'integer'],
            [['content', 'email', 'url','user.username'], 'safe'], //添加搜索的安全属性authorName
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
        $query = Comment::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
             $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
//             'id' => $this->id,
//             //'status' => $this->status,
//             'create_time' => $this->create_time,
//             'userid' => $this->userid,
//             'post_id' => $this->post_id,
//             'remind' => $this->remind,
              //由于有2个表关联查询，所有这里要改造
              'comment.id'=>$this->id,
                'comment.status' => $this->status,
                'create_time' => $this->create_time,
                'userid' => $this->userid,
                'post_id' => $this->post_id,
                'remind' => $this->remind,
            
        ]);

        $query->andFilterWhere(['like', 'content', $this->content])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'url', $this->url]);
        $query->join("INNER JOIN", 'user','comment.userid = user.id');
        //注意，这里要$this->getAttribute('user.username')
        $query->andFilterWhere(['like','user.username',$this->getAttribute('user.username')]);
        //给该列增加排序功能
        $dataProvider->sort->attributes['user.username']=[
            'asc'=>['user.username'=>SORT_ASC],
            'desc'=>['user.username'=>SORT_DESC],
        ];
        //设置默认排序,把待审核的排在上面
        $dataProvider->sort->defaultOrder=[
            'status'=>SORT_ASC,
            'id'=>SORT_DESC,
        ];
        return $dataProvider;
    }
}
