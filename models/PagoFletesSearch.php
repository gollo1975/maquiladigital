<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\PagoFletes;

/**
 * PagoFletesSearch represents the model behind the search form of `app\models\PagoFletes`.
 */
class PagoFletesSearch extends PagoFletes
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_pago', 'idproveedor', 'total_pagado', 'numero_pago', 'autorizado', 'proceso_cerrado'], 'integer'],
            [['fecha_pago', 'fecha_registro', 'user_name'], 'safe'],
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
        $query = PagoFletes::find();

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
            'id_pago' => $this->id_pago,
            'idproveedor' => $this->idproveedor,
            'fecha_pago' => $this->fecha_pago,
            'total_pagado' => $this->total_pagado,
            'fecha_registro' => $this->fecha_registro,
            'numero_pago' => $this->numero_pago,
            'autorizado' => $this->autorizado,
            'proceso_cerrado' => $this->proceso_cerrado,
        ]);

        $query->andFilterWhere(['like', 'user_name', $this->user_name]);

        return $dataProvider;
    }
}
