<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\DocumentoSoporte;

/**
 * DocumentoSoporteSearch represents the model behind the search form of `app\models\DocumentoSoporte`.
 */
class DocumentoSoporteSearch extends DocumentoSoporte
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_documento_soporte', 'idproveedor', 'id_compra', 'numero_soporte', 'id_forma_pago', 'autorizado'], 'integer'],
            [['documento_compra', 'fecha_elaboracion', 'fecha_hora_registro', 'fecha_recepcion_dian', 'fecha_envio_api', 'cuds', 'qrstr', 'user_name'], 'safe'],
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
        $query = DocumentoSoporte::find();

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
            'id_documento_soporte' => $this->id_documento_soporte,
            'idproveedor' => $this->idproveedor,
            'id_compra' => $this->id_compra,
            'fecha_elaboracion' => $this->fecha_elaboracion,
            'fecha_hora_registro' => $this->fecha_hora_registro,
            'fecha_recepcion_dian' => $this->fecha_recepcion_dian,
            'fecha_envio_api' => $this->fecha_envio_api,
            'numero_soporte' => $this->numero_soporte,
            'id_forma_pago' => $this->id_forma_pago,
            'autorizado' => $this->autorizado,
        ]);

        $query->andFilterWhere(['like', 'documento_compra', $this->documento_compra])
            ->andFilterWhere(['like', 'cuds', $this->cuds])
            ->andFilterWhere(['like', 'qrstr', $this->qrstr])
            ->andFilterWhere(['like', 'user_name', $this->user_name]);

        return $dataProvider;
    }
}
