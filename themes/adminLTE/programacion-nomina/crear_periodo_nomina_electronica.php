  <?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\ContactForm */
use app\models\Cliente;

//clases
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use yii\widgets\LinkPager;
use yii\bootstrap\Modal;
use yii\helpers\ArrayHelper;
use kartik\date\DatePicker;
use kartik\select2\Select2;
use yii\data\Pagination;
use kartik\depdrop\DepDrop;
//Modelos...


$this->title = 'Periodo nomina electronica';
$this->params['breadcrumbs'][] = $this->title;

?>
<script language="JavaScript">
    function mostrarfiltro() {
        divC = document.getElementById("filtro");
        if (divC.style.display == "none"){divC.style.display = "block";}else{divC.style.display = "none";}
    }
</script>

<!--<h1>Lista Facturas</h1>-->
<?php $formulario = ActiveForm::begin([
    "method" => "get",
    "action" => Url::toRoute("programacion-nomina/documento_electronico"),
    "enableClientValidation" => true,
    'options' => ['class' => 'form-horizontal'],
    'fieldConfig' => [
                    'template' => '{label}<div class="col-sm-4 form-group">{input}{error}</div>',
                    'labelOptions' => ['class' => 'col-sm-2 control-label'],
                    'options' => []
                ],

]);

$cliente = ArrayHelper::map(Cliente::find()->orderBy('nombrecorto ASC')->all(), 'idcliente', 'nombrecorto');
?>
<div class="panel panel-success panel-filters">
    <div class="panel-heading" onclick="mostrarfiltro()">
        Filtros de busqueda <i class="glyphicon glyphicon-filter"></i>
    </div>
	
    <div class="panel-body" id="filtro" style="display:block">
        <div class="row" >
             
            <?= $formulario->field($form, 'desde')->widget(DatePicker::className(), ['name' => 'check_issue_date',
                'value' => date('d-M-Y', strtotime('+2 days')),
                'options' => ['placeholder' => 'Seleccione una fecha ...'],
                'pluginOptions' => [
                    'format' => 'yyyy-m-d',
                    'todayHighlight' => true]])
            ?>
            <?= $formulario->field($form, 'hasta')->widget(DatePicker::className(), ['name' => 'check_issue_date',
                'value' => date('d-M-Y', strtotime('+2 days')),
                'options' => ['placeholder' => 'Seleccione una fecha ...'],
                'pluginOptions' => [
                    'format' => 'yyyy-m-d',
                    'todayHighlight' => true]])
            ?>    
        
        </div>
        <div class="panel-footer text-right">
            <?= Html::submitButton("<span class='glyphicon glyphicon-search'></span> Buscar", ["class" => "btn btn-primary btn-sm",]) ?>
            <a align="right" href="<?= Url::toRoute("programacion-nomina/documento_electronico") ?>" class="btn btn-primary btn-sm"><span class='glyphicon glyphicon-refresh'></span> Actualizar</a>
        </div>
    </div>
</div>

<?php $formulario->end() ?>
<?php
$form = ActiveForm::begin([
                "method" => "post",                            
            ]);
    ?>
<div class="table-responsive">
<div class="panel panel-success ">
    <div class="panel-heading">
          Registros  <span class="badge"> <?= $pagination->totalCount ?></span>
    </div>
        <table class="table table-bordered table-hover">
            <thead>
                <tr style ='font-size: 85%;'>         
                <th scope="col" style='background-color:#B9D5CE;'>Codigo</th>
                <th scope="col" style='background-color:#B9D5CE;'>Fecha inicio</th>
                <th scope="col" style='background-color:#B9D5CE;'>Fecha corte</th>
                <th scope="col" style='background-color:#B9D5CE;'>No empleados</th>
                <th scope="col" style='background-color:#B9D5CE;'>Fecha hora creacion</th>
                <th scope="col" style='background-color:#B9D5CE;'></th>                          
            </tr>
            </thead>
            <tbody>
            <?php foreach ($model as $val):?>
            <tr style ='font-size: 85%;'>                
                <td><?= $val->id_periodo_electronico ?></td>
                <td><?= $val->fecha_inicio_periodo ?></td>
                <td><?= $val->fecha_corte_periodo ?></td>
                <td><?= $val->cantidad_empleados ?></td>
                <td><?= $val->fecha_registro ?></td>
                <td style= 'width: 25px; height: 25px;'>
                    <?= Html::a('<span class="glyphicon glyphicon-user"></span> ', ['cargar_empleados_nomina', 'id_periodo' => $val->id_periodo_electronico, 'fecha_inicio' => $val->fecha_inicio_periodo,'fecha_corte' => $val->fecha_corte_periodo], [
                                           'class' => '',
                                           'title' => 'Proceso que permite cargar los empleados para generar el documento electronico.', 
                                           'data' => [
                                               'confirm' => 'Esta seguro de CARGAR los empleados que generaron NOMINA desde ('.$val->fecha_inicio_periodo.') hasta el ('.$val->fecha_corte_periodo.')'.'',
                                               'method' => 'post',
                                           ],
                    ])?>
                </td>   
                        
            </tr>            
            <?php endforeach; ?>
            </tbody>    
        </table> 
        <div class="panel-footer text-right" >            
              <!-- Inicio Nuevo Detalle proceso -->
                <?= Html::a('<span class="glyphicon glyphicon-plus"></span> Crear documento',
                    ['/programacion-nomina/crear_nuevo_documento'],
                    [
                        'title' => 'Permite generar el periodo de envio',
                        'data-toggle'=>'modal',
                        'data-target'=>'#modalcrearnuevodocumento',
                        'class' => 'btn btn-info btn-xs'
                    ])    
                    ?>
             <div class="modal remote fade" id="modalcrearnuevodocumento">
                     <div class="modal-dialog modal-lg-centered">
                         <div class="modal-content"></div>
                     </div>
             </div>
          
        </div>
     </div>
     <?php $form->end() ?>
</div>
<?= LinkPager::widget(['pagination' => $pagination]) ?>

