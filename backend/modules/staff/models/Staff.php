<?php

namespace backend\modules\staff\models;

use Yii;
use common\models\User;

/**
 * This is the model class for table "staff".
 *
 * @property int $id
 * @property int $user_id
 * @property string $staff_no
 * @property string $user_name
 * @property string $user_password_hash
 * @property string $staff_email
 * @property string $staff_name
 * @property string $staff_name_pub
 * @property string $staff_title
 * @property string $staff_edu
 * @property int $is_academic
 * @property int $position_id
 * @property int $position_status
 * @property int $working_status
 * @property string $leave_start
 * @property string $leave_end
 * @property string $leave_note
 * @property string $rotation_post
 * @property string $staff_expertise
 * @property string $staff_gscholar
 * @property string $officephone
 * @property string $handphone1
 * @property string $handphone2
 * @property string $staff_ic
 * @property string $staff_dob
 * @property string $date_begin_umk
 * @property string $date_begin_service
 * @property string $staff_note
 * @property string $personal_email
 * @property string $ofis_location
 * @property string $staff_cv
 * @property string $staff_img
 * @property int $teach_pg
 * @property int $staff_level
 * @property string $staff_interest
 * @property int $staff_department
 * @property int $trash
 * @property int $publish
 * @property int $staff_active
 * @property string $user_token
 * @property int $user_token_at
 */
class Staff extends \yii\db\ActiveRecord
{
	public $staff_name;
	
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'staff';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['staff_no', 'user_id', 'user_name', 'user_password_hash', 'staff_title', 'is_academic', 'position_id', 'position_status', 'working_status'], 'required'],
			
			[['user_id'], 'required', 'on' => 'reload'],
			
			
            [['user_id', 'is_academic', 'position_id', 'position_status', 'working_status', 'teach_pg', 'staff_level', 'staff_department', 'trash', 'publish', 'staff_active', 'user_token_at'], 'integer'],
            [['leave_start', 'leave_end', 'staff_dob', 'date_begin_umk', 'date_begin_service'], 'safe'],
			
            [['leave_note', 'staff_interest'], 'string'],
			
            [['staff_no'], 'string', 'max' => 10],
            [['user_name', 'staff_img'], 'string', 'max' => 50],
            [['user_password_hash', 'user_token'], 'string', 'max' => 255],
            [['staff_note', 'personal_email', 'ofis_location'], 'string', 'max' => 100],
            [['staff_name_pub'], 'string', 'max' => 200],
            [['staff_title', 'officephone', 'handphone1', 'handphone2'], 'string', 'max' => 20],
			
            [['staff_edu', 'staff_expertise', 'staff_cv'], 'string', 'max' => 300],
            [['rotation_post', 'staff_gscholar'], 'string', 'max' => 500],
            [['staff_ic'], 'string', 'max' => 15],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'staff_no' => 'Staff No',
            'user_name' => 'User Name',
            'user_password_hash' => 'User Password Hash',
            'staff_name_pub' => 'Staff Name Pub',
            'staff_title' => 'Staff Title',
            'staff_edu' => 'Staff Edu',
            'is_academic' => 'Is Academic',
            'position_id' => 'Position ID',
            'position_status' => 'Position Status',
            'working_status' => 'Working Status',
            'leave_start' => 'Leave Start',
            'leave_end' => 'Leave End',
            'leave_note' => 'Leave Note',
            'rotation_post' => 'Rotation Post',
            'staff_expertise' => 'Staff Expertise',
            'staff_gscholar' => 'Staff Gscholar',
            'officephone' => 'Officephone',
            'handphone1' => 'Handphone1',
            'handphone2' => 'Handphone2',
            'staff_ic' => 'Staff Ic',
            'staff_dob' => 'Staff Dob',
            'date_begin_umk' => 'Date Begin Umk',
            'date_begin_service' => 'Date Begin Service',
            'staff_note' => 'Staff Note',
            'personal_email' => 'Personal Email',
            'ofis_location' => 'Ofis Location',
            'staff_cv' => 'Staff Cv',
            'staff_img' => 'Staff Img',
            'teach_pg' => 'Teach Pg',
            'staff_level' => 'Staff Level',
            'staff_interest' => 'Staff Interest',
            'staff_department' => 'Staff Department',
            'trash' => 'Trash',
            'publish' => 'Publish',
            'staff_active' => 'Staff Active',
            'user_token' => 'User Token',
            'user_token_at' => 'User Token At',
        ];
    }
	
	public function getStaffPosition(){
		return $this->hasOne(StaffPosition::className(), ['position_id' => 'position_id']);
	}
	
	public static function activeStaff(){
		return self::find()
		->select('staff.id, user.fullname as staff_name, user.id as user_id')
		->innerJoin('user', 'user.id = staff.user_id')
		->where(['staff.staff_active' => 1, 'staff.trash' => 0])->orderBy('user.fullname ASC')
		->all();
	}
	
	public static function activeStaffNotMe(){
		return self::find()
		->select('staff.id, user.fullname as staff_name, user.id as user_id')
		->innerJoin('user', 'user.id = staff.user_id')
		->where(['staff.staff_active' => 1, 'staff.trash' => 0])
		->andWhere(['<>', 'staff.id', Yii::$app->user->identity->staff->id])
		->all();
	}
	
	public function getUser(){
		return $this->hasOne(User::className(), ['id' => 'user_id']);
	}
}