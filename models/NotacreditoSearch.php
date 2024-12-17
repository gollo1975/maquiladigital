<?php

namespace app\models;

use Yii;
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
            [['idnotacredito', 'idcliente', 'id_documento', 'numero', 'autorizado', 'anulado'], 'integer'],
            [['fecha', 'fechapago', 'usuariosistema', 'observacion'], 'safe'],
            [['valor','iva','reteiva','retefuente','total'], 'number'],
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
            'sort'=> ['defaultOrder' => ['idnotacredito' => SORT_DESC]] // Agregar esta linea para agregar el orden por defecto
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
            'fecha' => $this->fecha,
            'fechapago' => $this->fechapago,
            'id_documento' => $this->id_documento,
            'valor' => $this->valor,
            'iva' => $this->iva,
            'reteiva' => $this->reteiva,
            'retefuente' => $this->retefuente,
            'total' => $this->total,
            'numero' => $this->numero,
            'autorizado' => $this->autorizado,
            'anulado' => $this->anulado,
        ]);

        $query->andFilterWhere(['like', 'usuariosistema', $this->usuariosistema])
            ->andFilterWhere(['like', 'observacion', $this->observacion]);

        return $dataProvider;
    }
}
