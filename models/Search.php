<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */
namespace yuncms\search\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

/**
 * 全文索引表
 *
 * @property int $id 自增ID
 * @property int $type_id 类型ID
 * @property int $model_id 模型ID
 * @property string $data 数据
 * @property int $created_at 创建时间
 *
 * @package common\models
 */
class Search extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%search}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at']
                ],
            ]
        ];
    }

    /**
     * 返回索引类型
     * @return array
     */
    public static function getTypes()
    {
        return [
            'code' => [
                'id' => 1,
                'name' => 'Code',
                'table' => '{{%item}}',
                'modelClass' => 'common\models\Code'
            ],
            'question' => [
                'id' => 2,
                'name' => 'question',
                'table' => '{{%question_question}}',
                'modelClass' => 'common\models\Question',
            ],
        ];
    }

    /**
     * 更新索引
     * @param int $type_id 类型ID
     * @param int $modelId 模型ID
     * @param string $content 全文
     * @param string $title 不分词的文本
     * @param int $created_at
     */
    public static function updateIndex($type_id, $modelId, $content, $title, $created_at)
    {
        //分词结果
        $fullTextData = pullword($content);
        if (is_array($fullTextData)) {
            $fullTextData = implode(' ', $fullTextData);
        } else {
            $fullTextData = $content;
        }
        $fullTextData = $title . ' ' . $fullTextData;

        if (self::find()->where(['type_id' => $type_id, 'model_id' => $modelId])->exists()) {
            self::getDb()->createCommand()->update(
                self::tableName(),
                ['data' => $fullTextData, 'created_at' => $created_at,],
                ['type_id' => $type_id, 'model_id' => $modelId]
            )->execute();
        } else {
            self::getDb()->createCommand()->insert(self::tableName(),
                [
                    'type_id' => $type_id,
                    'model_id' => $modelId,
                    'data' => $fullTextData,
                    'created_at' => $created_at,
                ])->execute();
        }
    }
}