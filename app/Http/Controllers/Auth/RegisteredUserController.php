<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\{Education,Experience,Language,Skill,User,Doctor,ContactUs};
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth,Hash,Session,User_skill};
use Dirape\Token\Token;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $skills = Skill::get(['id','name']);
        return view('Auth.registration',compact('skills'));
    }

    /**
     * Handle an incoming registration request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password'=>'required',
            'contact_number' => ['required', 'min:10', 'max:10'],
            'address' => 'required',
        ]);
        try {
            if ($request->cover_image) {
                $fileName = time() . '_' . $request->cover_image->getClientOriginalName();
                $files = $request->cover_image->move(public_path() . '/assets/images/CoverImage/', $fileName);
            } else {
                $fileName = "";
            }
    
            $user_status= ($request->user_status=='doctor') ? '0' : '1';
            $approval = $request->medical_registration_no ? '2' : '1';

            if($request->user_status=='doctor'){
                $user = User::Create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' =>bcrypt($request->password), 
                    'phone_number' => $request->contact_number,
                    'address' => $request->address,
                    'latitude' =>$request->latitude,
                    'longitude' =>$request->longitude,
                    'cover_image'=>$fileName,
                    'user_status' =>$user_status,
                    'approval'=> $approval,
                    'remember_token' => (new Token())->Unique('users', 'remember_token', 60)
                ]);

                $id =  $user->id;
                $user = User::find($id);
                $skill = $user->skill()->sync($request->skill);
            }else{
                $contact_us = ContactUs::Create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' =>bcrypt($request->password), 
                    'phone_number' => $request->contact_number,
                    'address' => $request->address,
                    'latitude' =>$request->latitude,
                    'longitude' =>$request->longitude,
                ]);
            }

            $doctorMsg ='CONGRATULATIONS ! YOU HAVE BEEN SUCCESSFULLY REGISTERED'.($approval == 1 ?', PLEASE CHECK YOUR EMAIL TO ACTIVATE THE ACCOUNT':'');
            $contact_us ='Thank you for getting in touch!We appreciate you contacting us. One of our colleagues will get back in touch with you soon!Have a great day!';

            $response = [
                'status' => true,
                'message' => $user_status == 0 ? $doctorMsg : $contact_us,
                'icon' => 'success',
            ];
            
        } catch (\Throwable $th) {
            $response = [    
                'status' => false,
                'message' => 'Something Went Wrong! Please Try Again.',
                'icon' => 'error',
            ];
        }
		return response($response);
        
    }
}
   

    
