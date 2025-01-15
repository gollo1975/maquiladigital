<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\models\Conceptonota;
use app\models\Cliente;
use app\models\Notacredito;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $searchModel app\models\NotacreditoSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Notas Créditos';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="notacredito-index">
<!--<h1><?= Html::encode($this->title) ?></h1>-->
<?=  $this->render('_search', ['model' => $searchModel]); ?>

<?php $newButton = Html::a('Nuevo ' . Html::tag('i', '', ['class' => 'glyphicon glyphicon-plus']), ['create'], ['class' => 'btn btn-success']);?>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => [
        [
            'attribute' => 'numero',
            'contentOptions' => ['class' => 'col-sm-1'],
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
            'attribute' => 'fechapago',
            'contentOptions' => ['class' => 'col-lg-1'],
        ],
        [
            'attribute' => 'valor',
            'value' => function($model) {
                $notascredito = Notacredito::findOne($model->idnotacredito);
                $valor = "".number_format($notascredito->valor);
                return "{$valor}";
            },
            'contentOptions' => ['class' => 'col-lg-1'],
        ],
        [
            'attribute' => 'iva',
            'value' => function($model) {
                $notascredito = Notacredito::findOne($model->idnotacredito);
                $iva = "".number_format($notascredito->iva);
                return "{$iva}";
            },
            'contentOptions' => ['class' => 'col-lg-1'],
        ],
        [
            'attribute' => 'reteiva',
            'value' => function($model) {
                $notascredito = Notacredito::findOne($model->idnotacredito);
                $reteiva = "".number_format($notascredito->reteiva);
                return "{$reteiva}";
            },
            'contentOptions' => ['class' => 'col-lg-1'],
        ],
        [
            'attribute' => 'retefuente',
            'value' => function($model) {
                $notascredito = Notacredito::findOne($model->idnotacredito);
                $retefuente = "".number_format($notascredito->retefuente);
                return "{$retefuente}";
            },
            'contentOptions' => ['class' => 'col-lg-1'],
        ],
        [
            'attribute' => 'total',
            'value' => function($model) {
                $notascredito = Notacredito::findOne($model->idnotacredito);
                $total = ''.number_format($notascredito->total);
                return "{$total}";
            },
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
</div>
