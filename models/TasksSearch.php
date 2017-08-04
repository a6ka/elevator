<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * TasksSearch represents the model behind the search form about `app\models\Tasks`.
 */
class TasksSearch extends Tasks
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'start_floor', 'end_floor', 'direction', 'status_id', 'weight', 'vip'], 'integer'],
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
        $query = Tasks::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'start_floor' => $this->start_floor,
            'end_floor' => $this->end_floor,
            'direction' => $this->direction,
            'status_id' => $this->status_id,
            'weight' => $this->weight,
            'vip' => $this->vip,
        ]);

        $query->orderBy('id');

        return $dataProvider;
    }
}
