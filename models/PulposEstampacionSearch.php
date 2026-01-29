<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\PulposEstampacion;

/**
 * PulposEstampacionSearch represents the model behind the search form of `app\models\PulposEstampacion`.
 */
class PulposEstampacionSearch extends PulposEstampacion
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_pulpo', 'cantidad_brazos'], 'integer'],
            [['descripcion', 'fecha_registro'], 'safe'],
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
        $query = PulposEstampacion::find();

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
            'id_pulpo' => $this->id_pulpo,
            'cantidad_brazos' => $this->cantidad_brazos,
            'fecha_registro' => $this->fecha_registro,
        ]);

        $query->andFilterWhere(['like', 'descripcion', $this->descripcion]);

        return $dataProvider;
    }
}
