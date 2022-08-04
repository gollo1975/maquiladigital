<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Maquinas;

/**
 * MaquinasSearch represents the model behind the search form of `app\models\Maquinas`.
 */
class MaquinasSearch extends Maquinas
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_maquina', 'id_tipo', 'id_marca'], 'integer'],
            [['codigo', 'serial', 'modelo', 'fecha_compra', 'usuario', 'fecha_registro'], 'safe'],
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
        $query = Maquinas::find();

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
            'id_maquina' => $this->id_maquina,
            'id_tipo' => $this->id_tipo,
            'id_marca' => $this->id_marca,
            'fecha_compra' => $this->fecha_compra,
            'fecha_registro' => $this->fecha_registro,
        ]);

        $query->andFilterWhere(['like', 'codigo', $this->codigo])
            ->andFilterWhere(['like', 'serial', $this->serial])
            ->andFilterWhere(['like', 'modelo', $this->modelo])
            ->andFilterWhere(['like', 'usuario', $this->usuario]);

        return $dataProvider;
    }
}
