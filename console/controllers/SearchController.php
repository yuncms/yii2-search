<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */
namespace yuncms\search\console\controllers;

use Yii;
use yii\db\Query;
use yii\console\Controller;
use yuncms\search\models\Search;

/**
 * Class SearchController
 * @package yuncms\search\console\controllers
 */
class SearchController extends Controller
{
    /**
     * Create Index
     * @param string $model type
     */
    public function actionCreateIndex($model = null)
    {
        $types = Search::getTypes();
        if ($model == null) {
            foreach ($types as $_k => $_v) {
                $this->createIndexForModel($_v);
            }
        } else {
            foreach (Search::getTypes() as $_k => $_v) {
                if ($model == $_k) {
                    $this->createIndexForModel($_v);
                    break;
                }
            }
        }
        $this->stdout("Total station index update successfully!" . PHP_EOL);
    }

    /**
     * Delete all index
     */
    public function actionDelete()
    {
        Yii::$app->db->createCommand()->truncateTable(Search::tableName())->execute();
        $this->stdout("Empty full text index successfully!" . PHP_EOL);
    }

    /**
     * @param string $model 模型名称
     */
    private function createIndexForModel($model)
    {
        $query = (new Query())->from($model['table']);
        //类型ID
        $typeId = $model['id'];
        //每批次获取数量
        $pageSize = 50;
        //计算总数
        $total = $query->count();
        //总批次
        $pages = ceil($total / $pageSize);
        //循环获取
        for ($page = 1; $page <= $pages; $page++) {
            $offset = $pageSize * ($page - 1);
            $result = $query->offset($offset)->limit($pageSize)->all();
            //处理单条数据
            foreach ($result as $r) {
                $fullTextContent = '';
                foreach ($r as $field => $_r) {
                    if ($field == 'id') continue;
                    $fullTextContent .= strip_tags($_r) . ' ';
                }
                $fullTextContent = str_replace("'", '', $fullTextContent);
                Search::updateIndex($typeId, $r['id'], $fullTextContent, '', $r['created_at']);
                $this->stdout("Update " . $model['name'] . " model id:" . $r['id'] . PHP_EOL);
            }
        }
    }
}