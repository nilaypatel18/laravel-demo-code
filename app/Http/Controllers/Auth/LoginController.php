<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
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
        $this->middleware('guest')->except('logout');
    }

    //{
    //     "success": true,
    //     "data": {
    //         "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6Ijc3NGQ1OGQwZWViMWY3YTkxN2FkZDc3MzM0YjMyNDJlYmQ5YWQ2NGE4ZDA3NjlmZWFjMDEyOTQ1NjQzYzQ5YjE3ODE2NTUxMmYyMjRiMzY2In0.eyJhdWQiOiIxIiwianRpIjoiNzc0ZDU4ZDBlZWIxZjdhOTE3YWRkNzczMzRiMzI0MmViZDlhZDY0YThkMDc2OWZlYWMwMTI5NDU2NDNjNDliMTc4MTY1NTEyZjIyNGIzNjYiLCJpYXQiOjE1OTEzNDAzMzUsIm5iZiI6MTU5MTM0MDMzNSwiZXhwIjoxNjIyODc2MzM1LCJzdWIiOiIxIiwic2NvcGVzIjpbXX0.Al0YyblcpOpLb_X6IHBXLzVlTDx6WWYCWsByju7v2aRi3X8bj5vvfWDO4e3Ztn27OeSvgdjCD5TdiAcKa9Ju-Kg_JDWm0aXzmVn2NFGf9c1v4ag-dEJD8sp2ip_BUr0Vp80sEe40U7qqc8Wh-MMxIlVrEzM6LJxPCo47k-avYqgdg9dv8HJ8rGvjADixU7fJYXCOIUH0Op1UcnwPCYoZDbhAudUPJsqAb5HzUrKOOeT8uKZ2NLm_YJHVN_BC8OwCMZ1F7uHadCdnFhMez_yuxLOYY88m13wqLClvGXbaLAU7o4cHzLM5xo1HjJDmsXLaFiBBzK-F_isFMKFS4H0ud8sh5wbbPSYuXI-ATpaw1-1E6MFxOCo1hhtSqjnEdq3w_9hl_c5iOL-1x7EErNvsz_LkEw79CqmI5ajh1c8TZAJE0oCrdhHv2uldJFiauJjLTvCe0bg4cTdQ-d1BLOxP5te67KlYtZj7zaPY0TqV1iSJ1S5BEe8nA0Mw85FVk2N6ceVW5p2GyMnSaHViX5E4gzMN0W9TKwQfdJaDgkSwKNF1c9jUNYCHXSdivvoLjP8pxB_qMM-bla_xdvhUqTp0wiMkAqvf6aVWwOB17Pgbdxv84YRB-D4svRuT9xk4h6Yf98-f0fdlvmAXNd7fW0o12hIvS7DZ5xzK6qfoNrd0cAA",
    //         "name": "john"
    //     },
    //     "message": "User login successfully."
    // }
}
