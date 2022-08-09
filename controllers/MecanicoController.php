<?php

namespace app\controllers;


use app\models\Mecanico;
use app\models\MecanicoSearch;
use app\models\TipoDocumento;
use app\models\UsuarioDetalle;
use app\models\FormFiltroMecanico;
// aplicacion de yii
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\db\ActiveQuery;
use yii\base\Model;
use yii\web\Response;
use yii\web\Session;
use yii\data\Pagination;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\web\UploadedFile;
use yii\bootstrap\Modal;
use yii\helpers\ArrayHelper;
use Codeception\Lib\HelperModule;


/**
 * AbogadosController implements the CRUD actions for Abogados model.
 */
class MecanicoController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Abogados models.
     * @return mixed
     */
      public function actionIndex() {
        if (Yii::$app->user->identity){
            if (UsuarioDetalle::find()->where(['=','codusuario', Yii::$app->user->identity->codusuario])->andWhere(['=','id_permiso',121])->all()){
                $form = new FormFiltroMecanico();
                $documento = null;
                $nombre_completo = null;
                if ($form->load(Yii::$app->request->get())) {
                    if ($form->validate()) {
                        $documento = Html::encode($form->documento);
                        $nombre_completo = Html::encode($form->nombre_completo);
                        $table = Mecanico::find()
                                ->andFilterWhere(['=', 'documento', $documento])
                                ->andFilterWhere(['like', 'nombre_completo', $nombre_completo])
                                ->orderBy('documento desc');
                        $count = clone $table;
                        $to = $count->count();
                        $pages = new Pagination([
                            'pageSize' => 12,
                            'totalCount' => $count->count()
                        ]);
                        $model = $table
                                ->offset($pages->offset)
                                ->limit($pages->limit)
                                ->all();
                    } else {
                        $form->getErrors();
                    }
                } else {
                    $table = Mecanico::find()
                            ->orderBy('documento desc');
                    $count = clone $table;
                    $pages = new Pagination([
                        'pageSize' => 12,
                        'totalCount' => $count->count(),
                    ]);
                    $model = $table
                            ->offset($pages->offset)
                            ->limit($pages->limit)
                            ->all();
                }
                $to = $count->count();
                return $this->render('index', [
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

    /**
     * Displays a single Abogados model.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'table' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Abogados model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($token = 0)
    {
        
        $model = new Mecanico();
        if ($model->load(Yii::$app->request->post()) && Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate()) {
                $table = new Mecanico();
                $table->documento = $model->documento;
                $table->id_tipo_documento = $model->id_tipo_documento;
                $table->nombres = $model->nombres;
                $table->apellidos = $model->apellidos;
                $table->nombre_completo = strtoupper($model->nombres . " " . $model->apellidos);
                $table->direccion_mecanico = $model->direccion_mecanico;
                $table->celular = $model->celular;
                $table->email_mecanico = $model->email_mecanico;
                $table->iddepartamento = $model->iddepartamento;
                $table->idmunicipio = $model->idmunicipio;
                $table->usuario = Yii::$app->user->identity->username;
                $table->observacion = $model->observacion;
                $table->save(false);
                return $this->redirect(['index']);
            }else{
                $model->getErrors();
            }    
        }

        return $this->render('create', [
            'model' => $model,
            'token' => $token,
        ]);
    }

    /**
     * Updates an existing Abogados model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id, $token = 1)
    {
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post()) && Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
        if ($model->load(Yii::$app->request->post())) {
            if($model->validate()){
                $table = Mecanico::findOne($id);
                $table->id_tipo_documento = $model->id_tipo_documento;
                 $table->documento = $model->documento;
                $table->nombres = $model->nombres;
                $table->apellidos = $model->apellidos;
                $table->nombre_completo = strtoupper($model->nombres . " " . $model->apellidos);
                $table->direccion_mecanico = $model->direccion_mecanico;
                $table->celular = $model->celular;
                $table->email_mecanico = $model->email_mecanico;
                $table->iddepartamento = $model->iddepartamento;
                $table->idmunicipio = $model->idmunicipio;
                $table->estado = $model->estado;
                $table->observacion = $model->observacion;
                $table->save(false);
                return $this->redirect(['index']);
            }else{
             $model->getErrors();      
            }
        }
        if (Yii::$app->request->get("id")) {
            $table = Mecanico::find()->where(['id_mecanico' =>$id])->one();
            $municipio = \app\models\Municipio::find()->Where(['=', 'iddepartamento', $table->iddepartamento])->all();
            $municipio = ArrayHelper::map($municipio, "idmunicipio", "municipio");
            $model->nombres = $table->nombres;
            $model->apellidos = $table->apellidos;
            $model->direccion_mecanico = $table->direccion_mecanico;
            $model->celular = $table->celular;
            $model->email_mecanico = $table->email_mecanico;
            $model->iddepartamento = $table->iddepartamento;
            $model->idmunicipio = $table->idmunicipio;
            $model->estado = $table->estado;
            $model->observacion = $table->observacion;
        }
        return $this->render('update', [
            'model' => $model,
            'token' => $token,
            'municipio' => $municipio,

        ]);
    }

    /**
     * Deletes an existing Abogados model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
     public function actionEliminar($id) {
        if (Yii::$app->request->post()) {
            $mecanico = Mecanico::findOne($id);
            if ((int) $id) {
                try {
                    Mecanico::deleteAll("id_mecanico=:id_mecanico", [":id_mecanico" => $id]);
                    Yii::$app->getSession()->setFlash('success', 'Registro Eliminado.');
                    $this->redirect(["mecanico/index"]);
                } catch (IntegrityException $e) {
                    $this->redirect(["mecanico/index"]);
                    Yii::$app->getSession()->setFlash('error', 'No se puede eliminar el mecanico ' . $mecanico->nombre_completo . ', tiene registros asociados en otros procesos');
                } catch (\Exception $e) {

                    $this->redirect(["mecanico/index"]);
                    Yii::$app->getSession()->setFlash('error', 'No se puede eliminar el mecanico ' . $mecanico->nombre_completo . ', tiene registros asociados en otros procesos');
                }
            } else {
                // echo "Ha ocurrido un error al eliminar el cliente, redireccionando ...";
                echo "<meta http-equiv='refresh' content='3; " . Url::toRoute("mecanico/index") . "'>";
            }
        } else {
            return $this->redirect(["mecanico/index"]);
        }
    }
    
    /**
     * Finds the Abogados model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Abogados the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Mecanico::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
