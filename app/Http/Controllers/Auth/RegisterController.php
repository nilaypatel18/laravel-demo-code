<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        //response
        //  {
        //     "success": true,
        //     "data": {
        //         "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6ImQ1YjViYzY1OTdkOGQyMDNjYmI1ZjhkMmIxNTUzYzIyNzhmNzc4YzgwNGRmMDMxYzg2MjgyODk1NTUwZGQwYTkyMjA3YmM3NjczMWI0ODZhIn0.eyJhdWQiOiIxIiwianRpIjoiZDViNWJjNjU5N2Q4ZDIwM2NiYjVmOGQyYjE1NTNjMjI3OGY3NzhjODA0ZGYwMzFjODYyODI4OTU1NTBkZDBhOTIyMDdiYzc2NzMxYjQ4NmEiLCJpYXQiOjE1OTEzNDAwNDYsIm5iZiI6MTU5MTM0MDA0NiwiZXhwIjoxNjIyODc2MDQ1LCJzdWIiOiIyIiwic2NvcGVzIjpbXX0.ZUmw_HTddsXADWfp3NANmpAeK3wSQKMbrzLBGCDo462wmlx0ZqSVmTr0L7fKLDX5Q_xUYtqJ25xnk-2_SswwZZAGI4BPcXSVE6AkHwdWI6ilDWZh5ZvgKcVjz6GPCF9LodJVhGw-TWB1fy-mYHca5Z2EcQ-OrMHSTpV8ieer2N0romcnkk55_XDBleUrKljDpvNzAdAoeNCQmLWKsOOLdB9yc0gGseINnOYHPM3MEms81ZxTzRw_Uq_yigaXyp9kQ9zEmSYgPpPJ1j569ThIBNBEEjcIEZzFI1Bo5_OYWZzP5Gy96BZzlEpk1i7BFQjb1h-7vN3YZhQ7w8dZdBIPC7q8mIvAYjQrel7npaExJy1ZD7Uc1P4hdE445lk7p9xy2Ijvjq0kqEzh3utX9F9ogHHIKPDMnSywKT8PqgxNs3fhEuohYTAFA0tWDsHdIc-kbz0F6itpL_lF1nEfHEOr44-g_hXAL8ZQk9zhjw_ZkGkmBckvVmEPDlYM1i_Clg_f8XPKnlSLngyko4lgHyrMT5SvnfrsYycbIA5riqhTQi_J04ybpb8mJCCHDxS316eZrITbKm4fVHcW2Wpuq1pWJChQ2Vxuq4J-1fYDkQMt5NwqU5KgNgdrK9_qACDXNZcFwkI-xNXsrtFLpyPnl6OACuAT_0-2_eGdKiEbUekovIk",
        //         "name": "john"
        //     },
        //     "message": "User register successfully."
        // }
    }
}
