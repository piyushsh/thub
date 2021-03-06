<?php namespace talenthub\Http\Controllers;

use Exception;
use Illuminate\Database\Capsule\Manager;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;
use talenthub\Awards;
use talenthub\Events\NotifyUser;
use talenthub\Events\SendMail;
use talenthub\Http\Requests;
use talenthub\Http\Controllers\Controller;

use Illuminate\Http\Request;
use talenthub\Http\Requests\TalentCVRequest;
use talenthub\Http\Requests\UserProfileRequest;
use talenthub\ManagerCareerInformation;
use talenthub\ManagerProfile;
use talenthub\Notifications;
use talenthub\Repositories\BasicSiteRepository;
use talenthub\Repositories\SiteConstants;
use talenthub\Repositories\SiteSessions;
use talenthub\Repositories\SportsRepository;
use talenthub\Repositories\StorageLocationsRepository;
use talenthub\Repositories\UserMetaRepository;
use talenthub\Repositories\UserProfileRepository;
use talenthub\TalentCareerInformation;
use talenthub\TalentCareerReferences;
use talenthub\TalentCareerStatisticsModels\BaseBallStatistics;
use talenthub\TalentCareerStatisticsModels\BasketBallStatistics;
use talenthub\TalentCareerStatisticsModels\RugbyStatistics;
use talenthub\TalentCareerStatisticsModels\SoccerStatistics;
use talenthub\TalentCareerStatisticsModels\SwimmingStatistics;
use talenthub\TalentCareerStatisticsModels\TennisStatistics;
use talenthub\TalentCareerStatisticsModels\TrackAndFieldStatistics;
use talenthub\User;
use talenthub\UserMeta;
use talenthub\UserProfile;
use talenthub\UserSettings;

class ProfileController extends Controller {

	/**Displaying user's own profile	 *
	 * @return Response
	 */
	public function index()
	{
        Blade::setContentTags('<%', '%>');        // for variables and all things Blade
        Blade::setEscapedContentTags('<%%', '%%>');   // for escaped data

        $userProfile = UserProfile::find(Session::get(SiteSessions::USER_ID));
        //$userProfile->unmuatedDataForPresentation();
        $userProfile->getMutatedData = false;

        $country=BasicSiteRepository::getListOfCountries();
        $sportPositions=array_map('ucfirst',SportsRepository::getSportPositions(Session::get(SiteSessions::USER_SPORT_TYPE)));

        $userCareerHistory=$userProfile->careerInformation;
        $awards = $userProfile->awards;

        if($awards == null)
        {
            $awards = new Awards();
        }

        $endorsements = $userProfile->endorsements()->take(10)->get();

        //Setting whether profile is editable or not.
        $profileEditable = true;

		return response()->view('profile.user_profile',compact('userProfile','country','sportPositions','userCareerHistory','awards','endorsements','profileEditable'));
	}


    /**
     *Function to upload Profile Image
     */
    public function uploadImage(Request $request)
    {
        if(!$request->hasFile("profile_image") && !$request->hasFile("cover_image"))
        {
            return redirect()->back()->withErrors(["imageUploaded"=>'image not found']);
        }
        $imageFieldName= $request->hasFile("profile_image") ? "profile_image" : "cover_image";

        $validator = Validator::make($request->all(), [$imageFieldName=>'required']);
        $imageFile = $request->file($imageFieldName);

        $imageExt = $imageFile->getClientOriginalExtension();
        if(array_search(strtolower($imageExt),['jpg','jpeg','png','gif']))
        {
            dd($imageExt);
            return redirect()->back()->withErrors(["imageUploaded"=>'File extension is not supported.']);
        }
        if($validator->fails())
        {
            $errors = $validator->errors();
            dd($errors);
            return redirect()->back()->withErrors($validator->errors());
        }

        $fileName=hash("md5",$imageFile->getClientOriginalName().Session::get(SiteSessions::USER_ID).Session::get(SiteSessions::USER_NAME)).".".$imageFile->getClientOriginalExtension();

        $userProfile = UserProfile::find(Session::get(SiteSessions::USER_ID));

        $image_type_stored = "";

        //Saving Profile Image
        if($imageFieldName == "profile_image")
        {
//            $image = Image::canvas(300, 300);
//            $image_updated  = Image::make($imageFile)->resize(300, 300,function($constraint){
//                $constraint->aspectRatio();
//            });
//            $image->insert($image_updated, 'center');

            $image = Image::make($imageFile)->resize(350, 350, function($constraint){
               $constraint->aspectRatio();
            });
            $icon_image = Image::make($imageFile)->fit(300,300)->resize(60,60);
            $small_image = Image::make($imageFile)->fit(300,300)->resize(100,100);

            $this->createImageSourceDirectories(StorageLocationsRepository::USER_DIRECTORY_TYPE_PROFILE_IMAGES);

            $image->save(public_path().StorageLocationsRepository::USER_PROFILE_IMAGE_PATH.$fileName);
            $icon_image->save(public_path().StorageLocationsRepository::USER_PROFILE_IMAGE_ICON_PATH.$fileName);
            $small_image->save(public_path().StorageLocationsRepository::USER_PROFILE_IMAGE_SMALL_PATH.$fileName);

            $userProfile->profile_image_path = url(StorageLocationsRepository::USER_PROFILE_IMAGE_PATH.$fileName);
            $userProfile->profile_icon_image_path = url(StorageLocationsRepository::USER_PROFILE_IMAGE_ICON_PATH.$fileName);
            $userProfile->profile_small_image_path = url(StorageLocationsRepository::USER_PROFILE_IMAGE_SMALL_PATH.$fileName);

            $image_type_stored = "profile_image";
        }
        else if($imageFieldName == "cover_image")
        {
//            $image = Image::canvas(1200, 350);
//            $image_updated  = Image::make($imageFile)->widen(1200)->crop(1200,350);
//            $image->insert($image_updated, 'center');

            $image  = Image::make($imageFile)->widen(1200);
            $this->createImageSourceDirectories(StorageLocationsRepository::USER_DIRECTORY_TYPE_COVER_IMAGES);

            $image->save(public_path().StorageLocationsRepository::USER_COVER_IMAGE_PATH.$fileName);

            $userProfile->profile_cover_image_path = url(StorageLocationsRepository::USER_COVER_IMAGE_PATH.$fileName);

            $image_type_stored = "cover_image";
        }

        $userProfile->save();

//        return redirect()->back()->with(["imageUploaded"=>"successfully"]);

        Session::put("image_type",$image_type_stored);
        return redirect('profile/'.Session::get(SiteSessions::USER_ID).'/uploadImageSetting')->with(["imageUploaded"=>"successfully"]);
    }


    /**
     *Updating Profile Data as received from the User profile
     */
    public function updateProfileData(Request $request)
    {

        if(!Auth::check())
        {
            return response()->json(['request_status'=>'error','type'=>'user_not_logged_in']);
        }

        $validator = Validator::make(Input::all(),[
                'country'=> 'in:'.implode(",",array_keys(BasicSiteRepository::getListOfCountries())),
            ]);

        if($validator->fails())
        {
            return response()->json(['request_status'=>'error','type'=>'validation_error']);
        }


        try
        {
            $userProfile = UserProfile::find(Session::get(SiteSessions::USER_ID));
            $userProfile->first_name = Input::get("first_name");
            $userProfile->last_name = Input::get("last_name");
            $userProfile->about = Input::get("about");
            $userProfile->country = Input::get("country");
            $userProfile->save();
        }
        catch(ModelNotFoundException $e)
        {
            return response()->json(['request_status'=>'error','type'=>'user_not_found']);
        }

        return response()->json(['request_status'=>'successful']);
    }



	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit()
	{
        $user=UserProfile::find(Session::get(SiteSessions::USER_ID));

        $gender=UserProfileRepository::getUserGender();
        $addressType=UserProfileRepository::getAddressTypes();
        $instituteType=UserProfileRepository::getInstituteType();
        $country=BasicSiteRepository::getListOfCountries();
        $livingWith=UserProfileRepository::getLivingWithType();
        $gradeAverage=UserProfileRepository::getGradeAverageType();
        $sports=BasicSiteRepository::getSportTypes();
        if(SiteConstants::isTalent(Session::get(SiteSessions::USER_TYPE)))
        {
            $userProfile=UserProfile::find($user->user_id);

            return view('profile.talent.edit',compact('userProfile','gender','addressType','instituteType','country','livingWith','gradeAverage'));
        }
        else if(SiteConstants::isCoach(Session::get(SiteSessions::USER_TYPE)))
        {
            $managerProfile=ManagerProfile::find($user->user_id);
            $managerCareerInformation = $managerProfile->managerCareerInformation()->get();
            return view('profile.manager.edit',compact('managerProfile','gender','addressType','instituteType','country','livingWith','gradeAverage','sports'));
        }
        else if(Session::get(SiteSessions::USER_ID)=="")
        {
            return redirect('/');
        }
	}


    /**
     * Function to handle Edit CV request for Talent only
     * @param $id
     */
    public function editCV()
    {
        $country=BasicSiteRepository::getListOfCountries();

        $clubDataMap=SportsRepository::getClubDataMap($this->getSportDataMap(Session::get(SiteSessions::USER_SPORT_TYPE)),true);
        $schoolDataMap=SportsRepository::getSchoolDataMap($this->getSportDataMap(Session::get(SiteSessions::USER_SPORT_TYPE)),true);
        $sportPositions=array_map('ucfirst',SportsRepository::getSportPositions(Session::get(SiteSessions::USER_SPORT_TYPE)));
        $clubLeagueLevel = SportsRepository::getClubLeagueLevel();
        $clubLeagueStatus = SportsRepository::getClubLeagueStatus();
        $schoolTeamReputation = SportsRepository::getSchoolTeamReputation();
        $schoolTeamSideLevel = SportsRepository::getSchoolTeamSideLevel();
        $institutionType = UserProfileRepository::getInstituteType();

        if(SiteConstants::isTalent(Session::get(SiteSessions::USER_TYPE)))
        {
            $talentProfile=UserProfile::find(Session::get(SiteSessions::USER_ID));
            $clubCareerInformation = $talentProfile->careerInformation()->where('career_type','=',SiteConstants::CAREER_TYPE_CLUB)->get();
            $schoolCareerInformation = $talentProfile->careerInformation()->where('career_type','=',SiteConstants::CAREER_TYPE_SCHOOL)->get();

            return view('profile.talent.editCV',compact('talentProfile','country','clubDataMap','schoolDataMap','sportPositions',
                'clubLeagueLevel','clubLeagueStatus','schoolTeamReputation','schoolTeamSideLevel','institutionType',
                'clubCareerInformation','schoolCareerInformation'));
        }
    }

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id, UserProfileRequest $request)
	{

        if(SiteConstants::isTalent(Session::get(SiteSessions::USER_TYPE)))
        {
            $user=UserProfile::find($id);

            foreach($request->all() as $key=>$value)
            {
                if(isset($user->$key))
                    $user->$key=$value;
            }
            //Modifying Data fields of User, the data that doesn't need to be stored in the database
            $user->gender = $request->gender > 0 ? $request->gender : null;
            $user->address_type = $request->address_type > 0 ? $request->address_type : null;
            $user->graduating_from = $request->graduating_from > 0 ? $request->graduating_from : null;
            $user->father_living_with = $request->father_living_with > 0 ? $request->father_living_with : null;
            $user->mother_living_with = $request->mother_living_with > 0 ? $request->mother_living_with : null;
            $user->guardian_living_with = $request->guardian_living_with > 0 ? $request->guardian_living_with : null;
            $user->grade_avg = $request->grade_avg > 0 ? $request->grade_avg : null;

            $user->save();
        }
        else if(SiteConstants::isManager(Session::get(SiteSessions::USER_TYPE)))
        {
            $managerProfile = ManagerProfile::find($id);
            foreach($request->all() as $key=>$value)
            {
                if(isset($managerProfile->$key))
                    $managerProfile->$key=$value;
            }

            $managerProfile->save();

            $managerProfile=$this->storeAndSyncManagerCareerInformation($managerProfile,$request);
            $managerProfile->managerCareerInformation;
        }

        if(Session::has(SiteSessions::USER_COMPLETING_PROFILE_FIRST_TIME))
        {
            return redirect('profile/editCV');
        }
        return redirect('profile/edit')->with(["cv_update_status"=>"successfull"]);
	}


    /**
     * Updates the CV of a Talent for corresponding sports.
     *
     * @param $id
     */
    public function updateCV(Request $request)
    {
        //--------- Need to work over this function afterwards -------//
        $this->validateCVData($request);
        //--------- Need to work over this function afterwards -------//

        $userProfile = UserProfile::find(Session::get(SiteSessions::USER_ID));
        $userParamsData="";
        if(count($request->positions)>0)
        {
            $userProfile->positions=$request->positions;
            $userProfile->preferred_position = $request->preferred_position;
        }

        $userProfile->params=$this->getParamsDataForProfile($request);

        $userProfile->save();

        //Storing Club and School information and their respective sport statistics
        $this->storeClubSchoolInformation($userProfile,$request);

        if(Session::has(SiteSessions::USER_COMPLETING_PROFILE_FIRST_TIME))
        {
            Session::pull(SiteSessions::USER_COMPLETING_PROFILE_FIRST_TIME);
            return redirect('profile/completed');
        }

        return redirect('profile/editCV')->with(["cv_update_status"=>"successfull"]);
    }


    /**
     *Returning the view after profile is completed
     */
    public function completed()
    {
        return view('profile.talent.profile_completed');
    }


    /**
     * The method is storing Career information of a manager and syncing it with already stored information in the database
     *
     * @param $mangerProfile, $request
     */
    public function storeAndSyncManagerCareerInformation(ManagerProfile $managerProfile, $request)
    {
        //Getting existing career information data
        $existing_career_information = $managerProfile->managerCareerInformation;
        $count = count($request->company);
        $managerCareerInformation = array();
        //Iterating till number of career information is provided by the user
        for ($i = 0; $i < $count; $i++) {
            //If existing information is greater than the current iteration, then assigning existing model to the object
            if($i<count($existing_career_information))
            {
                $managerCareerInformation[$i] = $existing_career_information[$i];
            }
            //If existing information is less than the current iteration, then assigning new model
            else if($i>=count($existing_career_information))
            {
                $managerCareerInformation[$i] = new ManagerCareerInformation();
            }

            $managerCareerInformation[$i]->company = $request->company[$i];
            $managerCareerInformation[$i]->industry = $request->industry[$i];
            $managerCareerInformation[$i]->position_held = $request->position_held[$i];
            $managerCareerInformation[$i]->career_country = $request->career_country[$i];
            $managerCareerInformation[$i]->duties = $request->duties[$i];
            $managerCareerInformation[$i]->from_date = $request->from_date[$i];
            $managerCareerInformation[$i]->to_date = $request->to_date[$i];

            if($i>=count($existing_career_information))
            {
                $managerCareerInformation[$i] = $managerProfile->managerCareerInformation()->create($managerCareerInformation[$i]->toArray());
            }
            $managerCareerInformation[$i]->save();

        }
        if (count($existing_career_information) > $count)
        {
            for($i=$count;$i<count($existing_career_information);$i++)
            {
                $existing_career_information[$i]->delete();
            }
        }
        return $managerProfile;
    }


    /**
     * Storing Club and School Information as received by the $request Object
     * @param $userProfile
     * @param $request
     */
    public function storeClubSchoolInformation($userProfile,$request)
    {
        //Store Club Information
        $this->storeCareer($userProfile,$request, SiteConstants::CAREER_TYPE_CLUB);

        //Store School Information
        $this->storeCareer($userProfile,$request, SiteConstants::CAREER_TYPE_SCHOOL);
    }


    /**
     *Getting the extra parameters of a user profile and modifying it in a form such as [dominant_hand:right|speed_40:50], etc.
     * @param $request
     */
    public function getParamsDataForProfile($request)
    {
        $paramsData = "";
        $userProfileParamsKeys = SportsRepository::getExtraParamsKeysUserProfile();

        foreach($userProfileParamsKeys as $key=>$value)
        {
            if($request->has($value))
            {
                $paramsData.= $value.":".$request->get($value)."|";
            }
        }

        return count($paramsData)>0 ? substr($paramsData,0,-1) : $paramsData;
    }


    /**
     * Store the full information of Career, statistics and references.
     * Also, save career based upon career type
     * @param $request
     * @param $career_type
     */
    public function storeCareer($userProfile,$request,$career_type)
    {
        $sportDataMap = $this->getSportDataMap(Session::get(SiteSessions::USER_SPORT_TYPE));
        $formRequest = $request->all();
        $careerStatisticsType="";
        $careerInformationFillAbles="";
        $careerTypeSportDataMap="";
        $careerCounts="";

        $existingCareer="";
        $existingCareerCount="";

        $fieldPrefix="";

        switch($career_type)
        {
            case SiteConstants::CAREER_TYPE_CLUB:
                //Store Club Information
                $careerInformationFillAbles = TalentCareerInformation::$club_fillable;
                $careerTypeSportDataMap=SportsRepository::getClubDataMap($this->getSportDataMap(Session::get(SiteSessions::USER_SPORT_TYPE)));
                $careerCounts = count($formRequest["club_club_school_name"]);
                $fieldPrefix="club_";
                $existingCareer=$userProfile->careerInformation()->where('career_type','=',SiteConstants::CAREER_TYPE_CLUB)->get();
                $existingCareerCount = count($existingCareer);
                break;

            case SiteConstants::CAREER_TYPE_SCHOOL:
                //Store Club Information
                $careerInformationFillAbles = TalentCareerInformation::$school_fillable;
                $careerTypeSportDataMap=SportsRepository::getSchoolDataMap($this->getSportDataMap(Session::get(SiteSessions::USER_SPORT_TYPE)));
                $careerCounts = count($formRequest["school_club_school_name"]);
                $fieldPrefix="school_";
                $existingCareer=$userProfile->careerInformation()->where('career_type','=',SiteConstants::CAREER_TYPE_SCHOOL)->get();
                $existingCareerCount = count($existingCareer);
                break;

            default:
                return false;
        }

//        var_dump($careerTypeSportDataMap);
//        dd($formRequest);
        /*
         * For loop for storing Career Information
         */
        for($i=0;$i<$careerCounts;$i++)
        {
            $talentCareerInformation="";
            if($i<$existingCareerCount)
            {
                $talentCareerInformation=$existingCareer[$i];
            }
            else if($i>=$existingCareerCount)
            {
                $talentCareerInformation=new TalentCareerInformation();
            }

            $talentCareerInformation->career_type = $career_type;
            foreach($careerInformationFillAbles as $value)
            {
                $requestField=$fieldPrefix.$value;
                $talentCareerInformation->$value = isset($formRequest[$requestField][$i]) ? $formRequest[$requestField][$i] : null ;
            }

            if($i>=$existingCareerCount)
            {
                $talentCareerInformation=$userProfile->careerInformation()->create($talentCareerInformation->toArray());
            }
            $talentCareerInformation->save();

            /*
             * This code is for storing Sport statistics in the database
             */
            $sportStatisticsCount = count($formRequest[array_values($careerTypeSportDataMap)[0]][$i]);

            $existingSportStatistics = $talentCareerInformation->careerSportStatistics(Session::get(SiteSessions::USER_SPORT_TYPE))->get();

            for($j=0;$j<$sportStatisticsCount;$j++)
            {
                $careerStatistics="";
                if($j<count($existingSportStatistics))
                {
                    $careerStatistics=$existingSportStatistics[$j];
                }
                else if($j>=count($existingSportStatistics))
                {
                    $careerStatistics = $this->getCareerStatisticsModelObject(Session::get(SiteSessions::USER_SPORT_TYPE));
                }

                $careerStatistics->statistic_type=$career_type;
                foreach($careerTypeSportDataMap as $key=>$value)
                {
                    $statisticField = $sportDataMap[$key];
                    $careerStatistics->$statisticField = $formRequest[$careerTypeSportDataMap[$key]][$i][$j];
                }

                if($j>=count($existingSportStatistics))
                {
                    $careerStatistics = $talentCareerInformation->careerSportStatistics(Session::get(SiteSessions::USER_SPORT_TYPE))->create($careerStatistics->toArray());
                }
                $careerStatistics->save();
            }
            if (count($existingSportStatistics) > $sportStatisticsCount)
            {
                for($k=$sportStatisticsCount;$k<count($existingSportStatistics);$k++)
                {
                    $existingSportStatistics[$k]->delete();
                }
            }

            /*
             * This code is for storing references in the database
             */
            $clubReferenceCount = count($formRequest[$fieldPrefix.'name'][$i]);
            $existingReference = $talentCareerInformation->careerReferences()->get();

            for($j=0;$j<$clubReferenceCount;$j++)
            {
                $careerReference ="";
                if($j<count($existingReference))
                {
                    $careerReference = $existingReference[$j];
                }
                else if($j>=count($existingReference))
                {
                    $careerReference = new TalentCareerReferences();
                }
                foreach($careerReference->getFillAbleField() as $value)
                {
                    $inputField = $fieldPrefix.$value;
                    $careerReference->$value = $formRequest[$inputField][$i][$j];
                }

                if($j>=count($existingReference))
                {
                    $careerReference = $talentCareerInformation->careerReferences()->create($careerReference->toArray());
                }
                $careerReference->save();
            }
            if (count($existingReference) > $clubReferenceCount)
            {
                for($k=$clubReferenceCount;$k<count($existingReference);$k++)
                {
                    $existingReference[$k]->delete();
                }
            }
        }

        if ($existingCareerCount > $careerCounts)
        {
            for($i=$careerCounts;$i<$existingCareerCount;$i++)
            {
                $existingCareer[$i]->delete();
            }
        }
        //dd($formRequest);

//        var_dump($careerTypeSportDataMap);
//        dd($request->all());
    }


    /**
     * Retrieve the sport data map, based upon the sports type of the user
     * @param $sportType
     */
    public function getSportDataMap($sportType)
    {
        switch($sportType)
        {
            case SportsRepository::BASEBALL:
                return BaseBallStatistics::$dataMap;
                break;
            case SportsRepository::BASKETBALL:
                return BasketBallStatistics::$dataMap;
                break;
            case SportsRepository::FOOTBALL:
                return BaseBallStatistics::$dataMap;
                break;
            case SportsRepository::RUGBY:
                return RugbyStatistics::$dataMap;
                break;
            case SportsRepository::SOCCER:
                return SoccerStatistics::$dataMap;
                break;
            case SportsRepository::SOFTBALL:
                return BaseBallStatistics::$dataMap;
                break;
            case SportsRepository::SWIMMING:
                return SwimmingStatistics::$dataMap;
                break;
            case SportsRepository::TENNIS:
                return TennisStatistics::$dataMap;
                break;
            case SportsRepository::TRACK_FIELD:
                return TrackAndFieldStatistics::$dataMap;
                break;
            case SportsRepository::WATERPOLO:
                return BaseBallStatistics::$dataMap;
                break;

            default:
                return false;
                break;
        }
    }



    /**
     * Creating a new career statistics object based upon the sports type and returning the corresponding model
     * @param $sportType
     */
    public function getCareerStatisticsModelObject($sportType)
    {
        switch($sportType)
        {
            case SportsRepository::BASEBALL:
                return new BaseBallStatistics();
                break;
            case SportsRepository::BASKETBALL:
                return new BaseBallStatistics;
                break;
            case SportsRepository::FOOTBALL:
                return new BaseBallStatistics;
                break;
            case SportsRepository::RUGBY:
                return new BaseBallStatistics;
                break;
            case SportsRepository::SOCCER:
                return new BaseBallStatistics;
                break;
            case SportsRepository::SOFTBALL:
                return new BaseBallStatistics;
                break;
            case SportsRepository::SWIMMING:
                return new BaseBallStatistics;
                break;
            case SportsRepository::TENNIS:
                return new BaseBallStatistics;
                break;
            case SportsRepository::TRACK_FIELD:
                return new BaseBallStatistics;
                break;
            case SportsRepository::WATERPOLO:
                return new BaseBallStatistics;
                break;

            default:
                return false;
                break;
        }
    }


    /**
     * --------- Need to implement this logic ------- Logic is that Validate data, if errors then create a session's flash
     * and store all the user data in it and then make changes on the view to reflect it
     *
     * Validating data for CV
     * @param $request
     */
    public function validateCVData($request)
    {
        $rules=[];
        if($request->has("positions"))
        {
            foreach($request->get("positions") as $key=>$value)
            {
                $rules['positions.'.$key]="in:".implode(",",array_keys(SportsRepository::getSportPositions(Session::get(SiteSessions::USER_SPORT_TYPE))));
            }
            $rules['preferred_position']='in:'.implode(",",array_keys(SportsRepository::getSportPositions(Session::get(SiteSessions::USER_SPORT_TYPE))));
        }

        $validator = Validator::make($request->all(),$rules);

        if($validator->fails())
        {
            //dd($validator->errors());
        }

        //dd($validator);


    }


    /**
     *Check is directory exists or not , if not then creates the directories
     */
    public function createImageSourceDirectories($type)
    {
        //Creating Directories for User Profile Images
        if($type == StorageLocationsRepository::USER_DIRECTORY_TYPE_PROFILE_IMAGES)
        {
            if(!file_exists(public_path().StorageLocationsRepository::USER_PROFILE_IMAGE_PATH))
            {
                $result = File::makeDirectory(public_path().StorageLocationsRepository::USER_PROFILE_IMAGE_PATH, 0775, true);
            }
            if(!file_exists(public_path().StorageLocationsRepository::USER_PROFILE_IMAGE_SMALL_PATH))
            {
                $result = File::makeDirectory(public_path().StorageLocationsRepository::USER_PROFILE_IMAGE_SMALL_PATH, 0775, true);
            }
            if(!file_exists(public_path().StorageLocationsRepository::USER_PROFILE_IMAGE_ICON_PATH))
            {
                $result = File::makeDirectory(public_path().StorageLocationsRepository::USER_PROFILE_IMAGE_ICON_PATH, 0775, true);
            }
        }
        //Creating Directories for User Cover Profile Images
        else if($type == StorageLocationsRepository::USER_DIRECTORY_TYPE_COVER_IMAGES)
        {
            if(!file_exists(public_path().StorageLocationsRepository::USER_COVER_IMAGE_PATH))
            {
                $result = File::makeDirectory(public_path().StorageLocationsRepository::USER_COVER_IMAGE_PATH, 0775, true);
            }
        }
    }


    /**
     * Updating the profile Summary of a user
     * @param $request
     */
    public function updateProfileSummary(Request $request)
    {
        if(!Auth::check())
        {
            return response()->json(['status'=>'error','type'=>'user_not_found']);
        }

        $validator = Validator::make($request->all(),['summary'=>'required']);
        if($validator->fails())
        {
            return response()->json(['status'=>'error','type'=>'summary_not_provided']);
        }

        $userProfile = UserProfile::find(Session::get(SiteSessions::USER_ID));
        $userProfile->summary = $request["summary"];

        $userProfile->save();

        return response()->json(['status'=>'successful']);

    }


    /**
     * Updating the profile Awards of a user
     * @param $request
     */
    public function updateProfileAwards(Request $request)
    {
        if(!Auth::check())
        {
            return response()->json(['status'=>'error1','type'=>'user_not_found']);
        }

        $validator = Validator::make($request->all(),['award_details'=>'required']);
        if($validator->fails())
        {
            return response()->json(['status'=>'error2','type'=>'summary_not_provided']);
        }

        $userProfile = UserProfile::find(Session::get(SiteSessions::USER_ID));
        $awards = $userProfile->awards;
        if($awards==null)
        {
            $awards=$userProfile->awards()->create($request->all());
        }
        else
        {
            $awards->award_details = $request->award_details;
        }

        $awards->save();

        return response()->json(['status'=>'successful']);

    }


    /**
     *Showing User's Profile, A user can be manager or talent
     */
    public function showUserProfile($id)
    {
        Blade::setContentTags('<%', '%>');        // for variables and all things Blade
        Blade::setEscapedContentTags('<%%', '%%>');   // for escaped data

        if($this->checkIfGuestDoNotHavePermission($id,UserSettings::PRIVACY_TYPE_PROFILE))
        {
            return redirect('/');
        }

        $userProfile = UserProfile::find($id);

        $userProfile->getMutatedData = false;

        $country=BasicSiteRepository::getListOfCountries();
//        $sportPositions=array_map('ucfirst',SportsRepository::getSportPositions(Session::get(SiteSessions::USER_SPORT_TYPE)));

        $userCareerHistory=$userProfile->careerInformation;
        $awards = $userProfile->awards;

        if($awards == null)
        {
            $awards = new Awards();
        }

        $endorsements = $userProfile->endorsements()->take(10)->get();

        $visitingUserEndorsed = $userProfile->endorsements()->wherePivot('user_id','=',$userProfile->user_id)
            ->wherePivot('endorsement_by','=',Session::get(SiteSessions::USER_ID))
            ->get();

        if(count($visitingUserEndorsed)<=0)
        {
            $visitingUserEndorsed=0;
        }
        else
        {
            $visitingUserEndorsed=1;
        }

        //Setting whether profile is editable or not.
        $profileEditable = false;
        if($userProfile->user_id == Session::get(SiteSessions::USER_ID))
        {
            $profileEditable=true;
        }

        //Setting whether profile is favourited by visiting user or not
        $visitingUserFavourited = DB::table('favourite')
            ->where('user_id','=',Session::get(SiteSessions::USER_ID))
            ->where('favourited_to','=',$userProfile->user_id)
            ->get();


        if(count($visitingUserFavourited)>0)
        {
            $visitingUserFavourited=1;
        }
        else
        {
            $visitingUserFavourited=0;
        }

        return view('profile.user_profile',
            compact('userProfile','country','userCareerHistory','awards','endorsements',
                'visitingUserEndorsed','profileEditable','visitingUserFavourited'));
    }


    /**
     * Function to show all list of evangelists
     * @param $id
     */
    public function allEvangelists($id)
    {
        $userProfile = UserProfile::find($id);

        $userProfile->getMutatedData = false;

        $endorsements = $userProfile->endorsements;

        return view('profile.evangelists',compact('userProfile','endorsements'));
    }


    /**
     * Endorsing User
     * @param Request $request
     */
    public function endorseUser(Request $request)
    {
        if(!Auth::check())
        {
            return response()->json(['status'=>'error','type'=>'user_not_logged_in']);
        }

        $validator = Validator::make(Input::all(),[
            'user_id'=> 'required',
        ]);

        if($validator->fails())
        {
            return response()->json(['status'=>'error','type'=>'validation_error']);
        }


        try
        {
            $userProfile = UserProfile::find($request->user_id);
            $userProfile->endorsements()->attach(Session::get(SiteSessions::USER_ID));
            $userProfile->save();
            Event::fire(new NotifyUser($request->user_id,Session::get(SiteSessions::USER_ID),Notifications::NOTIFICATION_TYPE_ENDORSEMENT,null));
        }
        catch(ModelNotFoundException $e)
        {
            return response()->json(['status'=>'error','type'=>'user_not_found']);
        }

        return response()->json(['status'=>'successful']);
    }


    /**
     * Showing CV information of a talent or Manager
     * @param $user_id
     */
    public function viewCV($user_id)
    {
        if($this->checkIfGuestDoNotHavePermission($user_id,UserSettings::PRIVACY_TYPE_PROFILE))
        {
            return redirect('/');
        }

        $userProfile = null;
        try
        {
            $userProfile = UserProfile::findOrFail($user_id);
            $userProfile->getMutatedData = false;
        }
        catch(ModelNotFoundException $e)
        {
            return abort(404);
        }

        if(SiteConstants::isTalent($userProfile->user_type))
        {
            $talentProfile=$userProfile;

            return view('profile.talent.viewTalentCV',compact('talentProfile'));
        }
        else if(SiteConstants::isManager($userProfile->user_type))
        {
            $managerProfile=ManagerProfile::find($userProfile->user_id);

            $careerInformation = $managerProfile->managerCareerInformation;

            return view('profile.manager.viewManagerCV',compact('managerProfile','careerInformation'));
        }

        return redirect('profile');

    }


    /**
     * Function to favourite User with id contained in $request object
     * Favourited by User with current session
     * @param $favouriting_user_id
     */
    public function favouriteUser(Request $request)
    {

        if(!Auth::check())
        {
            return response()->json(['status'=>'error','type'=>'user_not_logged_in']);
        }

        $validator = Validator::make(Input::all(),[
            'connect_to_user_id'=> 'required',
        ]);

        if($validator->fails())
        {
            return response()->json(['status'=>'error','type'=>'validation_error']);
        }

        $operation_performed = "";

        try
        {
            $userProfile = UserProfile::find(Session::get(SiteSessions::USER_ID));
            $isAlreadyFavourite = $userProfile->favourites()->where('favourited_to','=',$request->connect_to_user_id)->get();
            if(count($isAlreadyFavourite)>0)
            {
                //$userProfile->favourites()->detach($request->connect_to_user_id);
                $userProfile->favourites()->where('favourited_to','=',$request->connect_to_user_id)->delete();
                $operation_performed = "removed";
            }
            else
            {
//                $userProfile->favourites()->attach($request->connect_to_user_id);
                $userProfile->favourites()->create(['favourited_to'=>$request->connect_to_user_id])
                    ->save();
                $operation_performed = "added";
                Event::fire(new NotifyUser($request->connect_to_user_id,Session::get(SiteSessions::USER_ID),Notifications::NOTIFICATION_TYPE_FAVOURITE,null));
            }
            //$userProfile->save();
        }
        catch(ModelNotFoundException $e)
        {
            return response()->json(['status'=>'error','type'=>'user_not_found']);
        }
        catch(Exception $e)
        {
            return $e;
        }

        return response()->json(['status'=>'successful','operation_done_type'=>$operation_performed]);
    }


    /**
     *Showing List of favourites of a user
     */
    public function showFavourites($user_id)
    {
        $userProfile = UserProfile::find($user_id);

        $userProfile->getMutatedData = false;

        $favourites = $userProfile->favourites()->join('users','users.user_id','=','favourite.favourited_to')
            ->get();
        return view('profile.favourites',compact('userProfile','favourites'));
    }

    /**
     * Showing List of User who favourited you
     * @param $user_id
     * @return \Illuminate\View\View
     */
    public function showWhoFavouritedYou($user_id)
    {
        $userProfile = UserProfile::find($user_id);

        $userProfile->getMutatedData = false;

//        $favouritedYou = DB::table('favourite')->join('users','users.user_id','=','favourite.user_id')
//            ->where('favourite.favourited_to','=',$userProfile->user_id)
//            ->get();

        $favouritedYou = $userProfile->favouritedByOthers()->join('users','users.user_id','=','favourite.user_id')
            ->get();

        return view('profile.who_favourited_you',compact('userProfile','favouritedYou'));
    }


    /**
     * Notification is read by a user, so status of the notification is updated in the database
     * @param Request $request
     */
    public function notificationReadByUser(Request $request)
    {
        if(!$request->has("notification_id"))
        {
            return response()->json(['status'=>'error','error_type'=>'NOTIFICATION_ID_MISSING']);
        }

        try
        {
            $notification = Notifications::find($request->notification_id);
            $notification->status = Notifications::NOTIFICATION_STATUS_READ;
            $notification->save();
        }
        catch(ModelNotFoundException $e)
        {
            return response()->json(['status'=>'error','error_type'=>'NOTIFICATION_NOT_FOUND']);
        }

        return response()->json(['status'=>'successful']);
    }


    /**
     * Checking if a user/Guest have permission for a given part or not
     * @param $profile_id
     * @param $privacy_type
     * @return boolean
     */
    public function checkIfGuestDoNotHavePermission($profile_id,$privacy_type)
    {
        $userProfileSetting = UserSettings::where('user_id','=',$profile_id)
            ->where('setting_type','=',$privacy_type)->first();

        if(Auth::guest() && $userProfileSetting != null && $userProfileSetting->setting_value == UserSettings::PRIVACY_SET)
        {
            return true;
        }
        return false;
    }


    /**
     *User account is not confirmed. Displaying message to user
     */
    public function accountNotConfirmed()
    {
        return view('account_not_confirmed');
    }

    /**
     *Resending confirmation link to the User
     */
    public function resendConfirmationLink()
    {
        $user = User::find(Session::get(SiteSessions::USER_ID));
        $user->confirmation_token = bcrypt(time());
        $user->save();
        Event::fire(new SendMail(SendMail::MAIL_TYPE_USER_CONFIRMATION,$user->username,[],$user));
        Session::flash("resent_confirmation_link",'successful');
        return redirect('account-not-confirmed');
    }


    /**
     * Setting uploaded Image position - Either Cover Image or Profile Image
     * @param $id
     */
    public function uploadedImageSetting($id)
    {
        $image_type = Session::has('image_type') ? Session::get('image_type') : "";
        $userProfile = UserProfile::find(Session::get(SiteSessions::USER_ID));
        return view('profile.configure_cover_image',compact('userProfile','image_type'));
    }


    /**
     *Saving Uploaded Image Settings - Either for Cover image or profile image
     */
    public function saveUploadedImageSetting(Request $request)
    {
        if($request->image_type == "profile_image")
        {
            $userMeta = UserMeta::profileImageTop()->first();
            $userMeta = count($userMeta) == 0 ? new UserMeta() : $userMeta;

            $userMeta->user_id = Session::get(SiteSessions::USER_ID);
            $userMeta->meta_type = UserMetaRepository::PROFILE_IMAGE_TOP_POSITION;
            $userMeta->meta_value = $request->top_adjusted;
            $userMeta->save();

            $userMeta = UserMeta::profileImageLeft()->first();
            $userMeta = count($userMeta) == 0 ? new UserMeta() : $userMeta;

            $userMeta->user_id = Session::get(SiteSessions::USER_ID);
            $userMeta->meta_type = UserMetaRepository::PROFILE_IMAGE_LEFT_POSITION;
            $userMeta->meta_value = $request->left_adjusted;
            $userMeta->save();
        }
        else if($request->image_type == "cover_image")
        {
            $userMeta = UserMeta::coverImageTop()->first();
            $userMeta = count($userMeta) == 0 ? new UserMeta() : $userMeta;

            $userMeta->user_id = Session::get(SiteSessions::USER_ID);
            $userMeta->meta_type = UserMetaRepository::COVER_IMAGE_TOP_POSITION;
            $userMeta->meta_value = $request->top_adjusted;

            $userMeta->save();
        }

        return redirect('profile');
    }


}
