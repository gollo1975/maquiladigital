<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\AsignacionProducto;

/**
 * AsignacionProductoSearch represents the model behind the search form of `app\models\AsignacionProducto`.
 */
class AsignacionProductoSearch extends AsignacionProducto
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_asignacion', 'idcliente', 'unidades', 'idtipo', 'orden_produccion', 'autorizado', 'total_orden'], 'integer'],
            [['documento', 'razonzocial', 'fecha_asignacion', 'fecha_registro', 'usuario'], 'safe'],
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
        $query = AsignacionProducto::find();

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
            'id_asignacion' => $this->id_asignacion,
            'idcliente' => $this->idcliente,
            'fecha_asignacion' => $this->fecha_asignacion,
            'fecha_registro' => $this->fecha_registro,
            'unidades' => $this->unidades,
            'idtipo' => $this->idtipo,
            'orden_produccion' => $this->orden_produccion,
            'autorizado' => $this->autorizado,
            'total_orden' => $this->total_orden,
        ]);

        $query->andFilterWhere(['like', 'documento', $this->documento])
            ->andFilterWhere(['like', 'razonzocial', $this->razonzocial])
            ->andFilterWhere(['like', 'usuario', $this->usuario]);

        return $dataProvider;
    }
}
