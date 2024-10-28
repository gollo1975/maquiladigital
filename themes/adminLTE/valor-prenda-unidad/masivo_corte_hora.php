<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use yii\widgets\LinkPager;
use yii\bootstrap\Modal;
use app\models\Ordenproducciontipo;
use yii\helpers\ArrayHelper;
use kartik\date\DatePicker;
use kartik\select2\Select2;
use yii\data\Pagination;
use kartik\depdrop\DepDrop;

/* @var $this yii\web\View */
/* @var $searchModel app\models\FichatiempoSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Crear corte masivo';
$this->params['breadcrumbs'][] = $this->title;
?>

<?php
    $form = ActiveForm::begin([
                "method" => "post",                            
            ]);
    ?>
<div class="table-responsive">
<div class="panel panel-success ">
    <div class="panel-heading">
        Registros <span class="badge"> <?= count($model) ?></span>
    </div>
        <table class="table table-bordered table-hover">
            <thead>
                <tr style ='font-size:85%;'>                
                <th scope="col" style='background-color:#B9D5CE;'>Id</th>
                <th scope="col" style='background-color:#B9D5CE;'>Op interna</th>
                <th scope="col" style='background-color:#B9D5CE;'>Ref.</th>
                <th scope="col" style='background-color:#B9D5CE;'>Cliente</th>
                <th scope="col" style='background-color:#B9D5CE;'>Servicio</th>
                <th scope="col" style='background-color:#B9D5CE;'>Proceso</th>
                <th scope="col" style='background-color:#B9D5CE;'>Planta/Bodega</th>
                <th scope="col" style='background-color:#B9D5CE; width: 120px'>Hora inicio</th>
                <th scope="col" style='background-color:#B9D5CE; width: 120px'>Hora corte</th>
                <th scope="col" style='background-color:#B9D5CE;'></th>
                <th scope="col" style='background-color:#B9D5CE;'></th>
            </tr>
            </thead>
            <tbody>
                <?php 

                foreach ($model as $val):?>
                    <tr style='font-size:85%;'>  
                        <td style='background-color:#DDE6E4;'><?= $val->id_valor ?></td>
                        <td style='background-color:#DDE6E4;'><?= $val->idordenproduccion ?></td>
                        <td style='background-color:#DDE6E4;'><?= $val->ordenproduccion->codigoproducto ?></td>
                        <td style='background-color:#DDE6E4;'><?= $val->ordenproduccion->cliente->nombrecorto ?></td>
                        <td style='background-color:#DDE6E4;'><?= $val->tipo->tipo?></td>
                        <?php if($val->id_proceso_confeccion == 1){?>
                          <td style='background-color:#A1D2D8;'><?= $val->procesoConfeccion->descripcion_proceso ?></td>
                       <?php }else{
                              if($val->id_proceso_confeccion == 2){?>
                                    <td style='background-color:#F1E4F4 ;'><?= $val->procesoConfeccion->descripcion_proceso ?></td> 
                              <?php }else{?> 
                                    <td style='background-color:#F5DB90;'><?= $val->procesoConfeccion->descripcion_proceso ?></td> 
                              <?php } 

                        }?> 
                        <td style='background-color:#DDE6E4;'><?= $val->planta->nombre_planta?></td>
                        <td style="padding-center: 1;padding-right: 1;"><input type="time" name="hora_inicio[]" value="" size="12"></td>  
                        <td style="padding-center: 1;padding-right: 1;"><input type="time" name="hora_corte[]" value="" size="12" ></td>  
                        <input type="hidden" name="listado_pago[]" value="<?= $val->id_valor ?>">
                        <td style="width: 25px; height: 25px;">
                            <!-- Inicio Nuevo Detalle proceso -->
                              <?= Html::a('<span class="glyphicon glyphicon-search"></span> ',
                                  ['/valor-prenda-unidad/ver_corte_hora', 'id_valor' => $val->id_valor],
                                  [
                                      'title' => 'Permite ver las programaciones de corte',
                                      'data-toggle'=>'modal',
                                      'data-target'=>'#modalvercortehora'.$val->id_valor,
                                  ])    
                             ?>
                          <div class="modal remote fade" id="modalvercortehora<?= $val->id_valor ?>">
                              <div class="modal-dialog modal-lg" style ="width: 600px;">
                                  <div class="modal-content"></div>
                              </div>
                          </div>
                        <?php
                        $fechaHoy = date('Y-m-d');
                        $buscar = app\models\ValorPrendaCorteConfeccion::find()->where(['=','id_valor', $val->id_valor])->andWhere(['=','fecha_proceso', $fechaHoy])->one();
                        if($buscar){ ?>
                            <td style= 'width: 25px; height: 25px;'>
                               <a href="<?= Url::toRoute(["valor-prenda-unidad/view_edit_hora", "id_valor" => $val->id_valor]) ?>" ><span class="glyphicon glyphicon-eye-open"></span></a>
                            </td>
                        <?php }else{?>
                            <td style= 'width: 25px; height: 25px;'></td>
                        <?php }?>    
                      </td>

                <?php endforeach; ?>
           </tbody>                               
        </table>    
        <div class="panel-footer text-right" >            
                <?= Html::submitButton("<span class='glyphicon glyphicon-export'></span> Crear corte masivo", ['name' => 'enviar_masivo','class' => 'btn btn-primary btn-sm ']); ?>                
                <?php $form->end() ?>
        </div>
    </div>
</div>

