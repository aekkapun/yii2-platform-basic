<?php
/**
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\basic\modules\elasticsearch;

use gromver\modulequery\ModuleQuery;
use gromver\platform\basic\modules\elasticsearch\models\ActiveDocument;
use gromver\platform\basic\interfaces\DesktopInterface;
use gromver\platform\basic\interfaces\MenuItemRoutesInterface;
use gromver\platform\basic\interfaces\MenuRouterInterface;
use gromver\platform\basic\interfaces\BootstrapInterface;
use gromver\platform\basic\modules\elasticsearch\components\MenuRouterSearch;
use Yii;
use yii\base\InvalidConfigException;

/**
 * Class Module
 * В этом модуле можно кастомизировать дополнительные кдассы ActiveDocument поддерживаемые цмской
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class Module extends \yii\base\Module implements BootstrapInterface, DesktopInterface, MenuItemRoutesInterface, MenuRouterInterface
{
    public $controllerNamespace = 'gromver\platform\basic\modules\elasticsearch\controllers';
    public $defaultRoute = 'frontend/default';
    public $desktopOrder = 6;
    public $elasticsearchIndex;
    public $documentClasses = [
        'gromver\platform\basic\modules\page\models\PageElasticSearch',
        'gromver\platform\basic\modules\news\models\PostElasticSearch',
        'gromver\platform\basic\modules\news\models\CategoryElasticSearch',
    ];

    public function init()
    {
        if ($this->elasticsearchIndex) {
            ActiveDocument::$index = $this->elasticsearchIndex;
        } else {
            throw new InvalidConfigException(__CLASS__ . '::elasticsearchIndex must be set.');
        }
    }

    /**
     * @inheritdoc
     */
    public function bootstrap($application)
    {
        $this->documentClasses = array_merge($this->documentClasses, ModuleQuery::instance()->implement('gromver\platform\basic\interfaces\ElasticSearchInterface')->fetch('getElasticSearchDocumentClasses', [], ModuleQuery::AGGREGATE_MERGE));
        ActiveDocument::watch($this->documentClasses);
    }

    /**
     * @inheritdoc
     */
    public function getDesktopItem()
    {
        return [
            'label' => Yii::t('gromver.platform', 'Search'),
            'items' => [
                ['label' => Yii::t('gromver.platform', 'Search'), 'url' => ['/grom/elasticsearch/backend/default/index']],
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function getMenuItemRoutes()
    {
        return [
            'label' => Yii::t('gromver.platform', 'Search'),
            'items' => [
                ['label' => Yii::t('gromver.platform', 'Search'), 'route' =>  'grom/elasticsearch/frontend/default/index'],
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function getMenuRouter()
    {
        return MenuRouterSearch::className();
    }
}