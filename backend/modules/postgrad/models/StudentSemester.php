<?php

namespace backend\modules\postgrad\models;

use Yii;

/**
 * This is the model class for table "pg_student_sem".
 *
 * @property int $id
 * @property int $semester_id
 * @property string $date_register
 * @property int $status
 */
class StudentSemester extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pg_student_sem';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['semester_id', 'status'], 'required'],
            [['semester_id', 'status'], 'integer'],
            ['fee_amount', 'number'],
            [['date_register', 'fee_paid_at'], 'safe'],
        ];
    }
    
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'semester_id' => 'Semester',
            'date_register' => 'Date Register',
            'status' => 'Status',
        ];
    }
    
    public function statusList(){
        return [
            10 => 'Active',
            20 => 'Postpone',
            100 => 'Complete'
        ];
    }
}