<?php
/**
 * @link https://github.com/gromver/yii2-cmf.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-grom/blob/master/LICENSE
 * @package yii2-cmf
 * @version 1.0.0
 */

namespace gromver\platform\basic\modules\main\controllers\backend;

use gromver\modulequery\ModuleQuery;
use gromver\platform\basic\modules\main\models\PlatformParams;
use gromver\models\ObjectModel;
use gromver\widgets\ModalIFrame;
use kartik\widgets\Alert;
use yii\caching\Cache;
use yii\di\Instance;
use yii\filters\AccessControl;
use yii\helpers\FileHelper;
use yii\web\Controller;
use Yii;

/**
 * Class DefaultController
 * @package yii2-cmf
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class DefaultController extends Controller
{
    public $layout = '@gromver/platform/basic/views/layouts/backend/main';

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['params', 'flush-cache', 'mode', 'contact-gromver'],  //todo contact-gromver
                        'roles' => ['update'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['index', 'error', 'contact', 'captcha'],
                        'roles' => ['read'],
                    ],
                ]
            ]
        ];
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => 'yii\captcha\CaptchaAction'
        ];
    }

    public function actionIndex()
    {
        return $this->render('index', [
            'items' => ModuleQuery::instance()->implement('\gromver\platform\basic\interfaces\DesktopInterface')->orderBy('desktopOrder')->fetch('getDesktopItem')
        ]);
    }

    public function actionMode($mode, $backUrl = null) {
        $this->module->setMode($mode);

        $this->redirect($backUrl ? $backUrl : Yii::$app->request->getReferrer());
    }

    public function actionParams($modal = null)
    {
        $paramsPath = Yii::getAlias($this->module->paramsPath);
        $paramsFile = $paramsPath . DIRECTORY_SEPARATOR . 'params.php';

        $params = $this->module->params;

        $model = new ObjectModel(PlatformParams::className());
        $model->setAttributes($params);

        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate() && Yii::$app->request->getBodyParam('task') !== 'refresh') {

                FileHelper::createDirectory($paramsPath);
                file_put_contents($paramsFile, '<?php return ' . var_export($model->toArray(), true) . ';');
                @chmod($paramsFile, 0777);

                Yii::$app->session->setFlash(Alert::TYPE_SUCCESS, Yii::t('gromver.platform', 'Configuration saved.'));

                if ($modal) {
                    ModalIFrame::refreshPage();
                }
            }
        }

        if ($modal) {
            $this->layout = 'modal';
        }

        return $this->render('params', [
            'model' => $model
        ]);
    }

    public function actionFlushCache($component = 'cache')
    {
        /** @var Cache $cache */
        $cache = Instance::ensure($component, Cache::className());

        $cache->flush();

        Yii::$app->session->setFlash(Alert::TYPE_SUCCESS, Yii::t('gromver.platform', 'Cache flushed.'));

        return $this->redirect(['index']);
    }

    public function actionContact()
    {
        return $this->render('contact');
    }
}