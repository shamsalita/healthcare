<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{User,Thumb,ContactUs};
use Illuminate\Support\Facades\Mail;
use Dirape\Token\Token;



class HospitalController extends Controller
{
     /*Dashboard Of Hospital*/
     public function index()
     {
         return view('admin.pages.hospital.hospital');
     }

    //Listing Data Of Hospital
    public function listing(){
        $data['hospital']= User::where('status',1)->where('user_status',1)->with('thumb')->get();
        $result = [];
        foreach ($data['hospital'] as $key=>$hospital) {
                $button = '';
                $button .= '<button class="edit_city btn btn-sm btn-success m-1"  data-id="'.$hospital['id'].'">
                <i class="mdi mdi-square-edit-outline"></i>
                </button>';

                $button .= '<button class="delete btn btn-sm btn-danger m-1" data-id="'.$hospital['id'].'">
                <i class="mdi mdi-delete"></i>
                </button>';

                $result[] = array(
                    "0"=>$key+1,
                    "1"=>ucfirst($hospital->name),
                    "2"=>$hospital->email,
                    "3"=>$hospital->phone_number,
                    "4"=>$hospital->address,
                    "5"=>$hospital->latitude,
                    "6"=>$hospital->longitude,
                    "7"=>$hospital->cover_image,
                    // "8"=>$value->thumb_image,
                    "8"=>$button
                    );
           
        }
        return response()->json(['data'=>$result]);
    }

     //Storing And Updating Data Of Hospital
     public function store(Request $request)
     {
         $request->validate(
             [
                 'name' => 'required',
                 'email' => 'required',
                 'password' => 'required',
                 'contact'=>'required',
                 'address'=>'required',

             ]
         );

         try {
            // Multiple Thumb Image Upload
            $thumbImage = array();
            if($files = $request->file('thumb_image')){
                foreach($request->thumb_image as $file){
                    $thumb_file = $file->getClientOriginalName();
                    $file->move(public_path() . '/assets/images/CoverImage/', $thumb_file);
                    $thumbImage[] = $thumb_file;
                }
            }
            //Cover Image Upload
            if ($request->cover_image) {
                $fileName = time() . '_' . $request->cover_image->getClientOriginalName();
                $files = $request->cover_image->move(public_path() . '/assets/images/CoverImage/', $fileName);
            } else {
                $fileName = "";
            }
            $hospitalData = User::updateOrCreate([
                 'id' => $request->id,
             ],[
                'name' => $request->name,
                'email' => $request->email,
                'password' =>bcrypt($request->password), 
                'phone_number' => $request->contact,
                'address' => $request->address,
                'latitude' =>$request->latitude,
                'longitude' =>$request->longitude,
                'cover_image'=>$fileName,
                'remember_token' => (new Token())->Unique('users', 'remember_token', 60)
             ]);

             //Store Thumb Image
             $hospital_id = $hospitalData->id;
             $thumb_image_data = Thumb::updateOrCreate(
                [
                    'id' => $hospital_id,
                ],[
                    'user_id' =>   $hospital_id,
                    'thumb_image'=>implode("|",$thumbImage),
                ]
             );

             //Send mail with attach file
          
             $user['to'] =$hospitalData->email;
             $password =['password' => $request->password];
              Mail::send('admin.pages.hospital.mail',$password, function ($messages) use ($user) {
                 $messages->to($user['to']);
                 $messages->subject('Hello Doctor');
             });

             $response = [
                 'status' => true,
                 'message' => 'Hospital Data '.($request->id ==0 ? 'Added' : 'Updated').' Successfully',
                 'icon' => 'success',
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

}
