<?php

namespace app\models;

use yii\base\Model;
use yii\data\ArrayDataProvider;
use Yii;

class ValorPrendaUnidadDetallesSearch extends Model
{
    public $id_planta;
    public $fecha;
    public $id_operario;

    public function rules()
    {
        return [
            [['id_planta', 'id_operario'], 'integer'],
            [['fecha'], 'safe'],
        ];
    }

    public function searchDashboard($params)
    {
        $this->load($params);

        // Instanciar la consulta base de Query Builder
        $query = (new \yii\db\Query())
            ->select([
                'o.id_operario AS id_operario',
                'o.nombrecompleto AS operario_nombre', // Ajusta 'nombre' al campo real de tu tabla operarios
                'pe.nombre_planta AS planta_nombre',  // Ajusta 'nombre' al campo real de planta_empresa
                'SUM(vpud.cantidad) AS total_cantidad', // Ajusta según tu campo de piezas/prendas hechas
                
                // Cálculo de Eficiencia: (Cantidad Real Producida / Meta Esperada) * 100
                // Nota: Ajusta 'meta_unidades' si el campo está en ordenproduccion o en otra tabla
                'ROUND(SUM(vpud.porcentaje_cumplimiento) / COUNT(vpud.consecutivo)) AS eficiencia',
                
                // Valor Facturado: Lo que la empresa cobra por prenda
                'SUM(vpud.total_valor_venta) AS valor_facturado',
                
                // Valor Ganado: Lo que el operario gana por prenda
                'SUM(vpud.vlr_pago) AS valor_ganado'
            ])
            ->from('valor_prenda_unidad_detalles vpud')
            ->innerJoin('operarios o', 'vpud.id_operario = o.id_operario')
            ->innerJoin('planta_empresa pe', 'vpud.id_planta = pe.id_planta') // Ajusta la clave foránea si es directa en operarios o en detalles
            ->innerJoin('ordenproduccion op', 'vpud.idordenproduccion = op.idordenproduccion')
            ->andWhere(['=', 'vpud.tipo_aplicacion', 1]) 
            ->groupBy(['o.id_operario', 'o.nombrecompleto', 'pe.nombre_planta']);

        // Aplicar los filtros seleccionados en la interfaz
        if ($this->id_planta) {
            $query->andWhere(['pe.id_planta' => $this->id_planta]);
        }
        if ($this->fecha) {
            // Suponiendo que tienes un campo 'fecha' en valor_prenda_unidad_detalles
            $query->andWhere(['DATE(vpud.dia_pago)' => $this->fecha]);
        }
        if ($this->id_operario) {
            $query->andWhere(['vpud.id_operario' => $this->id_operario]);
        }

        $resultados = $query->all();

        // Calcular los totales globales para los paneles de control (KPIs)
        $totalFacturado = array_sum(array_column($resultados, 'valor_facturado'));
        $totalGanado = array_sum(array_column($resultados, 'valor_ganado'));
        $promedioEficiencia = count($resultados) > 0 ? array_sum(array_column($resultados, 'eficiencia')) / count($resultados) : 0;

        // Estructura de datos para enviar a Chart.js
        $chartData = [
            'labels' => array_column($resultados, 'nombrecompleto'),
            'eficiencia' => array_column($resultados, 'eficiencia'),
        ];

        $dataProvider = new ArrayDataProvider([
            'allModels' => $resultados,
            'pagination' => [
                'pageSize' => 15,
            ],
        ]);

        return [
            'dataProvider' => $dataProvider,
            'kpis' => [
                'total_facturado' => $totalFacturado,
                'total_ganado' => $totalGanado,
                'eficiencia_promedio' => round($promedioEficiencia, 2),
                'operarios_activos' => count($resultados)
            ],
            'chartData' => $chartData
        ];
    }
}