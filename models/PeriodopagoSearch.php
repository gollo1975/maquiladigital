<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\PeriodoPago;
use app\models\PeriodopagoSearch;

/**
 * PeriodopagoSearch represents the model behind the search form of `app\models\Periodopago`.
 */
class PeriodopagoSearch extends Periodopago
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_periodo_pago', 'dias', 'limite_horas', 'continua'], 'integer'],
            [['nombre_periodo','codigo_api_nomina'], 'safe'],
            [['periodo_mes'], 'number'],
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
        $query = Periodopago::find();

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
            'id_periodo_pago' => $this->id_periodo_pago,
            'dias' => $this->dias,
            'limite_horas' => $this->limite_horas,
            'continua' => $this->continua,
            'periodo_mes' => $this->periodo_mes,
            'codigo_api_nomina' => $this->codigo_api_nomina,
        ]);

        $query->andFilterWhere(['like', 'nombre_periodo', $this->nombre_periodo]);

        return $dataProvider;
    }
}
