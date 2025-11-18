<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use Codeception\Lib\HelperModule;
use yii\base\Model;
use yii\data\Pagination;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use app\models\FormFiltroUsuario;
use app\models\FormRegister;
use app\models\FormEditRegister;
use app\models\FormChangepassword;
use app\models\Users;
use app\models\UsuarioDetalle;
use app\models\User;
use app\models\Permisos;

class SiteController extends Controller {

    /**
     * @inheritdoc
     */
    public function beforeAction($action) {
        if (parent::beforeAction($action)) {
            // change layout for error action
            if ($action->id == 'login')
                $this->layout = 'login';
            return true;
        } else {
            return false;
        }
    }

    public function actions() {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    

    public function actionUsers() {
        //if (!Yii::$app->user->isGuest) {
        $form = new FormFiltroUsuario;
        $nombreusuario = null;
        $documentousuario = null;
        $nombrecompleto = null;
        if ($form->load(Yii::$app->request->get())) {
            if ($form->validate()) {
                $nombreusuario = Html::encode($form->nombreusuario);
                $documentousuario = Html::encode($form->documentousuario);
                $nombrecompleto = Html::encode($form->nombrecompleto);
                $table = Users::find()
                        ->andFilterWhere(['like', 'username', $nombreusuario])
                        ->andFilterWhere(['like', 'documentousuario', $documentousuario])
                        ->andFilterWhere(['like', 'nombrecompleto', $nombrecompleto])
                        ->orderBy('codusuario desc');
                $count = clone $table;
                $to = $count->count();
                $pages = new Pagination([
                    'pageSize' => 30,
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
            $table = Users::find()
                    ->orderBy('codusuario desc');
            $count = clone $table;
            $pages = new Pagination([
                'pageSize' => 30,
                'totalCount' => $count->count(),
            ]);
            $model = $table
                    ->offset($pages->offset)
                    ->limit($pages->limit)
                    ->all();
              //PROCESO QUE ACTIVA O DESACTIVA EL ESTADO DEL USUARIO
        }
       
        $to = $count->count();
        return $this->render('users', [
                    'model' => $model,
                    'form' => $form,
                    'pagination' => $pages,
        ]);
        /* }else{
          return $this->redirect(["site/login"]);
          } */
    }

    public function actionView($id) {
        $usuariodetalles = UsuarioDetalle::find()->where(['=','codusuario',$id])->all();
        if (Yii::$app->user->identity->role == 2) {
            if (isset($_POST["eliminar"])) {
                if (isset($_POST["codusuario_detalle"])) {
                    foreach ($_POST["codusuario_detalle"] as $intCodigo) {                        
                        $eliminar = UsuarioDetalle::findOne($intCodigo);
                        $eliminar->delete();
                        $this->redirect(["view", 'id' => $id]);
                    }
                    //$this->redirect(["producto/view", 'id' => $id]);
                }
            } 
            return $this->render('user', [
                        'model' => $this->findModel($id),
                        'usuariodetalles' => $usuariodetalles
            ]);
        } else {
            return $this->render('usersimple', [
                        'model' => $this->findModel($id),
            ]);
        }
    }
    
    public function actionNewpermiso($id) {
        $permisos = Permisos::find()->orderBy('modulo,menu_operacion,permiso asc')->all();
        $mensaje = "";
        if(Yii::$app->request->post()) {
            if (isset($_POST["idpermiso"])) {
                $intIndice = 0;
                foreach ($_POST["idpermiso"] as $intCodigo) {
                    $permiso = Permisos::findOne($intCodigo);
                    $userspermisos = new UsuarioDetalle();                    
                    $userspermisosdetalle = UsuarioDetalle::find()
                            ->where(['=','codusuario',$id])
                            ->andWhere(['=','id_permiso',$intCodigo])
                            ->all();                    
                    $reg = count($userspermisosdetalle);
                    if ($reg == 0) {
                        $userspermisos->id_permiso = $intCodigo;                        
                        $userspermisos->codusuario = $id;
                        $userspermisos->activo = 0;                        
                        $userspermisos->insert();                        
                    }
                    $intIndice++;
                }
                $this->redirect(["site/view",'id' => $id]);
            }else{
                $mensaje = "Debe seleccionar al menos un registro";
            }
        }

        return $this->render('newpermiso', [
            'permisos' => $permisos,            
            'mensaje' => $mensaje,
            'id' => $id,

        ]);
    }
      
    
    public function actionSinpermiso() {        
        Yii::$app->getSession()->setFlash('danger', 'No tiene permiso para acceder a este menú, comuniquese con el administrador');
        return $this->render('sinpermiso', [                    
        ]);        
    }

    protected function findModel($id) {
        if (($model = Users::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionUser() {
        return $this->render('user');
    }

    public function actionAdmin() {
        return $this->render('admin');
    }

    private function randKey($str = '', $long = 0) {
        $key = null;
        $str = str_split($str);
        $start = 0;
        $limit = count($str) - 1;
        for ($x = 0; $x < $long; $x++) {
            $key .= $str[rand($start, $limit)];
        }
        return $key;
    }

    /* public function actionConfirm() //confirmar en el correo la activacion
      {
      $table = new Users;
      if (Yii::$app->request->get())
      {

      //Obtenemos el valor de los parámetros get
      $id = Html::encode($_GET["id"]);
      $authKey = $_GET["authKey"];

      if ((int) $id)
      {
      //Realizamos la consulta para obtener el registro
      $model = $table
      ->find()
      ->where("id=:id", [":id" => $id])
      ->andWhere("authKey=:authKey", [":authKey" => $authKey]);

      //Si el registro existe
      if ($model->count() == 1)
      {
      $activar = Users::findOne($id);
      $activar->activate = 1;
      if ($activar->update())
      {
      echo "Enhorabuena registro llevado a cabo correctamente, redireccionando ...";
      echo "<meta http-equiv='refresh' content='8; ".Url::toRoute("site/login")."'>";
      }
      else
      {
      echo "Ha ocurrido un error al realizar el registro, redireccionando ...";
      echo "<meta http-equiv='refresh' content='8; ".Url::toRoute("site/login")."'>";
      }
      }
      else //Si no existe redireccionamos a login
      {
      return $this->redirect(["site/login"]);
      }
      }
      else //Si id no es un número entero redireccionamos a login
      {
      return $this->redirect(["site/login"]);
      }
      }
      } */

    public function actionRegister() {
        //Creamos la instancia con el model de validación
        $model = new FormRegister;

        //Mostrará un mensaje en la vista cuando el usuario se haya registrado
        $msg = null;

        //Validación mediante ajax
        if ($model->load(Yii::$app->request->post()) && Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        //Validación cuando el formulario es enviado vía post
        //Esto sucede cuando la validación ajax se ha llevado a cabo correctamente
        //También previene por si el usuario tiene desactivado javascript y la
        //validación mediante ajax no puede ser llevada a cabo
        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate()) {
                //Preparamos la consulta para guardar el usuario
                $table = new Users();
                $table->username = $model->username;
                $table->nombrecompleto = $model->nombrecompleto;
                $table->role = $model->role;
                $table->documentousuario = $model->documentousuario;
                $table->emailusuario = $model->emailusuario;
                //Encriptamos el password
                $table->password = crypt($model->password, Yii::$app->params["salt"]);
                //Creamos una cookie para autenticar al usuario cuando decida recordar la sesión, esta misma
                //clave será utilizada para activar el usuario
                $table->authKey = $this->randKey("abcdef0123456789", 200);
                //Creamos un token de acceso único para el usuario
                $table->accessToken = $this->randKey("abcdef0123456789", 200);
                $table->activo = 1;
                $table->id_planta = $model->id_planta;
                 $table->id_agente = $model->id_agente;

                //Si el registro es guardado correctamente
                if ($table->save()) {
                    $msg = "Registro realizado correctamente";
                    return $this->redirect(['users']);
                } else {
                    $msg = "Ha ocurrido un error al llevar a cabo tu registro";
                }
            } else {
                $model->getErrors();
            }
        }
        return $this->render("register", ["model" => $model, "msg" => $msg]);
    }

    public function actionEditar($id) {
        $model = new FormEditRegister;
        $msg = null;

        if ($model->load(Yii::$app->request->post()) && Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
        if ($model->load(Yii::$app->request->post())) {

            if ($model->validate()) {
                $table = Users::find()->where(['codusuario' => $id])->one();
                if ($table) {
                    $table->username = $model->username;
                    $table->nombrecompleto = $model->nombrecompleto;
                    $table->role = $model->role;
                    $table->documentousuario = $model->documentousuario;
                    $table->emailusuario = $model->emailusuario;
                    $table->activo = $model->activo;
                    $table->id_planta = $model->id_planta;
                    $table->id_agente = $model->id_agente;
                    if ($table->save()) {
                        $msg = "El registro ha sido actualizado correctamente";
                        return $this->redirect(["site/users"]);
                    } else {
                        $msg = "El registro no sufrio ningun cambio";
                        return $this->redirect(["site/users"]);
                    }
                } else {
                    $msg = "El registro seleccionado no ha sido encontrado";
                }
            } else {
                $model->getErrors();
            }
        }

        if (Yii::$app->request->get("id")) {
            $table = Users::find()->where(['codusuario' => $id])->one();

            if ($table) {
                $model->username = $table->username;
                $model->nombrecompleto = $table->nombrecompleto;
                $model->role = $table->role;
                $model->documentousuario = $table->documentousuario;
                $model->emailusuario = $table->emailusuario;
                $model->activo = $table->activo;
                $model->id_planta = $table->id_planta;
                 $model->id_agente = $table->id_agente;
            } else {
                return $this->redirect(["site/users"]);
            }
        } else {
            return $this->redirect(["site/users"]);
        }
        return $this->render("editregister", ["model" => $model, "msg" => $msg]);
    }

    public function actionChangepassword($id) {
        $model = new FormChangepassword;
        $msg = null;

        if ($model->load(Yii::$app->request->post()) && Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate()) {
                $table = Users::find()->where(['codusuario' => $id])->one();
                if ($table) {
                    //Encriptamos el password
                    $table->password = crypt($model->password, Yii::$app->params["salt"]);
                    //Creamos una cookie para autenticar al usuario cuando decida recordar la sesión, esta misma
                    //clave será utilizada para activar el usuario
                    $table->authKey = $this->randKey("abcdef0123456789", 200);
                    //Creamos un token de acceso único para el usuario
                    $table->accessToken = $this->randKey("abcdef0123456789", 200);
                    if ($table->save()) {
                        $msg = "El registro ha sido actualizado correctamente";
                        if($table->role == 2){
                             return $this->redirect(["site/users"]);
                        }else{
                             return $this->redirect(["site/view",'id' => $id]);
                        }
                    } else {
                        $msg = "El registro no sufrio ningun cambio";
                        return $this->redirect(["site/users"]);
                    }
                } else {
                    $msg = "El registro seleccionado no ha sido encontrado";
                }
            } else {
                $model->getErrors();
            }
        }

        return $this->render("changepassword", ["model" => $model, "msg" => $msg]);
    }

    public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex() {

        return $this->render('index');
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin() {
        if (!Yii::$app->user->isGuest) {
            return $this->redirect(["site/main-login"]); //$this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('main-login', [
                    'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout() {
        Yii::$app->user->logout();

        //return $this->goHome();
        return $this->redirect(["site/login"]);
    }

}
