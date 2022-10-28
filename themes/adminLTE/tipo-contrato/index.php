<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\models\TipoContrato ;
use app\models\ConfiguracionFormatoPrefijo;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $searchModel app\models\TipoReciboSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Tipos de Contratos';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tipo-contrato-index">

    <!--<h1><?= Html::encode($this->title) ?></h1>-->
    <?= $this->render('_search', ['model' => $searchModel]); ?>

    <?php $newButton = Html::a('Nuevo ' . Html::tag('i', '', ['class' => 'glyphicon glyphicon-plus']), ['create'], ['class' => 'btn btn-success btn-sm']); ?>

    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute' => 'id_tipo_contrato',
                'contentOptions' => ['class' => 'col-lg-1'],
            ],
            [
                'attribute' => 'contrato',
                'contentOptions' => ['class' => 'col-lg-3'],
            ],
            [
                'attribute' => 'nro_prorrogas',
                'contentOptions' => ['class' => 'col-lg-1'],
            ],
           
            [
                'attribute' => 'prorroga',
                'value' => function($model) {
                    $prorrogacontrato = TipoContrato::findOne($model->id_tipo_contrato);
                    return $prorrogacontrato->prorrogaContrato;
                },
                'filter' => ArrayHelper::map(TipoContrato::find()->all(), 'prorroga', 'prorrogaContrato'),
                'contentOptions' => ['class' => 'col-lg-2'],
            ],
            [
                'attribute' => 'id_configuracion_prefijo',
                'value' => function($model) {
                    $tipo_formato = ConfiguracionFormatoPrefijo::findOne($model->id_configuracion_prefijo);
                    return $tipo_formato->formato;
                },
                'filter' => ArrayHelper::map(ConfiguracionFormatoPrefijo::find()->all(), 'id_configuracion_prefijo', 'formato'),
                'contentOptions' => ['class' => 'col-lg-2'],
            ],

            [
                'attribute' => 'prefijo',
                'contentOptions' => ['class' => 'col-lg-1'],
            ],
                        
             [
                'attribute' => 'estado',
                'value' => function($model) {
                    $orden = TipoContrato::findOne($model->id_tipo_contrato);
                    return $orden->activo;
                },
                'filter' => ArrayHelper::map(TipoContrato::find()->all(), 'estado', 'activo'),
                'contentOptions' => ['class' => 'col-lg-1'],
            ],
                        
            [
                'class' => 'yii\grid\ActionColumn',
                 'contentOptions' => ['class' => 'col-lg-1 '],
            ],
        ],
        'tableOptions' => ['class' => 'table table-bordered table-success'],
        'summary' => '<div class="panel panel-success "><div class="panel-heading">Registros: {totalCount}</div>',
        'layout' => '{summary}{items}</div><div class="row"><div class="col-sm-8">{pager}</div><div class="col-sm-4 text-right">' . $newButton . '</div></div>',
        'pager' => [
            'nextPageLabel' => '<i class="fa fa-forward"></i>',
            'prevPageLabel' => '<i class="fa fa-backward"></i>',
            'lastPageLabel' => '<i class="fa fa-fast-forward"></i>',
            'firstPageLabel' => '<i class="fa fa-fast-backward"></i>',
        ],
    ]);
    ?>
</div>