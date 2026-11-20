<?php

namespace App\Http\Controllers\User\Auth;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Lib\Intended;
use App\Models\AdminNotification;
use App\Models\User;
use App\Models\UserLogin;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Auth;
use App\Models\Extension;
use Laravel\Socialite\Facades\Socialite;
use App\Services\Aml\AmlRiskService;
use Illuminate\Validation\Rule;

class RegisterController extends Controller
{
    use RegistersUsers;

    public function __construct()
    {
        parent::__construct();
    }
    
    /** Reconfigure Socialite from the DB extension record if available. */
    private function configureSocialite(): void
    {
        $ext = Extension::where('act', 'google-auth')->where('status', 1)->first();
        if (!$ext) return;

        $clientId     = $ext->shortcode->client_id->value     ?? '';
        $clientSecret = $ext->shortcode->client_secret->value ?? '';

        if ($clientId && $clientSecret) {
            config([
                'services.google.client_id'     => $clientId,
                'services.google.client_secret' => $clientSecret,
                'services.google.redirect'      => route('user.handleGoogleCallback'),
            ]);
        }
    }

    public function redirectToGoogle()
    {
        $this->configureSocialite();
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        $this->configureSocialite();
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();
    
            // Extract split names
            $fullName = $googleUser->getName();
            $nameParts = explode(' ', trim($fullName), 2);
    
            $firstName = $googleUser->user['given_name'] ?? $nameParts[0];
            $lastName  = $googleUser->user['family_name'] ?? ($nameParts[1] ?? '');

            $user = User::firstOrCreate(
                ['email' => $googleUser->getEmail()],
                [
                    'firstname' => $firstName,
                    'lastname'  => $lastName,
                    'password' => bcrypt(uniqid()),
                    'ev' => true,
                    'sv' => true,
                    'tv' => true,
                ]
            );
            // dd($user);
    
            Auth::login($user, true);
    
            return redirect()->route('user.home');
    
        } catch (\Exception $e) {
            return redirect()->back()->withErrors([
                'google_error' => 'Unable to login using Google. ' . $e->getMessage()
            ]);
        }
    }

    public function showRegistrationForm()
    {
        $pageTitle = "Register";
        $policyPages = getContent('policy_pages.element', false, null,true);
        Intended::identifyRoute();
        $info       = json_decode(json_encode(getIpInfo()), true);
        $mobileCode = @implode(',', $info['code']);
        $countries  = json_decode(file_get_contents(resource_path('views/partials/country.json')));
        $view = (request()->type == 'business') ? 'register_business' : 'register';
        return view("Template::user.auth.$view", compact('pageTitle', 'policyPages', 'countries', 'info', 'mobileCode'));
    }


    protected function validator(array $data)
    {
        $passwordValidation = Password::min(6);
        if (gs('secure_password')) {
            $passwordValidation = $passwordValidation->mixedCase()->numbers()->symbols()->uncompromised();
        }
        $agree = 'nullable';
        // if (gs('agree')) {
        //     $agree = 'required';
        // }
        $countryData  = (array)json_decode(file_get_contents(resource_path('views/partials/country.json')));
        $countryCodes = implode(',', array_keys($countryData));
        $mobileCodes  = implode(',', array_column($countryData, 'dial_code'));
        $countries    = implode(',', array_column($countryData, 'country'));
        
        $validate     = Validator::make($data, [
            'firstname'    => 'required',
            'lastname'     => 'required',
            'email'        => 'required|string|email|unique:users',
            'password'     => ['required', 'confirmed', $passwordValidation],
            'captcha'      => 'sometimes|required',
            'agree'        => $agree,
            'company_name' => 'required_if:type,1',
            'owner_name'   => 'required_if:type,1',
            'country_code' => 'required|in:' . $countryCodes,
            'country'      => 'required|in:' . $countries,
            'mobile_code'  => 'required|in:' . $mobileCodes,
            'username'     => 'required|unique:users|min:6',
            'mobile'       => 'required|unique:users',
        ], [
            'firstname.required' => 'The first name field is required',
            'lastname.required'  => 'The last name field is required'
        ]);
        return $validate;
    }

    public function register(Request $request)
    {
        if (!gs('registration')) {
            $notify[] = ['error', 'Registration not allowed'];
            return back()->withNotify($notify);
        }

        // Strip leading '+' from mobile_code so it matches country.json dial_code values (e.g. "+1" → "1")
        $request->merge(['mobile_code' => ltrim($request->input('mobile_code', ''), '+')]);

        $this->validator($request->all())->validate();
        $request->session()->regenerateToken();

        event(new Registered($user = $this->create($request->all())));
        $user->profile_complete = Status::YES;
        $user->save();

        // AML: screen new user against sanctions lists
        try {
            app(AmlRiskService::class)->screenUser($user);
        } catch (\Throwable $e) {
            logger()->error('AML screening error on registration: ' . $e->getMessage());
        }

        $this->guard()->login($user);

        return to_route('user.home');
    }

    protected function create(array $data)
    {
        $referBy = session()->get('reference');
        if ($referBy) {
            $referUser = User::where('username', $referBy)->first();
        } else {
            $referUser = null;
        }

        $user            = new User();
        $user->email     = strtolower($data['email']);
        $user->firstname = $data['firstname'];
        $user->lastname  = $data['lastname'];
        $user->password  = Hash::make($data['password']);
        $user->ref_by    = $referUser ? $referUser->id : 0;
        $user->company_name = $data['company_name'] ?? null;
        $user->owner_name   = $data['owner_name'] ?? null;
        $user->username     = $data['username'];
        $user->mobile       = preg_replace('/\D/', '', $data['mobile']);
        $user->dial_code    = ltrim($data['mobile_code'] ?? '', '+');
        $user->country_code = $data['country_code'];
        $user->country_name = $data['country'];
        $user->city         = $data['city'] ?? null;
        $user->state        = $data['state'] ?? null;
        $user->zip          = $data['zip'] ?? null;
        $user->address      = $data['address'] ?? null;
        $user->dob          = !empty($data['dob']) ? $data['dob'] : null;
        $user->kv = gs('kv') ? Status::NO : Status::YES;
        $user->ev = gs('ev') ? Status::NO : Status::YES;
        $user->sv = gs('sv') ? Status::NO : Status::YES;
        $user->ts = Status::DISABLE;
        $user->tv = Status::ENABLE;
        $user->type = $data['type'];
        $user->save();

        $adminNotification            = new AdminNotification();
        $adminNotification->user_id   = $user->id;
        $adminNotification->title     = 'New member registered';
        $adminNotification->click_url = urlPath('admin.users.detail', $user->id);
        $adminNotification->save();

        //Login Log Create
        $ip        = getRealIP();
        $exist     = UserLogin::where('user_ip', $ip)->first();
        $userLogin = new UserLogin();

        if ($exist) {
            $userLogin->longitude    = $exist->longitude;
            $userLogin->latitude     = $exist->latitude;
            $userLogin->city         = $exist->city;
            $userLogin->country_code = $exist->country_code;
            $userLogin->country      = $exist->country;
        } else {
            $info                    = json_decode(json_encode(getIpInfo()), true);
            $userLogin->longitude    = @implode(',', $info['long']);
            $userLogin->latitude     = @implode(',', $info['lat']);
            $userLogin->city         = @implode(',', $info['city']);
            $userLogin->country_code = @implode(',', $info['code']);
            $userLogin->country      = @implode(',', $info['country']);
        }

        $userAgent          = osBrowser();
        $userLogin->user_id = $user->id;
        $userLogin->user_ip = $ip;
        $userLogin->browser = @$userAgent['browser'];
        $userLogin->os      = @$userAgent['os_platform'];
        $userLogin->save();

        return $user;
    }

    public function checkUser(Request $request)
    {
        $exist['data'] = false;
        $exist['type'] = null;
        if ($request->email) {
            $exist['data'] = User::where('email', $request->email)->exists();
            $exist['type'] = 'email';
            $exist['field'] = 'Email';
        }
        if ($request->mobile) {
            $exist['data'] = User::where('mobile', $request->mobile)->where('dial_code', $request->mobile_code)->exists();
            $exist['type'] = 'mobile';
            $exist['field'] = 'Mobile';
        }
        if ($request->username) {
            $exist['data'] = User::where('username', $request->username)->exists();
            $exist['type'] = 'username';
            $exist['field'] = 'Username';
        }
        return response($exist);
    }

    public function registered()
    {
        return to_route('user.home');
    }
}
