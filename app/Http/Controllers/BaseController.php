<?php

namespace App\Http\Controllers;

use App\Models\Applicant;
use App\Models\Experience;
use App\Models\Job;
use App\Models\Message;
use App\Models\JobCategory;
use App\Models\JobType;
use App\Models\User;
use Illuminate\Http\Request;

class BaseController extends Controller
{
    //bullhorn
    public function redirectToAuthorization()
    {
        $client_id = '6a851d51-f0c3-4e59-9518-d3e75f14ba64';
        $redirect_uri = 'https://trianglegroup.nl/bullhorn_callback';
        $response_type = 'code'; // For authorization code flow
        $username = 'triangle.api';
        $password = 'D@nny1964!';
        $state = '334';
        $action = 'Login'; // Action parameter

        $authorization_url = 'https://auth-ger.bullhornstaffing.com/oauth/authorize?' . http_build_query([
            'client_id' => $client_id,
            'redirect_uri' => $redirect_uri,
            'response_type' => $response_type,
            'username' => $username,
            'password' => $password,
            'state' => $state,
            'action' => $action, // Include action parameter
        ]);

        return redirect()->away($authorization_url);
    }
   //home
   public function home(){
    return $this->redirectToAuthorization();
   }
    public function message(Request $request)
    {
        //dd($request->all());
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'nullable|numeric',
            'comment' => 'required|string',
        ]);
        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'comment' => $request->comment,
        ];

        $message = Message::create($data);

        // Check if job_details is created successfully
        if ($message) {
            return redirect()->back()->with('success', 'Message sent successfully!');
        } else {
            // Handle the case where job_details creation fails
            return redirect()->back()->with('error', 'Failed to sent this message. Please try again.');
        }
    }
    public function collaborate()
    {
        return view('Front.collaborate');
    }
	public function postAndPartnerShip()
    {
        return view('Front.post_and_partnership');
    }
    public function client_dashboard()
    {
        return view('Front.client_dashboard');
    }
    public function company_profile()
    {
        $job_detail = Job::findOrFail(11);
        return view('Front.company_profile', compact('job_detail'));
    }
    public function apply(Request $request)
    {
        //dd($request->all());
        $id = auth()->user()->id;
        $request->validate([
            'resume' => 'required',
        ]);

        $applicant = User::findOrFail($id);
        // dd( $applicant);
        if ($request->hasFile('resume')) {
            $resumeName = time() . '.' . $request->resume->extension();
            $resumePath = $request->resume->storeAs('public/Application', $resumeName);
            $data['resume'] = $resumePath;
        }
        $data = [
            'user_id' => $id,
            'job_id' => $request->job_id,
            'resume' => $resumePath,
        ];

        //dd($data);
        $job_applied = Applicant::create($data);
        if ($job_applied) {
            $applicant->update([
                'phone' => $request->phone,
                'city' => $request->city,
                'is_applicant' => 1,
            ]);
            return redirect()->back()->with('success', 'Job Applied successfully!');
        } else {
            // Handle the case where job_details creation fails
            return redirect()->back()->with('error', 'Failed to Applied the job. Please try again.');
        }
    }
    //job detail page
    public function job_details(Request $request)
    {
        $id = $request->id;
        $job_detail = Job::findOrFail($id);
        //dd($job_detail);
        return view('Front.job-detail', compact('job_detail'));
    }
    public function client()
    {
        $job_categories = JobCategory::all();
        $job_types = JobType::all();
        $job_experience = Experience::all();
        //dd($job_categories);
        return view('Front.client', compact('job_categories', 'job_types', 'job_experience'));
    }
    public function profile()
    {
        return view('Front.profile');
    }
    public function job_seeker()
    {
        // $jobs = Job::orderBy('id', 'DESC')->paginate(5);
        // dd($jobs);
		// return $this->redirectToAuthorization();
        return view('Front.job-seeker');
    }

   public function bullhorn_callback(Request $request)
    {
      //return redirect()->away('https://www.google.com/');
    }

    public function job_post(Request $request)
    {
        //dd($request->all());
        $request->validate([
            'image' => 'nullable',
            'job_title' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'number_of_seat' => 'required',
            'min_pay' => 'nullable',
            'max_pay' => 'nullable',
            'last_date' => 'required|date',
            'job_description' => 'required|string',
            'company_name' => 'required|string|max:255',
            'company_email' => 'required|email',
            'company_phone' => 'required',
            'company_web' => 'nullable|url',
            'company_description' => 'required|string',
            'company_logo' => 'nullable',
        ]);

        //dd('hello');
        $data = [
            'user_id' => 1,
            'title' => $request->job_title,
            'short_description' => $request->short_description,
            'job_category_id' => $request->job_category,
            'job_type_id' => $request->job_type,
            'experience_id' => $request->job_experience,
            'location' => $request->location,
            'city' => $request->city,
            'country' => $request->country,
            'number_of_seat' => $request->number_of_seat,
            'min_pay' => $request->min_pay,
            'max_pay' => $request->max_pay,
            'last_date' => $request->last_date,
            'job_description' => $request->job_description,
            'company_name' => $request->company_name,
            'company_email' => $request->company_email,
            'company_phone' => $request->company_phone,
            'company_web' => $request->company_web,
            'company_description' => $request->company_description,
            // Add other fields as needed
        ];
        // Handle Image Upload
        if ($request->hasFile('image')) {
            $imageName = time() . '.' . $request->image->extension();
            $imagePath = $request->image->storeAs('FeatureImage', $imageName);
            $data['image'] = $imagePath;
        }

        if ($request->hasFile('company_logo')) {
            $imageName = time() . '.' . $request->company_logo->extension();
            $logoPath = $request->company_logo->storeAs('Logo', $imageName);
            $data['company_logo'] = $logoPath;
        }

        $job_details = Job::create($data);

        // Check if job_details is created successfully
        if ($job_details) {
            return redirect()->back()->with('success', 'Job posted successfully!');
        } else {
            // Handle the case where job_details creation fails
            return redirect()->back()->with('error', 'Failed to post the job. Please try again.');
        }
    }
}

