<?php

namespace backend\modules\esiap\controllers;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\db\Exception;
use yii\db\Expression;
use yii\helpers\FileHelper;

use common\models\Model;
use common\models\UploadFile;

use backend\modules\esiap\models\Tbl4Excel;
use backend\modules\esiap\models\Tbl4Excel2;
use backend\modules\esiap\models\CourseAdminSearch;
use backend\modules\esiap\models\CourseOwnerSearch;
use backend\modules\esiap\models\CourseVerificationSearch;
use backend\modules\esiap\models\CourseInactiveSearch;
use backend\modules\esiap\models\Course;
use backend\modules\esiap\models\CourseVersion;
use backend\modules\esiap\models\CourseProfile;
use backend\modules\esiap\models\CourseSyllabus;
use backend\modules\esiap\models\CourseSlt;
use backend\modules\esiap\models\CourseAssessment;
use backend\modules\esiap\models\CourseReference;
use backend\modules\esiap\models\CourseClo;
use backend\modules\esiap\models\CourseCloAssessment;
use backend\modules\esiap\models\CourseCloDelivery;
use backend\modules\esiap\models\CourseVersionSearch;
use backend\modules\esiap\models\CourseVersionClone;
use backend\modules\esiap\models\CoursePic;
use backend\modules\esiap\models\CourseAccess;
use backend\modules\esiap\models\CourseStaff;
use backend\modules\esiap\models\CourseTransferable;
use backend\modules\esiap\models\Access;
use backend\modules\esiap\models\VerifyRejectForm;
use backend\modules\staff\models\Staff;
use backend\modules\staff\models\StaffMainPosition;
use backend\models\Department;

/**
 * CourseController implements the CRUD actions for Course model.
 */
class CourseAdminController extends Controller
{
    /**
     * @inheritdoc
     */
	public function behaviors(){
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }


    /**
     * Lists all Course models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new CourseAdminSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
	
	public function actionCourseOwner(){
		if(!Access::IAmProgramCoordinator()){
			return $this->render('forbidden');
		}
		 $searchModel = new CourseOwnerSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('course-owner', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
	}
	
	public function actionVerification()
    {
		if(!Access::ICanVerify()){
			return $this->render('forbidden');
		}
        $searchModel = new CourseVerificationSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
		$verify = $this->findVerifier();
		$verify->scenario = 'verify_course';
		
		if ($verify->load(Yii::$app->request->post())) {
			//echo '<pre>';print_r(Yii::$app->request->post());die();
			$action = Yii::$app->request->post('actiontype');
			if($action == 'save'){
				$verify->save();
				return $this->refresh();
			}
			
			if(Yii::$app->request->post('selection')){
			   
				$courses = Yii::$app->request->post('selection');
				
				if($action != 'save'){
				   
					$action = $action == 'verify' ? 20 : 10;
					//die($action);
					if($action == 20){
						$copy = Yii::getAlias('@upload/' . $verify->signiture_file);
						$filepath = 'course-mgt/' . $verify->signiture_file;
						$mirror = Yii::getAlias('@upload/' . $filepath);
						
						if(empty($verify->signiture_file) and !is_file($copy )){
								Yii::$app->session->addFlash('error', "Please put your signature first!.");
						}else{
							
							//ok kena check file ni dah ada ke belum
							$verified_file = '';
							if (is_file($mirror)) {
								$verified_file = $filepath;
							}else{
								$dir = dirname($mirror);
								if (!is_dir($dir)) {
									FileHelper::createDirectory($dir);
								}
								if (is_file($copy)){
									copy($copy, $mirror);
									$verified_file = $filepath;
								}else{
									Yii::$app->session->addFlash('error', "Please put your signature first!.");
								}
								
							}
							
							//klu ada record db shj
							//klu xde copy dalam crs mgt
							// nak kena cari position dia
							$position = StaffMainPosition::findOne(['staff_id' => Yii::$app->user->identity->staff->id]);
							$position_name = '';
							if($position){
								$position_name = $position->position_name;
							}else{
								$department = Department::findOne(['head_dep' => Yii::$app->user->identity->staff->id]);
								if($department){
									$position_name = $department->position_stamp;
								}
							}
							
							$date = $verify->verified_at;
							$date_faculty = $verify->date1;
							$date_senate = $verify->date2;
							$result = CourseVersion::updateAll([
											'verified_by' => Yii::$app->user->identity->id, 
											'status' => $action, 
											'verified_at' => $date,
							                'faculty_approve_at' => $date_faculty,
							                 'senate_approve_at' => $date_senate,
											'verifiedsign_file' => $verified_file,
											'verified_adj_y' => $verify->tbl4_verify_y,
											'verified_size' => $verify->tbl4_verify_size,
											'verifier_position' => $position_name,
											], 
									['id' => $courses]);
											
							if(!$verify->save()){
							    Yii::$app->session->addFlash('error', "Verification failed!");
							}
							if($result){
								Yii::$app->session->addFlash('success', "Verification successful.");
								return $this->refresh();
							}
									
							
							
						}
					}else{
						$result = CourseVersion::updateAll([
											'verified_by' => Yii::$app->user->identity->id, 
											'status' => $action, 
											'verifiedsign_file' => '',
											'verified_adj_y' => 0,
											'verified_size' => 0,
											], 
									['id' => $courses]);
						$verify->save();
						if($result){
							Yii::$app->session->addFlash('success', "Unverification successful.");
						}
					}
					
				}
				
				
			}else{
				if($action != 'save'){
					Yii::$app->session->addFlash('error', "Please select some courses first.");
					//return $this->refresh();
				}
				
			}
			
		}
		
        return $this->render('verification', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
			'verify' => $verify
        ]);
    }
	
	public function actionVerificationPage($id){
		if(!Access::ICanVerify()){
			return $this->render('forbidden');
		}

		$verify = $this->findVerifier();
		$verify->scenario = 'verify_course';
		
		$reject_form = VerifyRejectForm::findOne($id);
		$reject_form->scenario = 'verify_reject';
		
		$version = $this->findVersion($id);
		$version->scenario = 'verify_approve';
		
		if ($version->load(Yii::$app->request->post())) {
			
				$courses = $version->id;
				
				$copy = Yii::getAlias('@upload/' . $verify->signiture_file);
				$filepath = 'course-mgt/' . $verify->signiture_file;
				$mirror = Yii::getAlias('@upload/' . $filepath);
				if(empty($verify->signiture_file) and !is_file($copy )){
						Yii::$app->session->addFlash('error', "Please put your signiture first!.");
				}else{
					
					//ok kena check file ni dah ada ke belum
					$verified_file = '';
					if (is_file($mirror)) {
						$verified_file = $filepath;
					}else{
						$dir = dirname($mirror);
						if (!is_dir($dir)) {
							FileHelper::createDirectory($dir);
						}
						if (is_file($copy)){
							copy($copy, $mirror);
							$verified_file = $filepath;
						}else{
							Yii::$app->session->addFlash('error', "Please put your signiture first!.");
						}
						
					}
					$position = StaffMainPosition::findOne(['staff_id' => Yii::$app->user->identity->staff->id]);
					$position_name = '';
					if($position){
						$position_name = $position->position_name;
					}else{
						$department = Department::findOne(['head_dep' => Yii::$app->user->identity->staff->id]);
						if($department){
							$position_name = $department->position_stamp;
						}
					}
					$date = $verify->verified_at;
					$version->verified_by = Yii::$app->user->identity->id;
					$version->status = 20; //verified
					$version->verifiedsign_file =  $verified_file;
					$version->verifier_position = $position_name;
					if($version->save()){
						Yii::$app->session->addFlash('success', "Verification successful.");
						return $this->redirect(['verification']);
					}
				}
		}
		
		if ($reject_form->load(Yii::$app->request->post())) {
			$reject_form->status = 13;
			if($reject_form->save()){
				Yii::$app->session->addFlash('success', "Back to update successful");
				return $this->redirect(['verification']);
			}
		}
		
		return $this->render('verification-page', [
            'version' => $version,
			'verify' => $verify,
			'reject_form' => $reject_form
        ]);
	}

	
	protected function findVerifier(){
		return Staff::findOne(Yii::$app->user->identity->staff->id);
	}
	
	public function actionInactive()
    {
        $searchModel = new CourseInactiveSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('inactive', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
	
	 /**
     * Lists all CourseVersion models.
     * @return mixed
     */
    public function actionCourseVersion($course)
    {
        $searchModel = new CourseVersionSearch();
        $dataProvider = $searchModel->search($course, Yii::$app->request->queryParams);
		
		$courseModel = $this->findModel($course);

        return $this->render('../course-version/index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
			'course' => $courseModel
        ]);
    }
	
	/**
     * Creates a new CourseVersion model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCourseVersionCreate($course)
    {
        $model = new CourseVersion();
		$model->scenario = 'create';
		$course_model = $this->findModel($course);

        if ($model->load(Yii::$app->request->post())) {
			
			$transaction = Yii::$app->db->beginTransaction();
			try {
				
				$model->course_id = $course;
				$model->created_by = Yii::$app->user->identity->id;
				$model->created_at = new Expression('NOW()');
				if($model->is_developed == 1){
					CourseVersion::updateAll(['is_developed' => 0], ['course_id' => $course]);
				}
				$flag = true;
				if($model->save()){
					if($model->duplicate == 1){
						if($model->dup_version > 0){
							$clone = new CourseVersionClone;
							$clone->ori_version = $model->dup_version;
							$clone->copy_version = $model->id;
							if($flag = $clone->cloneVersion()){
								Yii::$app->session->addFlash('success', "Version creation with duplication is successful");
							}else{
								Yii::$app->session->addFlash('error', "Duplication failed!");
							}
						}else{
							//Yii::$app->session->addFlash('error', "No existing version selected!");
						}
						
					}else{
						Yii::$app->session->addFlash('success', "Empty course version creation is successful");
					}
					
				}
				
				

				if ($flag) {
					$transaction->commit();
					return $this->redirect(['/esiap/course-admin/update', 'course' => $course]);
				} else {
					$transaction->rollBack();
				}
			} catch (Exception $e) {
				$transaction->rollBack();
				
			}
			
        }

        return $this->renderAjax('../course-version/create', [
            'model' => $model,
			'course' => $course_model
        ]);
    }
	
	public function actionListVersionByCourse($course){
		
		$version = CourseVersion::find()->select('id, version_name')->where(['course_id' => $course])->orderBy('created_at DESC')->all();

		
		if($version){
			return Json::encode($version);
		}
		
	}
	
	public function actionVerifyVersion($id){
		 $model = CourseVersion::findOne($id);
		 $model->scenario = 'verify';
		 $model->status = 20;
		 $model->verified_by = Yii::$app->user->identity->id;
		 $model->verified_at = new Expression('NOW()');
		 if($model->save()){
			 Yii::$app->session->addFlash('success', "Successfully Verified");
			 return $this->redirect(['update', 'course' => $model->course_id]);
		 }
		 
	}
	
	public function actionVersionBackDraft($id){
		 $model = CourseVersion::findOne($id);
		 $model->scenario = 'status';
		 $model->status = 0;
		 if($model->save()){
			 Yii::$app->session->addFlash('success', "Data Updated");
			 return $this->redirect(['update', 'course' => $model->course_id]);
		 }
		 
	}
	
	public function actionCourseVersionDelete($id){
		$model = $this->findVersionModel($id);
		if($model->deleteVersion()){
			Yii::$app->session->addFlash('success', "Course Version Deleted");
			
		}
		
		return $this->redirect(['/esiap/course-admin/update', 'course' => $model->course->id]);

	}
	
	public function actionCourseVersionUpdate($id)
    {
		$model = $this->findVersionModel($id);
		$model->scenario = 'update';

        if ($model->load(Yii::$app->request->post())) {
			
			$model->updated_at = new Expression('NOW()');
			
			if($model->is_developed == 1){
				CourseVersion::updateAll(['is_developed' => 0], ['course_id' => $model->course_id]);
			}
			
			if($model->is_published == 1){
				if($model->status == 20){
					if($model->is_developed ==1){
						Yii::$app->session->addFlash('error', "You can not publish and develop at the same time");
						return $this->redirect(['/esiap/course-admin/update', 'course' => $model->course->id]);
					}
					CourseVersion::updateAll(['is_published' => 0], ['course_id' => $model->course_id]);
				}else{
					Yii::$app->session->addFlash('error', "The status must be verified before publishing");
					return $this->redirect(['/esiap/course-admin/update', 'course' => $model->course->id]);
				}
			}
			
			if($model->save()){
				Yii::$app->session->addFlash('success', "Course Version Updated");
				return $this->redirect(['/esiap/course-admin/update', 'course' => $model->course->id]);
			}
			
			
            
        }

        return $this->renderAjax('../course-version/update', [
            'model' => $model,
        ]);
    }


    /**
     * Creates a new Course model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Course();
		$model->scenario = 'create';

        if ($model->load(Yii::$app->request->post())) {
			$model->course_code = str_replace(' ','', $model->course_code);
			$code = Course::findOne(['course_code' => $model->course_code]);
			if($code){
				Yii::$app->session->addFlash('error', "The course code has already exist!");
			}else{
				if($model->save()){
					Yii::$app->session->addFlash('success', "A new course has been successfully created. Please be noted that creating a development version is needed to fill in course information.");
					return $this->redirect(['update', 'course' => $model->id]);
				}
			}
			
            
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Course model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($course)
    {
        $model = $this->findModel($course);
		$model->scenario = 'update';
        $pics = $model->coursePics;
		
		$accesses = $model->courseAccesses;
		
		$searchModel = new CourseVersionSearch();
        $dataProvider = $searchModel->search($course, Yii::$app->request->queryParams);

        
       
        if ($model->load(Yii::$app->request->post())) {
			$model->course_code = str_replace(' ','', $model->course_code);
			$model->updated_at = new Expression('NOW()');    
			if($model->save()){
			$flag = true;
            $staff_pic_arr = Yii::$app->request->post('staff_pic');
			
			if($staff_pic_arr){
				
				$kira_post = count($staff_pic_arr);
				$kira_lama = count($model->coursePics);
				if($kira_post > $kira_lama){
					
					$bil = $kira_post - $kira_lama;
					for($i=1;$i<=$bil;$i++){
						$insert = new CoursePic;
						$insert->course_id = $model->id;
						if(!$insert->save()){
							$flag = false;
						}
					}
				}else if($kira_post < $kira_lama){

					$bil = $kira_lama - $kira_post;
					$deleted = CoursePic::find()
					  ->where(['course_id'=>$model->id])
					  ->limit($bil)
					  ->all();
					if($deleted){
						foreach($deleted as $del){
							$del->delete();
						}
					}
				}
				
				$update_pic = CoursePic::find()
				->where(['course_id' => $model->id])
				->all();
				//echo count($staff_pic_arr);
				//echo count($update_pic);die();

				if($update_pic){
					$i=0;
					foreach($update_pic as $ut){
						$ut->staff_id = $staff_pic_arr[$i];
						$ut->save();
						$i++;
					}
				}
			}
			
			

            $staff_access_arr = Yii::$app->request->post('staff_access');
			if($staff_access_arr){
				//echo 'hai';die();
				$kira_post = count($staff_access_arr);
				$kira_lama = count($model->courseAccesses);
				if($kira_post > $kira_lama){
					
					$bil = $kira_post - $kira_lama;
					for($i=1;$i<=$bil;$i++){
						//print_r($staff_access_arr);die();
						$insert = new CourseAccess;
						$insert->course_id = $model->id;
						if(!$insert->save()){
							$insert->flashError();
						}
					}
				}else if($kira_post < $kira_lama){

					$bil = $kira_lama - $kira_post;
					$deleted = CourseAccess::find()
					  ->where(['course_id'=>$model->id])
					  ->limit($bil)
					  ->all();
					if($deleted){
						foreach($deleted as $del){
							$del->delete();
						}
					}
				}
				
				$update_access = CourseAccess::find()
				->where(['course_id' => $model->id])
				->all();
				//echo count($staff_access_arr);
				//echo count($update_access);die();

				if($update_access){
					$i=0;
					foreach($update_access as $ut){
						$ut->staff_id = $staff_access_arr[$i];
						$ut->save();
						$i++;
					}
				}
			}
			Yii::$app->session->addFlash('success', "Course Updated");
			}else{
				$model->flashError();
			}
			
			return $this->redirect(['update', 'course' => $course]);
			}
		
		return $this->render('update', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
			'model' => $model
        ]);

    }
	
	public function actionUpdateSignature($version){
		$model = $this->findVersion($version);
		
		if ($model->load(Yii::$app->request->post())){
			$model->updated_at = new Expression('NOW()');
			$model->save();
		}
		
		return $this->render('update-signature', [
			'model' => $model
        ]);
	}
	
	public function actionUpdateOwner($course)
    {
        $model = $this->findModel($course);
        $pics = $model->coursePics;
		$accesses = $model->courseAccesses;
		
        if ($model->load(Yii::$app->request->post())) {
			$model->updated_at = new Expression('NOW()');    
			if($model->save()){
			$flag = true;
            $staff_pic_arr = Yii::$app->request->post('staff_pic');
			
			if($staff_pic_arr){
				
				$kira_post = count($staff_pic_arr);
				$kira_lama = count($model->coursePics);
				if($kira_post > $kira_lama){
					
					$bil = $kira_post - $kira_lama;
					for($i=1;$i<=$bil;$i++){
						$insert = new CoursePic;
						$insert->course_id = $model->id;
						if(!$insert->save()){
							$flag = false;
						}
					}
				}else if($kira_post < $kira_lama){

					$bil = $kira_lama - $kira_post;
					$deleted = CoursePic::find()
					  ->where(['course_id'=>$model->id])
					  ->limit($bil)
					  ->all();
					if($deleted){
						foreach($deleted as $del){
							$del->delete();
						}
					}
				}
				
				$update_pic = CoursePic::find()
				->where(['course_id' => $model->id])
				->all();
				//echo count($staff_pic_arr);
				//echo count($update_pic);die();

				if($update_pic){
					$i=0;
					foreach($update_pic as $ut){
						$ut->staff_id = $staff_pic_arr[$i];
						$ut->save();
						$i++;
					}
				}
			}
			
			

            $staff_access_arr = Yii::$app->request->post('staff_access');
			if($staff_access_arr){
				//echo 'hai';die();
				$kira_post = count($staff_access_arr);
				$kira_lama = count($model->courseAccesses);
				if($kira_post > $kira_lama){
					
					$bil = $kira_post - $kira_lama;
					for($i=1;$i<=$bil;$i++){
						//print_r($staff_access_arr);die();
						$insert = new CourseAccess;
						$insert->course_id = $model->id;
						if(!$insert->save()){
							$insert->flashError();
						}
					}
				}else if($kira_post < $kira_lama){

					$bil = $kira_lama - $kira_post;
					$deleted = CourseAccess::find()
					  ->where(['course_id'=>$model->id])
					  ->limit($bil)
					  ->all();
					if($deleted){
						foreach($deleted as $del){
							$del->delete();
						}
					}
				}
				
				$update_access = CourseAccess::find()
				->where(['course_id' => $model->id])
				->all();
				//echo count($staff_access_arr);
				//echo count($update_access);die();

				if($update_access){
					$i=0;
					foreach($update_access as $ut){
						$ut->staff_id = $staff_access_arr[$i];
						$ut->save();
						$i++;
					}
				}
			}
			Yii::$app->session->addFlash('success', "Course Updated");
			}else{
				$model->flashError();
			}
			
			return $this->redirect(['course-owner']);
			}
		
		return $this->render('update-owner', [
			'model' => $model
        ]);

    }
	
	public function actionProfile($course)
    {
        $model = $this->findProfile($course);
		$model->scenario = 'update';

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
			Yii::$app->session->addFlash('success', "Data Updated");
            return $this->redirect(['profile', 'course' => $course]);
        }

        return $this->render('profile', [
            'model' => $model,
        ]);
    }
	
	
	
    /**
     * Deletes an existing Course model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Course model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Course the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Course::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
	
	protected function findVersionModel($id)
    {
        if (($model = CourseVersion::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
	
	
	protected function findProfile($id)
    {
		$default = $this->findDefaultVersion($id);
		$model = CourseProfile::findOne(['crs_version_id' => $default->id]);
		if($model){
			return $model;
		}else{
			$profile = new CourseProfile;
			$profile->scenario = 'fresh';
			$profile->crs_version_id = $default->id;
			if($profile->save()){
				return $profile;
			}else{
				throw new NotFoundHttpException('There is problem creating course profile!');
			}
		}
    }
	
	protected function findDefaultVersion($id){
		$default = CourseVersion::findOne(['course_id' => $id, 'is_developed' => 1]);
		if($default){
			return $default;
		}else{
			throw new NotFoundHttpException('Please create default active version for this course!');
		}
	}
	
	protected function findVersion($id){
		$default = CourseVersion::findOne($id);
		if($default){
			return $default;
		}else{
			throw new NotFoundHttpException('Page not found!');
		}
	}
	
	protected function findCourseClo($id)
    {
		$default = $this->findDefaultVersion($id);
		$model = CourseProfile::findOne(['crs_version_id' => $default->id]);
		if($model){
			return $model;
		}else{
			$model = new CourseClo;
			$model->scenario = 'fresh';
			$model->crs_version_id = $default->id;
			if($model->save()){
				return $model;
			}else{
				throw new NotFoundHttpException('There is problem creating this sub function course!');
			}
		}
    }
	
	public function actionTarikcoor(){
		/* $courses = Course::find()->where(['>','coordinator', 0])->all();
		if($courses){
			foreach($courses as $course){
				$pic = CoursePic::findOne(['staff_id' => $course->coordinator]);
				if(!$pic){
					$npic = new CoursePic;
					$npic->staff_id = $course->coor->fasi->id;
					$npic->course_id = $course->id;
					$npic->updated_at = new Expression('NOW()');
					$npic->save();
				}
			}
		} */
	}
	
	public function actionRemovebracket(){
		/* $clos = CourseClo::find()->all();
		foreach($clos as $clo){
			$bm = $clo->clo_text;
			$clo->clo_text = trim(preg_replace('/\s*\([^)]*\)/', '', $bm));
			$bi = $clo->clo_text_bi;
			$clo->clo_text_bi = trim(preg_replace('/\s*\([^)]*\)/', '', $bi));
			$clo->save();
		} */
	}
	
	public function actionBulkCovidUpdateSlt(){
		die();
		$courses = Course::find()->where(['faculty_id' => Yii::$app->params['faculty_id']])->all();
		foreach($courses as $course){
			$ver = CourseVersion::findOne(['course_id' => $course->id, 'version_name' => 'Covid Version']);
			if($ver){
				$syl = CourseSyllabus::find()
				->where(['week_num' => ['6','7','8','9','10', '11', '12'], 'crs_version_id' => $ver->id])
				->andWhere("topics NOT LIKE '%cuti%'")
				->all();
				if($syl){
					foreach($syl as $s){
						$s->pnp_lecture = 0;
						$s->pnp_tutorial = 0;
						$s->pnp_practical = 0;
						$s->pnp_others = 0;
						$s->nf2f = 2;
						if($s->save()){
							echo 'SLT good.';
						}
					}
				}
			}
			
		}
		exit;
	}
	
	public function actionRunBulkClone($name){
		if(empty($name)){
			echo 'nama mana?';
			die();
		}
		$vname = $name;
		$transaction = Yii::$app->db->beginTransaction();
        try {
			$this->bulkCourseClone($vname);
            $transaction->commit();
            
        }
        catch (Exception $e) 
        {
            $transaction->rollBack();
            Yii::$app->session->addFlash('info', 'TRY AGAIN : ' . $e->getMessage());
        }
		exit;
	}
		
	private function bulkCourseClone($name){
		//die();
		$courses = Course::find()->where(['faculty_id' => Yii::$app->params['faculty_id']])->all();
		foreach($courses as $course){
			$mqf2 = CourseVersion::findOne(['course_id' => $course->id, 'version_name' => $name]);
			$ori = CourseVersion::find()->where(['course_id' => $course->id])
					->orderBy('created_at DESC')->limit(1)->all();
					
			/* echo 'course:' . $course->id;
			echo '<br />';
			echo $ori[0]->id . '<------';die();  */
			
			if(!$mqf2){
				CourseVersion::updateAll(['is_developed' => 0], ['course_id' => $course->id]);
				$nv = new CourseVersion;
				$nv->course_id = $course->id;
				$nv->version_type_id = 2;
				$nv->version_name = $name;
				$nv->study_week = '16';
				$nv->final_week = '17-19';
				$nv->created_at = new Expression('NOW()');
				$nv->is_developed = 1;
				$nv->status = 0;
				if($nv->save()){
					
					//echo $ori[0]->id . '<------';die(); 
					if($ori){
						$clone = new CourseVersionClone;
						$clone->ori_version = $ori[0]->id;
						$clone->copy_version = $nv->id;
						if(!$clone->cloneVersion()){
							echo 'clone failed ';
						}else{
							echo 'clone good; ';
						}
						
					}else{
		
						echo 'no ori <br />';
					}
					
				}
			}
		}
		
		
	}
	
	public function actionBulkDeleteVersion($name){
		//die();/////////////////////////////
		$courses = Course::find()->all();
		foreach($courses as $course){
			$ver = CourseVersion::findOne(['course_id' => $course->id, 'version_name' => $name]);
			if($ver){
				$id = $ver->id;
				$clos = CourseClo::find()->where(['crs_version_id' => $id])->all();
				if($clos){
					foreach($clos as $clo){
						$clo_id = $clo->id;
						CourseCloAssessment::deleteAll(['clo_id' => $clo_id]);
						CourseCloDelivery::deleteAll(['clo_id' => $clo_id]);
					}
				}
				CourseClo::deleteAll(['crs_version_id' => $id]);
				CourseReference::deleteAll(['crs_version_id' => $id]);
				CourseSyllabus::deleteAll(['crs_version_id' => $id]);
				CourseSlt::deleteAll(['crs_version_id' => $id]);
				CourseAssessment::deleteAll(['crs_version_id' => $id]);
				CourseProfile::deleteAll(['crs_version_id' => $id]);
				
				CourseVersion::findOne($id)->delete();
			}
			echo 'delete good; ';
		}
		exit;
	}
	
	public function actionBulkupdatepusatko(){
		die();////////////////////////stop
		$courses = Course::find()->where(['faculty_id' => Yii::$app->params['faculty_id']])->all();
		foreach($courses as $course){
			$version = CourseVersion::findOne(['course_id' => $course->id, 'version_type_id' => 2]);
			if($version){
				//cari pic
				$pics = $course->coursePics;
				if($pics){
					foreach($pics as $pic){
						$old = CourseStaff::findOne(['crs_version_id' => $version->id, 'staff_id' => $pic->staff_id]);
						if(!$old){
							$staff = new CourseStaff;
							$staff->crs_version_id = $version->id;
							$staff->staff_id = $pic->staff_id;
							$staff->save();
						}
						
					}
				}
				//staff
		//semester 1 -1 
		
		$profile = CourseProfile::findOne(['crs_version_id' => $version->id]);
		if(!$profile){
			$profile = new CourseProfile;
			$profile->crs_version_id = $version->id;
		}
		$profile->offer_sem = 1;
		$profile->offer_year = 1;
		$profile->save();
		
		//plo 8 - 11
		
		$clos = CourseClo::find()->where(['crs_version_id' => $version->id])->all();
		if($clos){
			$x = 1;
			foreach($clos as $clo){
				for($i=1;$i<=11;$i++){
					$prop = 'PLO'.$i;
					if(($x == 1 and $i == 11) or ($x == 2 and $i == 8) ){
						$clo->{$prop} = 1;
					}else{
						$clo->{$prop} = 0;
					}
					
				}
				$clo->save();
				$x++;
			}
		}
		
		
		//transferable value & leadership
		// 9 & 6
		
		$transfer1 = CourseTransferable::findOne(['crs_version_id' => $version->id, 'transferable_id' => 9]);
		if(!$transfer1){
			$trans1 = new CourseTransferable;
			$trans1->crs_version_id = $version->id;
			$trans1->transferable_id = 9;
			$trans1->transfer_order = 0;
			$trans1->save();
		}
		
		$transfer2 = CourseTransferable::findOne(['crs_version_id' => $version->id, 'transferable_id' => 6]);
		if(!$transfer1){
			$trans1 = new CourseTransferable;
			$trans1->crs_version_id = $version->id;
			$trans1->transferable_id = 6;
			$trans1->transfer_order = 1;
			$trans1->save();
		}
		
		//slt assess 4 - 2
		
		$ass = CourseAssessment::find()->where(['crs_version_id' => $version->id])->all();
		if($ass){
			foreach($ass as $as){
				$as->assess_f2f = 4;
				$as->assess_nf2f = 2;
				$as->save();
			}
		}
		
			}
		}
		
	}
	
	public function actionTable4(){
		if(Yii::$app->request->post()){
			if(Yii::$app->request->post('selection')){
				$pdf = new Tbl4Excel2;
				$pdf->multiple = true;
				$pdf->courses = Yii::$app->request->post('selection');
				$pdf->generateExcel();
				exit;
			}else{
				Yii::$app->session->addFlash('error', "Please select some courses first.");
				return $this->redirect('index');
			}
			
		}
		

		
	}
	
	public function actionAutoWeekDuration(){
		$list = CourseSyllabus::find()->all();
		foreach($list as $row){
			
			if (strpos($row->week_num, '-') !== false and strpos($row->week_num, '(') == false) {
				$string = str_replace(" ", "", $row->week_num);
				$arr = explode("-",$string);
				$duration = $arr[1] - $arr[0] + 1;
				$row->duration = $duration;
				$row->save();
				echo $duration;
				echo '<br />';
			}
			
		}
		exit();
	}
	
	public function actionAutoSetMidtermOne(){
		$list = Course::find()->where(['credit_hour' => 1])->all();
		foreach($list as $row){
			echo $row->course_name;
			$version = CourseVersion::find()->where(['course_id' => $row->id])->all();
			if($version){
				$br = 7;
				foreach($version as $ver){
					$syll = CourseSyllabus::find()->where(['crs_version_id' => $ver->id])->all();
					if($syll){
						$week_num = 1;
						$found = false;
						foreach($syll as $syl){
							
							if($found == false and $week_num >= 7){
								$br = $week_num;
								$found = true;
								break;
							}
							$week_num = $week_num + $syl->duration;
						}
					}
					$json = '["'.$br.'"]';
					echo $json . ' ';
					$ver->syllabus_break = $json;
					$ver->save();
				}
			}
			echo '<br />';
		}
		exit();
	}
	
	public function actionFixBreak(){
		$list = CourseVersion::find()
		->where(['version_name' => 'TABLE 4 V2'])
		->andWhere(['or', 
            ['syllabus_break' => ''],
            ['syllabus_break' => 'null']
        ])		
		->all();
		if($list){
			foreach($list as $row){
				$course = $row->course_id;
				$covid = CourseVersion::findOne(['version_name' => 'Covid Version', 'course_id' => $course]);
				$break_old = $covid->syllabus_break;
				$row->syllabus_break = $break_old;
				$row->save();
				echo $row->course_id . ' done<br />';
			}
		}
	}
	
	public function actionAutoSetMidtermTwo(){
		die();
		$list = Course::find()->where(['credit_hour' => 2])->all();
		foreach($list as $row){
			echo $row->course_name;
			$version = CourseVersion::find()->where(['course_id' => $row->id])->all();
			if($version){
				foreach($version as $ver){
					if(empty($ver->syllabus_break) or $ver->syllabus_break == 'null' or $ver->syllabus_break == null){
						$syll = CourseSyllabus::find()->where(['crs_version_id' => $ver->id])->all();
						$br1 = 7;
							$br2 = 21;
						if($syll){
							//echo 'syll'.$ver->id. ' ';
							
							$week_num = 1;
							$found1 = false;
							$found2 = false;
							
							foreach($syll as $syl){
								
								if($found1 == false and $week_num >= 7){
									$br1 = $week_num;
									$found1 = true;
								}
								if($found2 == false and $week_num >= 21){
									$br2 = $week_num;
									$found2 = true;
									break;
								}
								$week_num = $week_num + $syl->duration;
							}
						
						}
						
						$json = '["'.$br1.'", "'.$br2.'"]';
						echo $json . ' ';
						$ver->syllabus_break = $json;
						$ver->save();
						
					}
					
				}
			}
			echo '<br />';
		}
		exit();
	}
	
	public function actionUploadFile($attr, $id){
        $attr = $this->clean($attr);
        $model = $this->findVerifier();
        $model->file_controller = 'course-admin';
		$path = 'signiture/' . Yii::$app->user->identity->staff->staff_no ;
        return UploadFile::upload($model, $attr, 'updated_at', $path);

    }

	protected function clean($string){
		$allowed = ['signiture'];
        if(in_array($string,$allowed)){
            return $string;
        }
        throw new NotFoundHttpException('Invalid Attribute');
    }

	public function actionDeleteFile($attr, $id)
    {
        $attr = $this->clean($attr);
        $model = $this->findVerifier();
        $attr_db = $attr . '_file';
        
        $file = Yii::getAlias('@upload/' . $model->{$attr_db});
        
        $model->scenario = $attr . '_delete';
        $model->{$attr_db} = '';
        $model->updated_at = new Expression('NOW()');
        if($model->save()){
            if (is_file($file)) {
                unlink($file);
                
            }
            
            return Json::encode([
                        'good' => 1,
                    ]);
        }else{
            return Json::encode([
                        'errors' => $model->getErrors(),
                    ]);
        }
        


    }

	public function actionDownloadFile($attr, $id, $identity = true){
        $attr = $this->clean($attr);
        $model = $this->findVerifier();
        $filename = strtoupper($attr) . ' ' . Yii::$app->user->identity->fullname;
        
        
        
        UploadFile::download($model, $attr, $filename);
    }


	
}
