<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\helpers\ArrayHelper;
use app\models\Proveedor;
use app\models\Compra;
use app\models\CompraTipo;
use app\models\CompraConcepto;

/* @var $this yii\web\View */
/* @var $searchModel app\models\CompraSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Compras';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="compras-index">

    <!--<h1><?= Html::encode($this->title) ?></h1>-->
    <?=  $this->render('_search', ['model' => $searchModel]); ?>

    <?php $newButton = Html::a('Nuevo ' . Html::tag('i', '', ['class' => 'glyphicon glyphicon-plus']), ['create'], ['class' => 'btn btn-success btn-sm']);?>
    <?php Pjax::begin() ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,        
        'columns' => [                  
            [               
                'attribute' => 'numero',
                'contentOptions' => ['class' => 'col-lg-1 '],
            ],
            [
                'attribute' => 'id_compra_concepto',
                'value' => function($model){
                    $compraconcepto = CompraConcepto::findOne($model->id_compra_concepto);
                    return $compraconcepto->concepto;
                },
                'filter' => ArrayHelper::map(CompraConcepto::find()->all(),'id_compra_concepto','concepto'),
                'contentOptions' => ['class' => 'col-lg-1'],
            ],
            [               
                'attribute' => 'factura',
                'contentOptions' => ['class' => 'col-lg-1 '],
            ],            
            [
                'attribute' => 'id_proveedor',
                'value' => function($model){
                    $proveedores = Proveedor::findOne($model->id_proveedor);
                    return "{$proveedores->nombrecorto} - {$proveedores->cedulanit}";
                },
                'filter' => ArrayHelper::map(Proveedor::find()->all(),'idproveedor','nombreProveedores'),
                'contentOptions' => ['class' => 'col-lg-2'],
            ],            
            
            [               
            'attribute' => 'fechavencimiento',
            'value' => function($model){
                $compra = Compra::findOne($model->id_compra);
                return date("Y-m-d", strtotime("$compra->fechavencimiento"));
            },
            'contentOptions' => ['class' => 'col-lg-1'],
            ],        
           
            [
                'label' => 'Total',
                'attribute' => 'total',
                'value' => function($model) {
                    $compra = Compra::findOne($model->id_compra);
                    $total = "$ ".number_format($compra->total);
                    return "{$total}";
                },
                'contentOptions' => ['class' => 'col-lg-1'],
            ],
             [
                'attribute' => 'saldo',
                'value' => function($model) {
                    $compra = Compra::findOne($model->id_compra);
                    $saldo = "$ ".number_format($compra->saldo);
                    return "{$saldo}";
                },
                'contentOptions' => ['class' => 'col-lg-1'],
            ],
            [
                'attribute' => 'autorizado',
                'value' => function($model){
                    $compra = Compra::findOne($model->id_compra);                   
                    return $compra->autorizar;
                },
                'filter' => ArrayHelper::map(Compra::find()->all(),'autorizado','autorizar'),
                'contentOptions' => ['class' => 'col-lg-1'],
            ],
            [
                'attribute' => 'estado',
                'value' => function($model){
                    $compra = Compra::findOne($model->id_compra);                    
                    return $compra->estados;
                },
                'filter' => ArrayHelper::map(Compra::find()->all(),'estado','estados'),
                'contentOptions' => ['class' => 'col-lg-1'],
            ],                     
            [
                'class' => 'yii\grid\ActionColumn',
                'contentOptions' => ['class' => 'col-lg-1'],                
            ],
			
        ],
        'tableOptions' => ['class' => 'table table-bordered table-success'],
        'summary' => '<div class="panel panel-success "><div class="panel-heading">Registros: {totalCount}</div>',

        'layout' => '{summary}{items}</div><div class="row"><div class="col-sm-8">{pager}</div><div class="col-sm-4 text-right">' . $newButton . '</div></div>',
        'pager' => [
            'nextPageLabel' => '<i class="fa fa-forward"></i>',
            'prevPageLabel'  => '<i class="fa fa-backward"></i>',
            'lastPageLabel' => '<i class="fa fa-fast-forward"></i>',
            'firstPageLabel'  => '<i class="fa fa-fast-backward"></i>'
        ],
        
    ]); ?>
    <?php Pjax::end(); ?>
</div>
