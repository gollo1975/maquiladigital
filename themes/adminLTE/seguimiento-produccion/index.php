<?php
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use app\models\Cliente;

/* @var $this yii\web\View */
/* @var $searchModel app\models\SeguimientoProduccionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Lista Seguimientos Produccion';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="seguimiento-produccion-index-index">

    <!--<h1><?= Html::encode($this->title) ?></h1>-->
    <?=  $this->render('_search', ['model' => $searchModel]); ?>

    <?php $newButton = Html::a('Nuevo ' . Html::tag('i', '', ['class' => 'glyphicon glyphicon-plus']), ['create'], ['class' => 'btn btn-success']);?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        
        'columns' => [
            [                
                'attribute' => 'id_seguimiento_produccion',
                'contentOptions' => ['class' => 'col-lg-1'],
            ],
            [                
                'attribute' => 'fecha_inicio_produccion',
                'contentOptions' => ['class' => 'col-lg-1'],                
            ],
            [               
                'attribute' => 'hora_inicio',
                'contentOptions' => ['class' => 'col-lg-1 '],                
            ],
            [
                'attribute' => 'idcliente',
                'value' => function($model){
                    $clientes = Cliente::findOne($model->idcliente);
                    return "{$clientes->nombrecorto} - {$clientes->cedulanit}";
                },
                'filter' => ArrayHelper::map(Cliente::find()->all(),'idcliente','nombreClientes'),
                'contentOptions' => ['class' => 'col-lg-3'],
            ],
            [               
                'attribute' => 'idordenproduccion',
                'contentOptions' => ['class' => 'col-lg-1 '],                
            ],
            [               
                'attribute' => 'codigoproducto',
                'contentOptions' => ['class' => 'col-lg-1 '],                
            ],
            [               
                'attribute' => 'ordenproduccionint',
                'contentOptions' => ['class' => 'col-lg-1 '],                
            ],
            [               
                'attribute' => 'ordenproduccionext',
                'contentOptions' => ['class' => 'col-lg-1 '],                
            ],
            [
                'attribute' => 'estado',
                'value' => function($model){
                    $ficha = \app\models\SeguimientoProduccion::findOne($model->id_seguimiento_produccion);                    
                    return $ficha->cerrado;
                },
                'filter' => ArrayHelper::map(\app\models\SeguimientoProduccion::find()->all(),'estado','cerrado'),
                'contentOptions' => ['class' => 'col-lg-1'],
            ],
            [
                'class' => 'yii\grid\ActionColumn',              
            ],
			
        ],
        //'tableOptions' => ['class' => 'table table-success'],
        'tableOptions'=>['class'=>'table table-bordered table-success'],        
        'summary' => '<div class="panel panel-success "><div class="panel-heading">Registros: {totalCount}</div>',

        'layout' => '{summary}{items}</div><div class="row"><div class="col-sm-8">{pager}</div><div class="col-sm-4 text-right">' . $newButton . '</div></div>',
        'pager' => [
            'nextPageLabel' => '<i class="fa fa-forward"></i>',
            'prevPageLabel'  => '<i class="fa fa-backward"></i>',
            'lastPageLabel' => '<i class="fa fa-fast-forward"></i>',
            'firstPageLabel'  => '<i class="fa fa-fast-backward"></i>'
        ],
        
    ]); ?>
</div>


