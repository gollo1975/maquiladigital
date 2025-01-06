<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Notacredito;

/**
 * NotacreditoSearch represents the model behind the search form of `app\models\Notacredito`.
 */
class NotacreditoSearch extends Notacredito
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['idnotacredito', 'idcliente', 'id_concepto', 'id_documento', 'numero', 'autorizado', 'anulado', 'id_detalle_factura_api'], 'integer'],
            [['fecha', 'fechapago', 'fecha_recepcion_dian', 'fecha_envio_api', 'fecha_factura_venta', 'usuariosistema', 'observacion', 'cufe', 'cude', 'qrstr'], 'safe'],
            [['valor', 'iva', 'reteiva', 'retefuente', 'total'], 'number'],
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
        $query = Notacredito::find();

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
            'idnotacredito' => $this->idnotacredito,
            'idcliente' => $this->idcliente,
            'id_concepto' => $this->id_concepto,
            'fecha' => $this->fecha,
            'fechapago' => $this->fechapago,
            'fecha_recepcion_dian' => $this->fecha_recepcion_dian,
            'fecha_envio_api' => $this->fecha_envio_api,
            'fecha_factura_venta' => $this->fecha_factura_venta,
            'id_documento' => $this->id_documento,
            'valor' => $this->valor,
            'iva' => $this->iva,
            'reteiva' => $this->reteiva,
            'retefuente' => $this->retefuente,
            'total' => $this->total,
            'numero' => $this->numero,
            'autorizado' => $this->autorizado,
            'anulado' => $this->anulado,
            'id_detalle_factura_api' => $this->id_detalle_factura_api,
        ]);

        $query->andFilterWhere(['like', 'usuariosistema', $this->usuariosistema])
            ->andFilterWhere(['like', 'observacion', $this->observacion])
            ->andFilterWhere(['like', 'cufe', $this->cufe])
            ->andFilterWhere(['like', 'cude', $this->cude])
            ->andFilterWhere(['like', 'qrstr', $this->qrstr]);

        return $dataProvider;
    }
}
