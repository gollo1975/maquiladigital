<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\OrdenProduccionInsumos;

/**
 * OrdenProduccionInsumosSearch represents the model behind the search form of `app\models\OrdenProduccionInsumos`.
 */
class OrdenProduccionInsumosSearch extends OrdenProduccionInsumos
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_entrega', 'idordenproduccion', 'idtipo', 'total_insumos', 'total_costo'], 'integer'],
            [['fecha_hora_generada', 'codigo_producto', 'orden_produccion_cliente', 'user_name', 'fecha_creada'], 'safe'],
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
        $query = OrdenProduccionInsumos::find();

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
            'id_entrega' => $this->id_entrega,
            'idordenproduccion' => $this->idordenproduccion,
            'idtipo' => $this->idtipo,
            'fecha_hora_generada' => $this->fecha_hora_generada,
            'total_insumos' => $this->total_insumos,
            'total_costo' => $this->total_costo,
            'fecha_creada' => $this->fecha_creada,
        ]);

        $query->andFilterWhere(['like', 'codigo_producto', $this->codigo_producto])
            ->andFilterWhere(['like', 'orden_produccion_cliente', $this->orden_produccion_cliente])
            ->andFilterWhere(['like', 'user_name', $this->user_name]);

        return $dataProvider;
    }
}
