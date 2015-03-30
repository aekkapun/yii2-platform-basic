<?php
/**
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\basic\modules\search\widgets\events;


use gromver\modulequery\Event;

/**
 * Class SearchableModelsEvent
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 *
 * @property $sender null, инициатор \gromver\platform\basic\modules\search\widgets\SearchResultsFrontend или \gromver\platform\basic\modules\search\widgets\SearchResultsBackend
 */
class SearchableModelsEvent extends Event {
    /**
     * @var string[]
     */
    public $items;
}