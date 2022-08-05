<?php
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

/* @var $this yii\web\View */
/* @var $searchModel app\models\FichatiempoSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Maquinas';
$this->params['breadcrumbs'][] = $this->title;
?>
<script language="JavaScript">
    function mostrarfiltro() {
        divC = document.getElementById("filtro");
        if (divC.style.display == "none"){divC.style.display = "block";}else{divC.style.display = "none";}
    }
</script>

<?php $formulario = ActiveForm::begin([
    "method" => "get",
    "action" => Url::toRoute("maquinas/index"),
    "enableClientValidation" => true,
    'options' => ['class' => 'form-horizontal'],
    'fieldConfig' => [
                    'template' => '{label}<div class="col-sm-4 form-group">{input}{error}</div>',
                    'labelOptions' => ['class' => 'col-sm-2 control-label'],
                    'options' => []
                ],

]);
$marcas= ArrayHelper::map(\app\models\MarcaMaquinas::find()->orderBy('descripcion ASC')->all(), 'id_marca', 'descripcion');
$tipos= ArrayHelper::map(\app\models\TiposMaquinas::find()->orderBy('descripcion ASC')->all(), 'id_tipo', 'descripcion');
?>
<div class="panel panel-success panel-filters">
    <div class="panel-heading" onclick="mostrarfiltro()">
        Filtros de busqueda <i class="glyphicon glyphicon-filter"></i>
    </div>
	
    <div class="panel-body" id="filtro" style="display:none">
        <div class="row" >
            <?= $formulario->field($form, "codigo")->input("search") ?>
              <?= $formulario->field($form, "modelo")->input("search") ?>
               <?= $formulario->field($form, 'fecha_desde')->widget(DatePicker::className(), ['name' => 'check_issue_date',
                'value' => date('d-M-Y', strtotime('+2 days')),
                'options' => ['placeholder' => 'Seleccione una fecha ...'],
                'pluginOptions' => [
                    'format' => 'yyyy-m-d',
                    'todayHighlight' => true]])
            ?>
               <?= $formulario->field($form, 'fecha_corte')->widget(DatePicker::className(), ['name' => 'check_issue_date',
                'value' => date('d-M-Y', strtotime('+2 days')),
                'options' => ['placeholder' => 'Seleccione una fecha ...'],
                'pluginOptions' => [
                    'format' => 'yyyy-m-d',
                    'todayHighlight' => true]])
            ?>
             <?= $formulario->field($form, 'id_tipo')->widget(Select2::classname(), [
                'data' => $tipos,
                'options' => ['prompt' => 'Seleccione un cliente ...'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?>
             <?= $formulario->field($form, 'id_marca')->widget(Select2::classname(), [
                'data' => $marcas,
                'options' => ['prompt' => 'Seleccione un cliente ...'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?>
        </div>
        <div class="panel-footer text-right">
            <?= Html::submitButton("<span class='glyphicon glyphicon-search'></span> Buscar", ["class" => "btn btn-primary btn-sm",]) ?>
            <a align="right" href="<?= Url::toRoute("maquinas/index") ?>" class="btn btn-primary btn-sm"><span class='glyphicon glyphicon-refresh'></span> Actualizar</a>
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
        Registros <span class="badge"> <?= number_format($pagination->totalCount,0) ?></span>
    </div>
        <table class="table table-bordered table-hover">
            <thead>
                <tr style ='font-size:85%;'>                
                <th scope="col" style='background-color:#B9D5CE;'>Nro</th>    
                <th scope="col" style='background-color:#B9D5CE;'>Tipo de maquina</th>
                  <th scope="col" style='background-color:#B9D5CE;'>Marca</th>
                <th scope="col" style='background-color:#B9D5CE;'>Codigo</th>
                <th scope="col" style='background-color:#B9D5CE;'>Serial</th>
                <th scope="col" style='background-color:#B9D5CE;'>Modelo</th>
                <th scope="col" style='background-color:#B9D5CE;'>Ultimo Mto</th>
                <th scope="col" style='background-color:#B9D5CE;'>Nuevo Mto</th>
                <th scope="col" style='background-color:#B9D5CE;'>Usuario</th>
                <th scope="col" style='background-color:#B9D5CE;'></th>
                <th scope="col" style='background-color:#B9D5CE;'></th>
            </tr>
            </thead>
            <tbody>
                <?php 
                foreach ($modelo as $val):?>
                    <tr style='font-size:85%;'>  
                        <td><?= $val->codigo_maquina ?></td>
                         <td><?= $val->tipo->descripcion ?></td>
                        <td><?= $val->marca->descripcion?></td>
                        <td><?= $val->codigo ?></td>
                        <td><?= $val->serial ?></td>
                        <td><?= $val->modelo ?></td>
                        <td><?= $val->fecha_ultimo_mantenimiento ?></td>
                        <td><?= $val->fecha_nuevo_mantenimiento ?></td>
                        <td><?= $val->usuario ?></td>
                        <td style= 'width: 25px; height: 25px;'>
                        <a href="<?= Url::toRoute(["maquinas/view", "id" => $val->id_maquina, ]) ?>" ><span class="glyphicon glyphicon-eye-open"></span></a>
                        </td>
                        <td style= 'width: 25px; height: 25px;'>
                                <a href="<?= Url::toRoute(["maquinas/update", "id" => $val->id_maquina, ]) ?>" ><span class="glyphicon glyphicon-pencil"></span></a>
                        </td>
                <?php
                endforeach; ?>
            </tbody>           
        </table>    
        <div class="panel-footer text-right" >            
                <?= Html::submitButton("<span class='glyphicon glyphicon-export'></span> Excel", ['name' => 'excel','class' => 'btn btn-primary btn-sm ']); ?>                
                <a align="right" href="<?= Url::toRoute("maquinas/create") ?>" class="btn btn-success btn-sm"><span class='glyphicon glyphicon-plus'></span> Nuevo</a> 
            <?php $form->end() ?>
        </div>
    </div>
</div>
<?= LinkPager::widget(['pagination' => $pagination]) ?>

