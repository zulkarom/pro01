<?php

namespace backend\modules\courseFiles\models;

use Yii;
use backend\modules\teachingLoad\models\CourseOffered;

/**
 * This is the model class for table "cf_lec_cancel_class".
 *
 * @property int $id
 * @property int $lecture_id
 * @property string $path_file
 */
class CoordinatorRubricsFile extends \yii\db\ActiveRecord
{

    public $file_controller;
    public $path_instance;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cf_coor_rubrics_class';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
			[['offered_id'], 'required'],
			
			[['file_name'], 'required', 'on' => 'saveall'],
			
			[['offered_id', 'updated_at'], 'required', 'on' => 'add_rubrics'],

            
            [['offered_id'], 'integer'],
			
            [['path_file', 'file_name'], 'string'],
			
			[['path_file'], 'required', 'on' => 'path_upload'],
			
			[['path_instance'], 'file', 'skipOnEmpty' => true, 'extensions' => 'pdf', 'maxSize' => 2000000],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'offered_id' => 'Offered ID',
            'path_file' => 'Path File',
        ];
    }
	
	public function flashError(){
        if($this->getErrors()){
            foreach($this->getErrors() as $error){
                if($error){
                    foreach($error as $e){
                        Yii::$app->session->addFlash('error', $e);
                    }
                }
            }
        }

    }

    public function getOffered(){
        return $this->hasOne(CourseOffered::className(), ['id' => 'offered_id']);
    }

   

}