<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\SubtipoCotizante;

/**
 * SubtipoCotizanteSearch represents the model behind the search form of `app\models\SubtipoCotizante`.
 */
class SubtipoCotizanteSearch extends SubtipoCotizante
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_subtipo_cotizante'], 'integer'],
            [['subtipo'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
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
        $query = SubtipoCotizante::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id_subtipo_cotizante' => $this->id_subtipo_cotizante,
        ]);

        $query->andFilterWhere(['like', 'subtipo', $this->subtipo]);

        return $dataProvider;
    }
}
