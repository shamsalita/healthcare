<?php

namespace App\Http\Controllers\frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Connect,Education,Experience,Language,Skill,User,JodPost,JobSkill};
use Illuminate\Support\Facades\{Auth,Crypt,DB,URL};


 class ProfileController extends Controller
{
    /* middeleware for verifying login or not */
    protected $id;
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->id = Auth::user()->id;
            return $next($request);
        });
    }

     /* Display Job Dashboard */ 
     public function job_listing()
     {
         $all_skills = Skill::all();
         $jobs = JodPost::where('user_id', $this->id)->where('status',1)->get();
         $skill =JobSkill::where('job_id',$this->id)->pluck('skill_id');

         return view('layouts.job_edit', compact('jobs','all_skills','skill'));
     }

    /*Store Job Data in Database */ 

    public function job_store(Request $request){

        $request->validate(
            [
                'title' => 'required',
                'skills' => 'required',
                'work_period'=>'required',
                'experience'=>'required',
                'hourly_rate'=>'required',
            ]
        );

        try {
       
          //Cover Image Upload
          if ($request->attach_file) {
            $fileName = time() . '_' . $request->attach_file->getClientOriginalName();
            $files = $request->attach_file->move(public_path() . '/assets/images/JobImage/', $fileName);
        } else {
            $fileName = "";
        }
        $jobData = JodPost::updateOrCreate(
            ['id'=>decryptid($request['id'])],[
            'user_id'=>Auth::user()->id,
            'title' => $request->title,
            'work_period' =>$request->work_period, 
            'experience' => $request->experience,
            'hourly_rate' => $request->hourly_rate,
            'attach_file' =>$fileName,
         ]);

         $id =  $jobData->id;
         $job = JodPost::find($id);
         $skill = $job->job_skill()->sync($request->skills);

         $response = [
            'status' => true,
            'message' => 'Job Data '.(decryptid($request['id'])==0 ? 'Added' : 'Updated').' Successfully',
            'icon' => 'success',
            'redirect_url' => 'profile/'.auth()->user()->remember_token,
        ];
    } catch (\Throwable $e) {
        $response = [
            'status' => false,
            'message' => 'Something Went Wrong! Please Try Again.',
            'icon' => 'error',
        ];
    }
    return response($response);
 }

 public function job_update(Request $request)
    {
     try {
            $id = decryptid($request['id']);
            $data['job'] = JodPost::where('id', $id)->first();
            $data['skill'] =JobSkill::where('job_id',$id)->pluck('skill_id');
                if (!is_null($data)) {
                    $response['data'] = $data;
                    $response['status'] = [
                        'status' => true,
                    ];
                } 
            
         } catch (\Throwable $e) {
             $response = [
                 'status' => false,
                 'message' => 'Something Went Wrong! Please Try Again.',
                 'icon' => 'error',
             ];
         }
         return response($response);
    }

    /* Deleting Job */
    public function job_delete(Request $request)
    {
        try {
        $update['status']=0;
        $job = JodPost::where('id',decryptid($request['id']))->update($update);
            $response = [
                'status' => true,
                'message' => "Job Data Deleted Successfully",
                'icon' => 'success',
                'redirect_url' => 'profile/'.auth()->user()->remember_token,
            ];
         
    } catch (\Throwable $e) {
            $response = [
                'status' => false,
                'message' => 'Something Went Wrong! Please Try Again.',
                'icon' => 'error',
            ];
        }
        return response($response);
    }


    // * Author : Rajvi 
    // * Date : 30/04/22
    // * added index,about,experience_listing and experience_store
    
    /* view of profile */
    public function index()
    {
        $url_parts = explode('/', URL::current());
        $token = $url_parts[count($url_parts)-1];
        $user= User::with(['education','experience','skill','language'])->with('from_connections',function($query){
            $query->where('to_id',$this->id);
        })->with('to_connections',function($query){
            $query->where('from_id',$this->id);
        })->where('remember_token',$token)->first();
        $all_skills = Skill::all();
        $all_languages = Language::all();
        return view('layouts.profile', compact('user', 'all_skills', 'all_languages'));
    }

    /* updating user details to database */
    public function user(Request $request)
    {
        $request->validate(
            [
                'first_name' => 'required',
                'last_name' => 'required',
                'grade_primary' => 'required',
                'country' => 'required',
            ]
        );
        $user = User::where('id', $this->id)->first();
        $user->first_name = $request['first_name'];
        $user->last_name = $request['last_name'];      
        $user->grade_primary = $request['grade_primary'];
        if (!is_null($request['grade_secondary'])) {
            $user->grade_secondary = $request['grade_secondary'];
        }
        $user->residential_country = $request['country'];
        if (!is_null($request['city'])) {
            $user->residential_city = $request['city'];
        }
        $user->updated_at=now();
         if(Auth::user()->user_status==0){
            $request->validate(
                [
                    'medical_no' => 'required',
                    'current_position' => 'required',
                    'education' => 'required',
                ]
            );
            $user->medical_no = $request['medical_no'];
            $experience = Experience::where('id',$request['current_position'])->first();
            $experience->flag = '1';
            DB::table('experiences')->where('id','!=',$request['current_position'])->update(['flag' => '0']);
            $education = Education::where('id',$request['education'])->first();
            $education->flag = '1';
            DB::table('education')->where('id','!=',$request['education'])->update(['flag' => '0']);
            $experience->save();
            $education->save();

        }else{
            $request->validate(
                [
                    'hospital_name' => 'required',
                    'headline' => 'required',
                ]
            );
            $user->headline = $request['headline'];
            $user->hospital_name = $request['hospital_name'];
        }
        if ($user->save()) {
            $response = [
                'status' => true,
                'message' => "user data updated successfully",
                'icon' => 'success',
                'redirect_url' => 'profile/'.auth()->user()->remember_token,
            ];
            echo json_encode($response);
            exit;
        } else {
            $response = [
                'status' => false,
                'message' => "error in updating",
                'icon' => 'error',
                'redirect_url' => 'profile/'.auth()->user()->remember_token,
            ];
            echo json_encode($response);
            exit;
        }
    }

    /* store about to database */
    public function about(Request $request)
    {
        $request->validate(
            ['about' => 'required']
        );
        $user = User::where('id', $this->id)->first();
        $user->about = $request['about'];
        $user->updated_at = now();
        $user->save();
        if ($user->save()) {
            $response = [
                'status' => true,
                'message' => "about updated successfully",
                'icon' => 'success',
                'redirect_url' => 'profile/'.auth()->user()->remember_token,
            ];
            echo json_encode($response);
            exit;
        } else {
            $response = [
                'status' => false,
                'message' => "error in updating",
                'icon' => 'error',
                'redirect_url' => 'profile/'.auth()->user()->remember_token,
            ];
            echo json_encode($response);
            exit;
        }
    }

    /* display experience dashboard */ 
    public function experience_listing()
    {
        $experiences = Experience::where('u_id', $this->id)->where('status', 1)->get();
        return view('layouts.experience_edit', compact('experiences'));
    }

    /* store experience to database */ 
    public function experience_store(Request $request)
    {
        $request->validate(
            [
                'position' => 'required',
                'name' => 'required',
                'start_date' => 'required',
            ]
        );
        $id = decryptid($request['id']);
        $experience = ($id == 0) ?  (new Experience()) : (Experience::where('id', $id)->first());
        $experience->u_id = $this->id;
        $experience->position = $request['position'];
        if (!is_null($request['employment_type'])) {
            $experience->employment_type = $request['employment_type'];
        }
        $experience->name = $request['name'];
        if (!is_null($request['logo'])) {
            $logo = $request['logo'];
            $name = $logo->getClientOriginalName();
            $allowedlogoExtension = ['jpg', 'png', 'jpeg'];
            $extension = $logo->getClientOriginalExtension();
            $check = in_array($extension, $allowedlogoExtension);
            if ($check) {
                $image['filePath'] = time() . $name;
                $logo->move(public_path() . '/assets/images/hospital/hospital_logo/', $image['filePath']);
                $experience->logo = $image['filePath'];
            }
        }
        $experience->country = $request['country'];      
        $experience->city = $request['city'];
        $experience->start_date = $request['start_date'];
        $experience->end_date = $request['end_date'];
        $experience->description = $request['description'];
        $result = ($id == 0) ? $experience->save() : $experience->update();
        if ($result) {
            $response = [
                'status' => true,
                'message' => 'experience '.($id==0 ? 'added' : 'updated ').' successfully',
                'icon' => 'success',
                'redirect_url' => 'profile/'.auth()->user()->remember_token,
            ];
            echo json_encode($response);
            exit;
        } else {
            $response = [
                'status' => false,
                'message' => "error in updating",
                'icon' => 'error',
                'redirect_url' => 'profile/'.auth()->user()->remember_token,
            ];
            echo json_encode($response);
            exit;
        }
    }

    /* fetch experience data from database for editing */ 
    public function experience_data(Request $request)
    {
        $id = decryptid($request['id']);
        $experience = Experience::where('id', $id)->first();
        if (!is_null($experience)) {
            $response['data'] = $experience;
            $response['status'] = [
                'status' => true,
            ];
            echo json_encode($response);
            exit;
        } else {
            $response = [
                'status' => false,
                'message' => "error in fetching",
                'icon' => 'error',
                'redirect_url' => 'profile/'.auth()->user()->remember_token,
            ];
            echo json_encode($response);
            exit;
        }
    }

    // * Author : Rajvi 
    // * Date : 30/04/22
    // * end
    
    // * Author : Rajvi 
    // * Date : 2/05/22
    // * added education 

    /* display education dashboard */ 
    public function education_listing()
    {
        $educations = Education::where('u_id', $this->id)->where('status', 1)->get();
        return view('layouts.education_edit', compact('educations'));
    }

    /* store education to database */ 
    public function education_store(Request $request)
    {
        $request->validate(
            [
                'name' => 'required',
                'start_date' => 'required',
            ]
        );
        $id = decryptid($request['id']);
        $education = ($id == 0) ? (new Education()) : (Education::where('id', $id)->first());
        $education->u_id = $this->id;
        $education->name = $request['name'];
        $education->degree = $request['degree'];
        if (!is_null($request['school_logo'])) {
            $school_logo = $request['school_logo'];
            $name = $school_logo->getClientOriginalName();
            $allowedschool_logoExtension = ['jpg', 'png', 'jpeg'];
            $extension = $school_logo->getClientOriginalExtension();
            $check = in_array($extension, $allowedschool_logoExtension);
            if ($check) {
                $image['filePath'] = time() . $name;
                $school_logo->move(public_path() . '/assets/images/school/logo/', $image['filePath']);
                $education->logo = $image['filePath'];
            }
        }
        $education->grade = $request['grade'];
        $education->start_date = $request['start_date'];
        $education->end_date = $request['end_date'];
        $education->description = $request['description'];
        $result = ($id == 0) ? $education->save() : $education->update();
        if ($result) {
            $response = [
                'status' => true,
                'message' => 'education '.($id==0 ? 'added' : 'updated').' successfully',
                'icon' => 'success',
                'redirect_url' => 'profile/'.auth()->user()->remember_token,
            ];
            echo json_encode($response);
            exit;
        } else {
            $response = [
                'status' => false,
                'message' => "error in updating",
                'icon' => 'error',
                'redirect_url' => 'profile/'.auth()->user()->remember_token,
            ];
            echo json_encode($response);
            exit;
        }
    }

    /* fetch education data from database for editing */ 
    public function education_data(Request $request)
    {
        $id = decryptid($request['id']);
        $education = Education::where('id', $id)->first();
        if (!is_null($education)) {
            $response['data'] = $education;
            $response['status'] = [
                'status' => true,
            ];
            echo json_encode($response);
            exit;
        } else {
            $response = [
                'status' => false,
                'message' => "error in fetching",
                'icon' => 'error',
                'redirect_url' => 'profile/'.auth()->user()->remember_token,
            ];
            echo json_encode($response);
            exit;
        }
    }

    // * Author : Rajvi 
    // * Date : 2/05/22
    // * end 

    // * Author : Rajvi 
    // * Date : 3/05/22
    // * fetch user data 

    /* fetch user data from database for editing user details */ 
    public function user_data(Request $request)
    {
        $user = User::where('id', $this->id)->with('experience',function ($query) {
            $query->where('flag','=','1')->first();
        })->with('education',function ($query) {
            $query->where('flag','=','1')->first();
        })->first();
        if (!is_null($user)) {
            $response['data'] = $user;
            $response['status'] = [
                'status' => true,
            ];
            echo json_encode($response);
            exit;
        } else {
            $response = [
                'status' => false,
                'message' => "error in fetching",
                'icon' => 'error',
                'redirect_url' => 'profile/'.auth()->user()->remember_token,
            ];
            echo json_encode($response);
            exit;
        }
    }

    // * Author : Rajvi 
    // * Date : 3/05/22
    // * end 

    // * Author : Rajvi 
    // * Date : 4/05/22
    // * delete education and experience

    /* deleting experience */
    public function experience_delete(Request $request)
    {
        $id = decryptid($request['id']);
        $experience = Experience::where('id',$id)->where('status',1)->first();
        $experience->status=0;
        $experience->updated_at=now();
        if($experience->save()){
            $response = [
                'status' => true,
                'message' => "experience deleted successfully",
                'icon' => 'success',
                'redirect_url' => 'profile/'.auth()->user()->remember_token,
            ];
            echo json_encode($response);
            exit;
        }else {
            $response = [
                'status' => false,
                'message' => "error in deleting",
                'icon' => 'error',
                'redirect_url' => 'profile/'.auth()->user()->remember_token,
            ];
            echo json_encode($response);
            exit;
        }
    }

    /* deleting education */
    public function education_delete(Request $request)
    {
        $id = decryptid($request['id']);
        $education = Education::where('id',$id)->where('status',1)->first();
        $education->status='0';
        $education->updated_at=now();
        if($education->save()){
            $response = [
                'status' => true,
                'message' => "education deleted successfully",
                'icon' => 'success',
                'redirect_url' => 'profile/'.auth()->user()->remember_token,
            ];
            echo json_encode($response);
            exit;
        }else {
            $response = [
                'status' => false,
                'message' => "error in deleting",
                'icon' => 'error',
                'redirect_url' => 'profile/'.auth()->user()->remember_token,
            ];
            echo json_encode($response);
            exit;
        }
    }
    // * Author : Rajvi 
    // * Date : 4/05/22
    // * delete education and experience

    /*
        * Author : kishan 
        * Date : 29/04/22
        * Added skills and language function
    */

    public function skill(Request $request)
    {
        $request->validate([
            'skills' => 'required'
        ]);
        $user = User::find($request->id);
        $user->skill()->sync($request->skills);

        $response = [
            'status' => true,
            'message' => "skills updated successfully ",
            'icon' => 'success',
            'redirect_url' => 'profile/'.auth()->user()->remember_token,
        ];
        echo json_encode($response);
        exit;
    }

    public function language(Request $request)
    {

        $request->validate([
            'languages' => 'required'
        ]);

        $user = User::find($request->id);
        $user->language()->sync($request->languages);

        $response = [
            'status' => true,
            'message' => "skills updated successfully",
            'icon' => 'success',
            'redirect_url' => 'profile/'.auth()->user()->remember_token,
        ];
        echo json_encode($response);
        exit;
    }

    /* searching */
    public function search(Request $request)
    {
        // dd($request->all());
        $user=Auth::user();
        if(!is_null($request->location)){
            $user_status = $user->user_status==0  ? '1' : '0';
            $users = User::where('status',1)->where('user_status',$user_status)->where(function ($query) use ($request) {
                $query->where('address',$request->location)
                      ->orWhere('latitude',$request->latitude)->orWhere('longitude',$request->longitude);
                })->with('from_connections',function($query){
                    $query->where('to_id',$this->id);
                })->with('to_connections',function($query){
                    $query->where('from_id',$this->id);
                })->get(); 
                
            // $query->where('residential_country',$request->location)
            //           ->orWhere('residential_city',$request->location);
            //     })->with('from_connections',function($query){
            //         $query->where('to_id',$this->id);
            //     })->with('to_connections',function($query){
            //         $query->where('from_id',$this->id);
            //     })->whereHas('from_connections', function ($query) use($user){
            //         $query->where('connect_status',1)->orwhere($user->from_connections==null);
            //     })->get(); 
                // $users_ = '';
                // foreach($users as $user){
                //     foreach($user->from_connections as $from_connection){
                //         $users_ .=$user->$from_connection->where('connect_status',1)->get();
                //     }
                // }
                // dd($users_);
//             $users =  DB::table('users')->leftjoin('connects','users.id','=','from_id')->where('connects.connect_status','!=',2)->/* where('users.status',1)->where('users.user_status',$user_status)-> */where(function ($query) use ($request) {
//                 $query->where('users.residential_country',$request->location)
//                       ->orWhere('users.residential_city',$request->location);
//                 })->get();
// dd($users);
            // $users = User::join('connect', 'connect.from_id', '=', 'users.id')
            // ->where('connect.connect_status', '1')
            // ->where('posts.status','active')
            // ->get(['users.*', 'posts.descrption']);
        }
        $response['data'] = $users;
        $response['status'] = [
            'status' => true,
        ];
        echo json_encode($response);
        exit;
    }

    /* connections and cancle connection */
    public function connect(Request $request)
    {
        if(isset($request->arr)){
            if(isset($request->arr[1])){
                $connect = Connect::where('id',$request->arr[1])->first();
                if($connect->from_id!=$this->id){
                    $connect->from_id = $this->id;
                    $connect->to_id = $request->arr[0];
                }
                $connect->status = 0;
            }else{
                $connect = new Connect;
                $connect->from_id = $this->id;
                $connect->to_id = $request->arr[0];
            }
            (isset($request->arr[1])) ? $connect->update() : $connect->save();
        }else{
            $connect = Connect::where('id',$request['connection_id'])->first();

            if(isset($request['accept_id'])){
                $connect->status = 1; 
            }
            elseif(isset($request['reject_id'])){
                $connect->status = 2; 
            }
            elseif(isset($request['block_id'])){
                $connect->connect_status = 2; 
            }
            $connect->update(); 

        }
        $response = [
            'status' => true,
            'flag' => $request['flag']==1
        ];
        echo json_encode($response);
        exit;
    }

    /* storing cover photo to database */
    public function cover_store(Request $request)
    {
        if(isset($request->image)){
            $folderPath = public_path('assets/images/users/users_cover/');
            $image_parts = explode(";base64,", $request->image);
            $image_type_aux = explode("image/", $image_parts[0]);
            $image_type = $image_type_aux[1];
            $image_base64 = base64_decode($image_parts[1]);
            $imageName = uniqid() . '.png';
            $imageFullPath = $folderPath.$imageName;
            file_put_contents($imageFullPath, $image_base64);
            User::where('id', $this->id)->update(['profile_cover' => $imageName]);
        }elseif(isset($request->profile_image)){
            $folderPath = public_path('assets/images/users/users_profile/');
            $image_parts = explode(";base64,", $request->profile_image);
            $image_type_aux = explode("image/", $image_parts[0]);
            $image_type = $image_type_aux[1];
            $image_base64 = base64_decode($image_parts[1]);
            $imageName = uniqid() . '.png';
            $imageFullPath = $folderPath.$imageName;
            file_put_contents($imageFullPath, $image_base64);
            User::where('id', $this->id)->update(['profile' => $imageName]);
        }
        
        $response = [
            'status' => true,
            'message' => "photo updated successfully",
            'icon' => 'success',
            'redirect_url' => 'profile/'.auth()->user()->remember_token,
        ];
        echo json_encode($response);
        exit;

    }
}