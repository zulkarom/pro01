<?php

namespace backend\modules\esiap\models;

use Yii;

/**
 * This is the model class for table "sp_course_profile".
 *
 * @property int $id
 * @property int $crs_version_id
 * @property int $prerequisite
 * @property string $synopsis
 * @property string $synopsis_bi
 * @property string $transfer_skill
 * @property string $transfer_skill_bi
 * @property string $feedback
 * @property string $feedback_bi
 * @property string $staff_academic
 * @property string $requirement
 * @property string $additional
 * @property string $offer_at
 */
class CourseProfile extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sp_course_profile';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
			
			[['crs_version_id'], 'required', 'on' => 'fresh'],
			
            [['synopsis', 'synopsis_bi', 'transfer_skill', 'transfer_skill_bi', 'feedback', 'feedback_bi'], 'required', 'on' => 'update'],
			
			
            [['crs_version_id', 'prerequisite'], 'integer'],
            [['synopsis', 'synopsis_bi', 'transfer_skill', 'transfer_skill_bi', 'feedback', 'feedback_bi', 'staff_academic', 'requirement', 'additional'], 'string'],
            [['offer_at'], 'string', 'max' => 200],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'crs_version_id' => 'Crs Version ID',
            'prerequisite' => 'Prerequisite',
            'synopsis' => 'Synopsis (BM)',
            'synopsis_bi' => 'Synopsis (EN)',
            'transfer_skill' => 'Transfer Skill (BM)',
            'transfer_skill_bi' => 'Transfer Skill (EN)',
            'feedback' => 'Feedback (BM)',
            'feedback_bi' => 'Feedback (EN)',
            'staff_academic' => 'Staff Academic',
            'requirement' => 'Requirement',
            'additional' => 'Additional',
            'offer_at' => 'Offer At',
        ];
    }
	
	public function getCourseVersion(){
        return $this->hasOne(CourseVersion::className(), ['id' => 'crs_version_id' ]);
    }
	
	public function getCourse(){
		return Course::findOne($this->courseVersion->course_id);
	}

}