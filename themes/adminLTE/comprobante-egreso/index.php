<?php
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ComprobanteEgresoSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Comprobante Egreso';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="comprobante-egreso-index">

    <!--<h1><?= Html::encode($this->title) ?></h1>-->
    <?=  $this->render('_search', ['model' => $searchModel]); ?>

    <?php $newButton = Html::a('Nuevo ' . Html::tag('i', '', ['class' => 'glyphicon glyphicon-plus']), ['create'], ['class' => 'btn btn-success btn-sm']);?>
    <?php $newButton2 = Html::a('Nuevo Libre ' . Html::tag('i', '', ['class' => 'glyphicon glyphicon-plus']), ['createlibre'], ['class' => 'btn btn-success btn-sm']);?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        
        'columns' => [
            [
                'attribute' => 'numero',
                'contentOptions' => ['class' => 'col-lg-1 '],
            ],
            [               
                'attribute' => 'fecha_comprobante',
                'value' => function($model){
                    $comprobante = app\models\ComprobanteEgreso::findOne($model->id_comprobante_egreso);
                    return date("Y-m-d", strtotime("$comprobante->fecha_comprobante"));
                },
                'contentOptions' => ['class' => 'col-lg-1'],
            ],            
            [
                'attribute' => 'id_proveedor',
                'value' => function($model){
                    $proveedores = app\models\Proveedor::findOne($model->id_proveedor);
                    if ($proveedores){return "{$proveedores->nombrecorto} - {$proveedores->cedulanit}";}else{return $model->id_proveedor;}
                    
                },
                'filter' => ArrayHelper::map(app\models\Proveedor::find()->orderBy('nombrecorto ASC')->all(),'idproveedor','nombreProveedores'),
                'contentOptions' => ['class' => 'col-lg-2.8'],
            ],
            [
                'attribute' => 'id_comprobante_egreso_tipo',
                'value' => function($model){
                    $tipos = \app\models\ComprobanteEgresoTipo::findOne($model->id_comprobante_egreso_tipo);
                    return $tipos->concepto;
                },
                'filter' => ArrayHelper::map(\app\models\ComprobanteEgresoTipo::find()->orderBy('concepto ASC')->all(),'id_comprobante_egreso_tipo','concepto'),
                'contentOptions' => ['class' => 'col-lg-2'],
            ],
            [
                'attribute' => 'valor',
                'value' => function($model) {
                    $comprobante = app\models\ComprobanteEgreso::findOne($model->id_comprobante_egreso);
                    $valor = "$ ".number_format($comprobante->valor);
                    return "{$valor}";
                },
                'contentOptions' => ['class' => 'col-lg-1'],
            ],
            [
                'attribute' => 'autorizado',
                'value' => function($model){
                    $comprobante = app\models\ComprobanteEgreso::findOne($model->id_comprobante_egreso);                   
                    return $comprobante->autorizar;
                },
                'filter' => ArrayHelper::map(app\models\ComprobanteEgreso::find()->all(),'autorizado','autorizar'),
                'contentOptions' => ['class' => 'col-lg-1'],
            ],            
            [
                    'class' => 'yii\grid\ActionColumn',              
                ],

            ],
            //'tableOptions' => ['class' => 'table table-success'],
            'tableOptions'=>['class'=>'table table-bordered table-success'],        
            'summary' => '<div class="panel panel-success "><div class="panel-heading">Registros:<span class="badge"> {totalCount}</span></div>',

            'layout' => '{summary}{items}</div><div class="row"><div class="col-sm-8">{pager}</div><div class="col-sm-4 text-right">' . $newButton2 .' ' . $newButton .'</div></div>',
        'pager' => [
            'nextPageLabel' => '<i class="fa fa-forward"></i>',
            'prevPageLabel'  => '<i class="fa fa-backward"></i>',
            'lastPageLabel' => '<i class="fa fa-fast-forward"></i>',
            'firstPageLabel'  => '<i class="fa fa-fast-backward"></i>'
        ],

        ]); ?>
</div>


