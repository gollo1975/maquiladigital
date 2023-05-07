<?php

namespace app\controllers;

use app\models\Ordenproduccion;
use Codeception\Lib\HelperModule;
use yii;
use yii\base\Model;
use yii\web\Controller;
use yii\web\Response;
use yii\web\Session;
use yii\data\Pagination;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Matriculaempresa;
use app\models\Cliente;
use app\models\Municipio;
use app\models\Departamentos;
use app\models\FormCliente;
use yii\helpers\Url;
use app\models\FormFiltroCliente;
use app\models\FormFiltroConsultaCliente;
use yii\web\UploadedFile;
use yii\bootstrap\Modal;
use yii\helpers\ArrayHelper;
use app\models\UsuarioDetalle;

class ClientesController extends Controller {

    public function actionIndex($token = 0) {
        if (Yii::$app->user->identity){
            if (UsuarioDetalle::find()->where(['=','codusuario', Yii::$app->user->identity->codusuario])->andWhere(['=','id_permiso',14])->all()){
                $form = new FormFiltroCliente;
                $cedulanit = null;
                $nombrecorto = null;
                if ($form->load(Yii::$app->request->get())) {
                    if ($form->validate()) {
                        $cedulanit = Html::encode($form->cedulanit);
                        $nombrecorto = Html::encode($form->nombrecorto);
                        $table = Cliente::find()
                                ->andFilterWhere(['like', 'cedulanit', $cedulanit])
                                ->andFilterWhere(['like', 'nombrecorto', $nombrecorto]);
                        $table = $table->orderBy('idcliente desc');
                        $tableexcel = $table->all();
                        $count = clone $table;
                        $to = $count->count();
                        $pages = new Pagination([
                            'pageSize' => 15,
                            'totalCount' => $count->count()
                        ]);
                        $model = $table
                                ->offset($pages->offset)
                                ->limit($pages->limit)
                                    ->all();
                        if(isset($_POST['excel'])){                    
                            $this->actionExcelconsulta($tableexcel);
                        }
                    } else {
                        $form->getErrors();
                    }
                } else {
                    $table = Cliente::find()
                            ->orderBy('idcliente desc');
                    $count = clone $table;
                    $pages = new Pagination([
                        'pageSize' => 15,
                        'totalCount' => $count->count(),
                    ]);
                    $tableexcel = $table->all();
                    $model = $table
                            ->offset($pages->offset)
                            ->limit($pages->limit)
                            ->all();
                    if(isset($_POST['excel'])){                    
                            $this->actionExcelconsulta($tableexcel);
                    }
                }
                $to = $count->count();
                return $this->render('index', [
                            'model' => $model,
                            'form' => $form,
                            'pagination' => $pages,
                            'token' => $token,
                ]);
            }else{
                return $this->redirect(['site/sinpermiso']);
            }
        }else{
            return $this->redirect(['site/login']);
        }    
    }

    public function actionNuevo() {
        $matriculaempresa = Matriculaempresa::findOne(1);
        $model = new FormCliente();
        $msg = null;
        $tipomsg = null;
        if ($model->load(Yii::$app->request->post()) && Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        if ($model->load(Yii::$app->request->post())) {
            $dv = Html::encode($_POST["dv"]);
            if ($model->validate()) {
                $table = new Cliente();
                $table->id_tipo_documento = $model->id_tipo_documento;
                $table->cedulanit = $model->cedulanit;
                $table->razonsocial = $model->razonsocial;
                $table->nombrecliente = $model->nombrecliente;
                $table->apellidocliente = $model->apellidocliente;
                $table->direccioncliente = $model->direccioncliente;
                $table->telefonocliente = $model->telefonocliente;
                $table->celularcliente = $model->celularcliente;
                $table->emailcliente = $model->emailcliente;
                $table->iddepartamento = $model->iddepartamento;
                $table->idmunicipio = $model->idmunicipio;
                $table->contacto = $model->contacto;
                $table->telefonocontacto = $model->telefonocontacto;
                $table->celularcontacto = $model->celularcontacto;
                $table->formapago = $model->formapago;
                $table->plazopago = $model->plazopago;
                $table->nitmatricula = $matriculaempresa->nitmatricula;
                $table->tiporegimen = $model->tiporegimen;
                $table->autoretenedor = $model->autoretenedor;
                $table->retencionfuente = $model->retencionfuente;
                $table->retencioniva = $model->retencioniva;
                $table->observacion = $model->observacion;
                $table->minuto_confeccion = $model->minuto_confeccion;
                $table->minuto_terminacion = $model->minuto_terminacion;
                $table->proceso = $model->proceso;
                $table->dv = $dv;
                if ($model->id_tipo_documento == 1 || $model->id_tipo_documento == 2 || $model->id_tipo_documento == 9 || $model->id_tipo_documento == 4) {
                    $table->nombrecorto = $model->nombrecliente . " " . $model->apellidocliente;
                    $model->razonsocial = null;
                } elseif ($model->id_tipo_documento == 5) {
                    $table->nombrecorto = $model->razonsocial;
                    $model->nombrecliente = null;
                    $model->apellidocliente = null;
                }

                if ($table->insert()) {
                    $this->redirect(["clientes/index"]);
                } else {
                    $msg = "error";
                }
            } else {
                $model->getErrors();
            }
        }
        return $this->render('nuevo', ['model' => $model, 'msg' => $msg, 'tipomsg' => $tipomsg]);
    }

    public function actionEditar($id) {
        $matriculaempresa = Matriculaempresa::findOne(1);
        $model = new FormCliente();
        $msg = null;
        $tipomsg = null;
        if ($model->load(Yii::$app->request->post()) && Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
        if ($model->load(Yii::$app->request->post())) {
            $dv = Html::encode($_POST["dv"]);
            if ($model->validate()) {
                $table = Cliente::find()->where(['idcliente' => $id])->one();
                if ($table) {
                    $table->id_tipo_documento = $model->id_tipo_documento;
                    $table->cedulanit = $model->cedulanit;
                    $table->razonsocial = $model->razonsocial;
                    $table->nombrecliente = $model->nombrecliente;
                    $table->apellidocliente = $model->apellidocliente;
                    $table->direccioncliente = $model->direccioncliente;
                    $table->telefonocliente = $model->telefonocliente;
                    $table->celularcliente = $model->celularcliente;
                    $table->emailcliente = $model->emailcliente;
                    $table->iddepartamento = $model->iddepartamento;
                    $table->idmunicipio = $model->idmunicipio;
                    $table->contacto = $model->contacto;
                    $table->telefonocontacto = $model->telefonocontacto;
                    $table->celularcontacto = $model->celularcontacto;
                    $table->formapago = $model->formapago;
                    $table->plazopago = $model->plazopago;
                    $table->nitmatricula = $matriculaempresa->nitmatricula;
                    $table->tiporegimen = $model->tiporegimen;
                    $table->autoretenedor = $model->autoretenedor;
                    $table->retencionfuente = $model->retencionfuente;
                    $table->retencioniva = $model->retencioniva;
                    $table->observacion = $model->observacion;
                    $table->minuto_confeccion = $model->minuto_confeccion;
                    $table->minuto_terminacion = $model->minuto_terminacion;
                    $table->proceso = $model->proceso;
                    $table->dv = $dv;
                    if ($model->id_tipo_documento == 1 || $model->id_tipo_documento == 2 || $model->id_tipo_documento == 9 || $model->id_tipo_documento == 4) {
                        $table->nombrecorto = strtoupper($model->nombrecliente . " " . $model->apellidocliente);
                        $model->razonsocial = null;
                    } elseif ($model->id_tipo_documento == 5) {
                        $table->nombrecorto = strtoupper($model->razonsocial);
                        $model->nombrecliente = null;
                        $model->apellidocliente = null;
                    }
                    if ($table->update()) {
                        $msg = "El registro ha sido actualizado correctamente";
                       $this->redirect(["clientes/index"]);
                    } else {
                        $this->redirect(["clientes/index"]);
                    }
                } else {
                    $msg = "El registro seleccionado no ha sido encontrado";
                    $tipomsg = "danger";
                }
            } else {
                $model->getErrors();
            }
        }


        if (Yii::$app->request->get("id")) {
            $table = Cliente::find()->where(['idcliente' => $id])->one();
            $municipio = Municipio::find()->Where(['=', 'iddepartamento', $table->iddepartamento])->all();
            $municipio = ArrayHelper::map($municipio, "idmunicipio", "municipio");
            if ($table) {
                $model->id_tipo_documento = $table->id_tipo_documento;
                $model->cedulanit = $table->cedulanit;
                $model->razonsocial = $table->razonsocial;
                $model->nombrecliente = $table->nombrecliente;
                $model->apellidocliente = $table->apellidocliente;
                $model->direccioncliente = $table->direccioncliente;
                $model->telefonocliente = $table->telefonocliente;
                $model->celularcliente = $table->celularcliente;
                $model->emailcliente = $table->emailcliente;
                $model->iddepartamento = $table->iddepartamento;
                $model->idmunicipio = $table->idmunicipio;
                $model->contacto = $table->contacto;
                $model->telefonocontacto = $table->telefonocontacto;
                $model->celularcontacto = $table->celularcontacto;
                $model->formapago = $table->formapago;
                $model->plazopago = $table->plazopago;
                $model->nitmatricula = $table->nitmatricula;
                $model->tiporegimen = $table->tiporegimen;
                $model->autoretenedor = $table->autoretenedor;
                $model->retencionfuente = $table->retencionfuente;
                $model->retencioniva = $table->retencioniva;
                $model->dv = $table->dv;
                $model->observacion = $table->observacion;
                $model->minuto_confeccion = $table->minuto_confeccion;
                $model->minuto_terminacion = $table->minuto_terminacion;
                $model->proceso = $table->proceso;
            } else {
                return $this->redirect(["clientes/index"]);
            }
        } else {
            return $this->redirect(["clientes/index"]);
        }
        return $this->render("editar", ["model" => $model, "msg" => $msg, "tipomsg" => $tipomsg, "municipio" => $municipio]);
    }

    public function actionView($id, $token) {
        // $model = new List();            
        $table = Cliente::find()->where(['idcliente' => $id])->one();
        return $this->render('view', ['table' => $table, 'token' => $token
                
        ]);
    }

    public function actionEliminar($id) {
        if (Yii::$app->request->post()) {
            $cliente = Cliente::findOne($id);
            if ((int) $id) {
                try {
                    Cliente::deleteAll("idcliente=:idcliente", [":idcliente" => $id]);
                    Yii::$app->getSession()->setFlash('success', 'Registro Eliminado.');
                    $this->redirect(["clientes/index"]);
                } catch (IntegrityException $e) {
                    $this->redirect(["clientes/index"]);
                    Yii::$app->getSession()->setFlash('error', 'Error al eliminar el cliente ' . $cliente->cedulanit - $cliente->nombrecorto . ' tiene registros asociados en otros procesos');
                } catch (\Exception $e) {

                    $this->redirect(["clientes/index"]);
                    Yii::$app->getSession()->setFlash('error', 'Error al eliminar el cliente ' . $cliente->cedulanit . '-' . $cliente->nombrecorto . ' tiene registros asociados en otros procesos');
                }
            } else {
                // echo "Ha ocurrido un error al eliminar el cliente, redireccionando ...";
                echo "<meta http-equiv='refresh' content='3; " . Url::toRoute("clientes/index") . "'>";
            }
        } else {
            return $this->redirect(["clientes/index"]);
        }
    }

    public function actionMunicipio($id) {

        $rows = Municipio::find()->where(['iddepartamento' => $id])->all();

        echo "<option required>Seleccione...</option>";
        if (count($rows) > 0) {
            foreach ($rows as $row) {
                echo "<option value='$row->idmunicipio' required>$row->municipio</option>";
            }
        }
    }
    
    public function actionIndexconsulta() {
        if (Yii::$app->user->identity){
        if (UsuarioDetalle::find()->where(['=','codusuario', Yii::$app->user->identity->codusuario])->andWhere(['=','id_permiso',39])->all()){
            $form = new FormFiltroConsultaCliente;
            $cedulanit = null;
            $nombrecorto = null;
            if ($form->load(Yii::$app->request->get())) {
                if ($form->validate()) {
                    $cedulanit = Html::encode($form->cedulanit);
                    $nombrecorto = Html::encode($form->nombrecorto);
                    $table = Cliente::find()
                            ->andFilterWhere(['like', 'cedulanit', $cedulanit])
                            ->andFilterWhere(['like', 'nombrecorto', $nombrecorto])
                            ->orderBy('idcliente desc');
                    $tableexcel = $table->all();
                    $count = clone $table;
                    $to = $count->count();
                    $pages = new Pagination([
                        'pageSize' => 10,
                        'totalCount' => $count->count()
                    ]);
                    $model = $table
                            ->offset($pages->offset)
                            ->limit($pages->limit)
                            ->all();
                    if(isset($_POST['excel'])){
                        //$table = $table->all();
                        $this->actionExcelconsulta($tableexcel);
                    }
                } else {
                    $form->getErrors();
                }
            } else {
                $table = Cliente::find()
                        ->orderBy('idcliente desc');
                $tableexcel = $table->all();
                $count = clone $table;
                $pages = new Pagination([
                    'pageSize' => 10,
                    'totalCount' => $count->count(),
                ]);
                $model = $table
                        ->offset($pages->offset)
                        ->limit($pages->limit)
                        ->all();
                if(isset($_POST['excel'])){
                    //$table = $table->all();
                    $this->actionExcelconsulta($tableexcel);
                }
            }
            $to = $count->count();
            return $this->render('index_consulta', [
                        'model' => $model,
                        'form' => $form,
                        'pagination' => $pages,
            ]);
        }else{
            return $this->redirect(['site/sinpermiso']);
        }
        }else{
            return $this->redirect(['site/login']);
        }
    }
    
    public function actionViewconsulta($id) {
        // $model = new List();            
        $table = Cliente::find()->where(['idcliente' => $id])->one();
        return $this->render('view_consulta', ['table' => $table
        ]);
    }
    
    public function actionExcelconsulta($tableexcel) {                
        $objPHPExcel = new \PHPExcel();
        // Set document properties
        $objPHPExcel->getProperties()->setCreator("EMPRESA")
            ->setLastModifiedBy("EMPRESA")
            ->setTitle("Office 2007 XLSX Test Document")
            ->setSubject("Office 2007 XLSX Test Document")
            ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
            ->setKeywords("office 2007 openxml php")
            ->setCategory("Test result file");
        $objPHPExcel->getDefaultStyle()->getFont()->setName('Arial')->setSize(10);
        $objPHPExcel->getActiveSheet()->getStyle('1')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('R')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('S')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('T')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('U')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('V')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('W')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('X')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('Y')->setAutoSize(true);        
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A1', 'Id')
                    ->setCellValue('B1', 'Tipo')
                    ->setCellValue('C1', 'Fecha')
                    ->setCellValue('D1', 'Cedula/Nit')
                    ->setCellValue('E1', 'Dv')
                    ->setCellValue('F1', 'Razon Social')
                    ->setCellValue('G1', 'Nombres')
                    ->setCellValue('H1', 'Apellidos')
                    ->setCellValue('I1', 'Nombre Completo')
                    ->setCellValue('J1', 'Departamento')
                    ->setCellValue('K1', 'Municipio')
                    ->setCellValue('L1', 'Direccion')
                    ->setCellValue('M1', 'Telefono')  
                    ->setCellValue('N1', 'celular')
                    ->setCellValue('O1', 'Email')
                    ->setCellValue('P1', 'Contacto')
                    ->setCellValue('Q1', 'Telefono Cont')
                    ->setCellValue('R1', 'Celular Cont')                    
                    ->setCellValue('S1', 'Forma Pago')
                    ->setCellValue('T1', 'Plazo Pago')
                    ->setCellValue('U1', 'Tipo Regimen')
                    ->setCellValue('V1', 'Autoretenedor')
                    ->setCellValue('W1', 'Retencion Iva')
                    ->setCellValue('X1', 'Retencion Fuente')
                    ->setCellValue('Y1', 'Observacion');                    
        $i = 2;
        
        foreach ($tableexcel as $val) {
                                  
            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . $i, $val->idcliente)
                    ->setCellValue('B' . $i, $val->tipo->tipo)
                    ->setCellValue('C' . $i, $val->fechaingreso)
                    ->setCellValue('D' . $i, $val->cedulanit)
                    ->setCellValue('E' . $i, $val->dv)
                    ->setCellValue('F' . $i, $val->razonsocial)
                    ->setCellValue('G' . $i, $val->nombrecliente)
                    ->setCellValue('H' . $i, $val->apellidocliente)
                    ->setCellValue('I' . $i, $val->nombrecorto)
                    ->setCellValue('J' . $i, $val->departamento->departamento)
                    ->setCellValue('K' . $i, $val->municipio->municipio)
                    ->setCellValue('L' . $i, $val->direccioncliente)
                    ->setCellValue('M' . $i, $val->telefonocliente)
                    ->setCellValue('N' . $i, $val->celularcliente)
                    ->setCellValue('O' . $i, $val->emailcliente)
                    ->setCellValue('P' . $i, $val->contacto)
                    ->setCellValue('Q' . $i, $val->telefonocontacto)
                    ->setCellValue('R' . $i, $val->celularcontacto)
                    ->setCellValue('S' . $i, $val->formapago)
                    ->setCellValue('T' . $i, $val->plazopago)
                    ->setCellValue('U' . $i, $val->regimen)
                    ->setCellValue('V' . $i, $val->autoretener)
                    ->setCellValue('W' . $i, $val->retenerfuente)
                    ->setCellValue('X' . $i, $val->reteneriva)
                    ->setCellValue('Y' . $i, $val->observacion);
            $i++;
        }

        $objPHPExcel->getActiveSheet()->setTitle('cliente');
        $objPHPExcel->setActiveSheetIndex(0);

        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="cliente.xlsx"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');
        // If you're serving to IE over SSL, then the following may be needed
        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0
        $objWriter = new \PHPExcel_Writer_Excel2007($objPHPExcel);
        $objWriter->save('php://output');
        exit;
    }

}
