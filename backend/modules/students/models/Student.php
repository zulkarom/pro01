<?php

namespace backend\modules\students\models;

use Yii;
use backend\modules\courseFiles\models\StudentLecture;

/**
 * This is the model class for table "st_student".
 *
 * @property int $id
 * @property string $matric_no
 * @property string $nric
 * @property string $st_name
 * @property string $program
 */
class Student extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'st_student';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['matric_no', 'st_name'], 'required'],
            [['matric_no', 'nric'], 'string', 'max' => 20],
            [['st_name'], 'string', 'max' => 100],
            [['program'], 'string', 'max' => 10],
            [['matric_no'], 'unique'],
			[['is_active','complete','sync'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'matric_no' => 'Matric No',
            'nric' => 'Nric',
            'st_name' => 'Name',
            'program' => 'Program',
        ];
    }

     public function getStudentLecture(){
        return $this->hasMany(StudentLecture::className(), ['matric_no' => 'matric_no']);
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

}
