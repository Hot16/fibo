<?php

namespace app\controllers;

use app\models\PaymentForm;
use moonland\phpexcel\Excel;
use Yii;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
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
     * {@inheritdoc}
     */
    public function actions()
    {
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

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

    /**
     * Display payment page
     *
     * @return string
     */
    public function actionPayment()
    {
        $model = new PaymentForm();

        if ($model->load(Yii::$app->request->post())) {
            Yii::$app->session->setFlash('payment'); //пишем в сессию факт оплаты

            $model->send_payment(Yii::$app->params['adminEmail']); //отправляем письмо

            $model->update(); //пишем в базу

            return $this->refresh();
        }


        $userName = Yii::$app->user->identity->username; //логин авторизовавшегося
        $path = Yii::getAlias('@webroot'); //путь к директории
        $payDataAllList = Excel::import($path.'/files/example_test_01.xlsx'); //файл для импорта данных
        $payDataFirstList = $payDataAllList[0];
        $payDataAllListUsers = ArrayHelper::index($payDataFirstList,'Аккаунт');
        $payDataCurrentUser = ArrayHelper::getValue($payDataAllListUsers, $userName);
//проверяем, что не null и присваиваем значения
        if (!is_null($payDataCurrentUser)) {
            $model->name = $payDataCurrentUser['Аккаунт'];
            $model->currency = $payDataCurrentUser['Валюта'];
            $model->summ = $payDataCurrentUser['Сумма'];
        }

        return $this->render('payment', [
            'name' => $userName,
            'payment' => $payDataCurrentUser,
            'model' => $model
        ]);
    }
}
