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
use app\models\PlantaEmpresa;
use app\models\TiposMaquinas;
use app\models\MarcaMaquinas;


/* @var $this yii\web\View */
/* @var $searchModel app\models\FichatiempoSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Maquinas';
$this->params['breadcrumbs'][] = $this->title;
$fecha_mes = date('Y-m-d');
$mesActual = substr($fecha_mes, 5, 2);
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
$marcas= ArrayHelper::map(MarcaMaquinas::find()->orderBy('descripcion ASC')->all(), 'id_marca', 'descripcion');
$tipos= ArrayHelper::map(TiposMaquinas::find()->orderBy('descripcion ASC')->all(), 'id_tipo', 'descripcion');
$bodegas= ArrayHelper::map(PlantaEmpresa::find()->orderBy('nombre_planta ASC')->all(), 'id_planta', 'nombre_planta');
?>
<div class="panel panel-success panel-filters">
    <div class="panel-heading" onclick="mostrarfiltro()">
        Filtros de busqueda <i class="glyphicon glyphicon-filter"></i>
    </div>
	
    <div class="panel-body" id="filtro" style="display:none">
        <div class="row" >
            <?= $formulario->field($form, "codigo_maquina")->input("search") ?>
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
                'options' => ['prompt' => 'Seleccione la maquina ...'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?>
             <?= $formulario->field($form, 'id_marca')->widget(Select2::classname(), [
                'data' => $marcas,
                'options' => ['prompt' => 'Seleccione la marca ...'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?>
             <?= $formulario->field($form, 'bodega')->widget(Select2::classname(), [
                'data' => $bodegas,
                'options' => ['prompt' => 'Seleccione la bodega ...'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?>
             <?= $formulario->field($form, 'estado')->dropDownList(['0' => 'SI', '1' => 'NO'],['prompt' => 'Seleccione una opcion ...']) ?>
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
                <th scope="col" style='background-color:#B9D5CE;'>Bodega/Planta</th>
                <th scope="col" style='background-color:#B9D5CE;'>Marca</th>
                <th scope="col" style='background-color:#B9D5CE;'>Codigo</th>
                <th scope="col" style='background-color:#B9D5CE;'>Serial</th>
                <th scope="col" style='background-color:#B9D5CE;'>Modelo</th>
                <th scope="col" style='background-color:#B9D5CE;'>Ultimo Mto</th>
                <th scope="col" style='background-color:#B9D5CE;'>Nuevo Mto</th>
                <th scope="col" style='background-color:#B9D5CE;'>Usuario</th>
                 <th scope="col" style='background-color:#B9D5CE;'>Activa</th>
                <th scope="col" style='background-color:#B9D5CE;'></th>
                <th scope="col" style='background-color:#B9D5CE;'></th>
            </tr>
            </thead>
            <tbody>
                <?php
                $fecha_mtto = '';
                foreach ($modelo as $val):
                    $fecha_mtto = substr($val->fecha_nuevo_mantenimiento, 5, 2);
                    if($val->estado_maquina == 0){
                        ?>
                        <tr style='font-size:85%;'>  
                            <td><?= $val->codigo_maquina ?></td>
                            <td><?= $val->tipo->descripcion ?></td>
                            <td><?= $val->planta->nombre_planta ?></td>
                            <td><?= $val->marca->descripcion?></td>
                            <td><?= $val->codigo ?></td>
                            <td><?= $val->serial ?></td>
                            <td><?= $val->modelo ?></td>
                            <td><?= $val->fecha_ultimo_mantenimiento ?></td>
                            <?php if($fecha_mtto === $mesActual){?>
                                  <td style='background-color:#E6C4F9;'><?= $val->fecha_nuevo_mantenimiento ?></td>
                            <?php }else{ ?>      
                                  <td><?= $val->fecha_nuevo_mantenimiento ?></td>
                            <?php } ?>      
                            <td><?= $val->usuario ?></td>
                               <td><?= $val->estadoMaquina ?></td>
                            <td style= 'width: 25px; height: 25px;'>
                            <a href="<?= Url::toRoute(["maquinas/view", "id" => $val->id_maquina, ]) ?>" ><span class="glyphicon glyphicon-eye-open"></span></a>
                            </td>
                            <td style= 'width: 25px; height: 25px;'>
                                    <a href="<?= Url::toRoute(["maquinas/update", "id" => $val->id_maquina, ]) ?>" ><span class="glyphicon glyphicon-pencil"></span></a>
                            </td>
                        </tr>   
                    <?php }else{?>
                         <tr style='font-size:85%;'>  
                            <td style='background-color:#BCD7E5'><?= $val->codigo_maquina ?></td>
                            <td style='background-color:#BCD7E5;'><?= $val->tipo->descripcion ?></td>
                            <td style='background-color:#BCD7E5;'><?= $val->planta->nombre_planta ?></td>
                            <td style='background-color:#BCD7E5;'><?= $val->marca->descripcion?></td>
                            <td style='background-color:#BCD7E5;'><?= $val->codigo ?></td>
                            <td style='background-color:#BCD7E5;'><?= $val->serial ?></td>
                            <td style='background-color:#BCD7E5;'><?= $val->modelo ?></td>
                            <td style='background-color:#BCD7E5;'><?= $val->fecha_ultimo_mantenimiento ?></td>
                            <td style='background-color:#BCD7E5;'><?= $val->fecha_nuevo_mantenimiento ?></td>
                            <td style='background-color:#BCD7E5;'><?= $val->usuario ?></td>
                            <td style='background-color:#BCD7E5;'><?= $val->estadoMaquina ?></td>
                            <td style= 'width: 25px; height: 25px;'>
                            <a href="<?= Url::toRoute(["maquinas/view", "id" => $val->id_maquina, ]) ?>" ><span class="glyphicon glyphicon-eye-open"></span></a>
                            </td>
                            <td style= 'width: 25px; height: 25px;'></td>
                        </tr>   
                            
                    <?php } 
                endforeach; ?>
            </tbody>           
        </table>    
        <div class="panel-footer text-right" >            
                <?= Html::submitButton("<span class='glyphicon glyphicon-export'></span> Exportar excel", ['name' => 'excel','class' => 'btn btn-primary btn-sm ']); ?>                
                <a align="right" href="<?= Url::toRoute("maquinas/create") ?>" class="btn btn-success btn-sm"><span class='glyphicon glyphicon-plus'></span> Nuevo</a> 
            <?php $form->end() ?>
        </div>
    </div>
</div>
<?= LinkPager::widget(['pagination' => $pagination]) ?>

