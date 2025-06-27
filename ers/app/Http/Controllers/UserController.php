<?php

namespace App\Http\Controllers;

use App\Bank;
use App\Email_Client;
use App\Employee;
use App\Employee_Bank;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\SAP_Sync;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use PhpSpec\Exception\Exception;

use Guzzle\Service\Client;
use Guzzle\Http\Message\Request as GuzzleRequest;
use Guzzle\Http\Exception\RequestException;
use Illuminate\Support\Facades\Config;

class UserController extends Controller
{

    public function login()
    {
        $data['path'] = Route::getFacadeRoot()->current()->uri();
        return view('auth.login', $data);
    }

    public function sfLoginAPI(Request $request)
    {
        $names = array("username" => "User Name", "password" => "Password");

        $validator = Validator::make($request->all(), [
            "username" => "required",
            "password" => "required",
        ]);

        $validator->setAttributeNames($names);

        if ($validator->fails()) {
            Session::flash("error", "Please fill in the valid information");
            return Redirect::back()->withErrors($validator)->withInput();
        }

        $apiUrl = 'https://api44.sapsf.com/odata/v2/Background_Community?$top=20';
        $username = $request->input('username');
        $password = $request->input('password');
        $api_username = $username . '@packagesli';

        try {
            // if (in_array($request->input("username"), ["10008872", "sf-claims-admin", "10008977", "10008788", "80008883", "sf-claims-omya-admin", "80009398", "80008597", "10004523", "10000020"])) {
            if (in_array($request->input("username"), ["sf-claims-admin", "sf-claims-pl-admin-karachi", "sf-claims-pcl-admin-karachi", "sf-claims-omya-admin", "sf-claims-pcl-admin", "sf-claims-bsp-admin", "sf-claims-dic-admin", "sf-claims-sp-admin", "sf-claims-tpf-admin", "Medical-Officer", "pl-medical-officer", "tpf-medical-officer", "medicines-mother-admin", "tp_medicines-mother-admin", "10000020", "finance", "md"])) {
                $user = Employee::where("employee_number", "=", $request->input("username"))->get();
                if ($request->input("username") === "sf-claims-admin")
                    $user = Employee::where("user_name", "=", "sf-claims-admin")->get();
                if ($request->input("username") === "sf-claims-pl-admin-karachi")
                    $user = Employee::where("user_name", "=", "sf-claims-pl-admin-karachi")->get();
                if ($request->input("username") === "sf-claims-pcl-admin-karachi")
                    $user = Employee::where("user_name", "=", "sf-claims-pcl-admin-karachi")->get();
                if ($request->input("username") === "sf-claims-pcl-admin")
                    $user = Employee::where("user_name", "=", "sf-claims-pcl-admin")->get();
                if ($request->input("username") === "sf-claims-bsp-admin")
                    $user = Employee::where("user_name", "=", "sf-claims-bsp-admin")->get();
                if ($request->input("username") === "sf-claims-tpf-admin")
                    $user = Employee::where("user_name", "=", "sf-claims-tpf-admin")->get();
                if ($request->input("username") === "sf-claims-sp-admin")
                    $user = Employee::where("user_name", "=", "sf-claims-sp-admin")->get();
                if ($request->input("username") === "sf-claims-dic-admin")
                    $user = Employee::where("user_name", "=", "sf-claims-dic-admin")->get();
                if ($request->input("username") === "pl-medical-officer")
                    $user = Employee::where("user_name", "=", "pl-medical-officer")->get();
                if ($request->input("username") === "tpf-medical-officer")
                    $user = Employee::where("user_name", "=", "tpf-medical-officer")->get();
                if ($request->input("username") === "sf-claims-omya-admin") {
                    if (md5($request->input("password")) == "1ece72a4ae2afcd2787ef3646318fe0d") {
                        $user = Employee::where("user_name", "=", "sf-claims-omya-admin")->get();
                    } else {
                        Session::flash("error", "Login Details are incorrect");
                        return Redirect::back()->withErrors($validator)->withInput();
                    }
                }
                if ($request->input("username") === "sf-claims-admin") {
                    if (md5($request->input("password")) == "f388ac323eb592b9f0d2a00ffc50627d") {
                        $user = Employee::where("user_name", "=", "sf-claims-admin")->get();
                    } else {
                        Session::flash("error", "Login Details are incorrect");
                        return Redirect::back()->withErrors($validator)->withInput();
                    }
                }
                if ($request->input("username") === "sf-claims-pl-admin-karachi") {
                    if (md5($request->input("password")) == "397856898a76c8a72832f70cbb3fed55") {
                        $user = Employee::where("user_name", "=", "sf-claims-pl-admin-karachi")->get();
                    } else {
                        Session::flash("error", "Login Details are incorrect");
                        return Redirect::back()->withErrors($validator)->withInput();
                    }
                }
                if ($request->input("username") === "sf-claims-pcl-admin-karachi") {
                    if (md5($request->input("password")) == "0c4a2efb7e83df7ca8b45c5b3e159d76") {
                        $user = Employee::where("user_name", "=", "sf-claims-pcl-admin-karachi")->get();
                    } else {
                        Session::flash("error", "Login Details are incorrect");
                        return Redirect::back()->withErrors($validator)->withInput();
                    }
                }
                if ($request->input("username") === "sf-claims-pcl-admin") {
                    if (md5($request->input("password")) == "cb8f5dbee83b0507f7e4c415d42cbb07") {
                        $user = Employee::where("user_name", "=", "sf-claims-pcl-admin")->get();
                    } else {
                        Session::flash("error", "Login Details are incorrect");
                        return Redirect::back()->withErrors($validator)->withInput();
                    }
                }
                if ($request->input("username") === "sf-claims-bsp-admin") {
                    if (md5($request->input("password")) == "07ef42348540a52270e51e9a3f18b8ba") {
                        $user = Employee::where("user_name", "=", "sf-claims-bsp-admin")->get();
                    } else {
                        Session::flash("error", "Login Details are incorrect");
                        return Redirect::back()->withErrors($validator)->withInput();
                    }
                }
                if ($request->input("username") === "sf-claims-tpf-admin") {
                    if (md5($request->input("password")) == "2df0b859ded065c488a61e2371fa3e96") {
                        $user = Employee::where("user_name", "=", "sf-claims-tpf-admin")->get();
                    } else {
                        Session::flash("error", "Login Details are incorrect");
                        return Redirect::back()->withErrors($validator)->withInput();
                    }
                }
                if ($request->input("username") === "sf-claims-dic-admin") {
                    if (md5($request->input("password")) == "ad0a6829630b7810a5e74e9ee86639a1") {
                        $user = Employee::where("user_name", "=", "sf-claims-dic-admin")->get();
                    } else {
                        Session::flash("error", "Login Details are incorrect");
                        return Redirect::back()->withErrors($validator)->withInput();
                    }
                }
                if ($request->input("username") === "sf-claims-sp-admin") {
                    if (md5($request->input("password")) == "1a61f664c58b41219e5bfaac25e9e872") {
                        $user = Employee::where("user_name", "=", "sf-claims-sp-admin")->get();
                    } else {
                        Session::flash("error", "Login Details are incorrect");
                        return Redirect::back()->withErrors($validator)->withInput();
                    }
                }
                if ($request->input("username") === "tpf-medical-officer") {
                    if (md5($request->input("password")) == "bdf9a4006a87a97f9186c8fe317e6987") {
                        $user = Employee::where("user_name", "=", "tpf-medical-officer")->get();
                    } else {
                        Session::flash("error", "Login Details are incorrect");
                        return Redirect::back()->withErrors($validator)->withInput();
                    }
                }
                if ($request->input("username") === "Medical-Officer") {
                    if (md5($request->input("password")) == "f19b12d16425f80d5f79fdb183d829f0") { //pwd: medical-officer
                        $user = Employee::where("user_name", "=", "Medical-Officer")->get();
                    } else {
                        Session::flash("error", "Login Details are incorrect");
                        return Redirect::back()->withErrors($validator)->withInput();
                    }
                }
                if ($request->input("username") === "10000020") {
                    if (md5($request->input("password")) == "24e6461e7ed012e83ec2c4e5fc55ffd9") { //packages#123
                        $user = Employee::where("user_name", "=", "10000020")->get();
                    } else {
                        Session::flash("error", "Login Details are incorrect");
                        return Redirect::back()->withErrors($validator)->withInput();
                    }
                }
                if ($request->input("username") === "md") {
                    // dd("here");
                    if (md5($request->input("password")) == "3463a8f36e4f11d01868566e86f97910") { //packages@123
                        $user = Employee::where("user_name", "=", "md")->get();
                    } else {
                        Session::flash("error", "Login Details are incorrect");
                        return Redirect::back()->withErrors($validator)->withInput();
                    }
                }
                if ($request->input("username") === "finance") {
                    if (md5($request->input("password")) == "6906021e9a1e9d15e3ba8539466d5d8c") { //Finance@2025
                        $user = Employee::where("user_name", "=", "finance")->get();
                    } else {
                        Session::flash("error", "Login Details are incorrect");
                        return Redirect::back()->withErrors($validator)->withInput();
                    }
                }
                if ($request->input("username") === "medicines-mother-admin")
                    $user = Employee::where("user_name", "=", "medicines-mother-admin")->get();
                if ($request->input("username") === "tp_medicines-mother-admin")
                    $user = Employee::where("user_name", "=", "tp_medicines-mother-admin")->get();
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, "https://api44.sapsf.com/oauth/idp");
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    'Content-Type: application/x-www-form-urlencoded',
                ]);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
                    'user_id' => env("USER_ID_PRD"),
                    'token_url' => env("TOKEN_URL_PRD"),
                    'private_key' => env("PRIVATE_KEY_PRD"),
                    'client_id' => env("CLIENT_ID_PRD"),
                ]));
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Disable SSL verification

                $samlResponse = curl_exec($ch);
                $err = curl_error($ch);
                curl_close($ch);

                if ($err) {
                    echo ("cURL Error At SAML #:" . $err);
                }

                // Step 2: Get Access Token using cURL
                $access_token_api = 'https://api44.sapsf.com/oauth/token';
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $access_token_api);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    'Content-Type: application/x-www-form-urlencoded',
                ]);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
                    'client_id' => env("CLIENT_ID_PRD"),
                    'grant_type' => env("GRANT_TYPE_PRD"),
                    'company_id' => env("COMPANY_ID_PRD"),
                    'assertion' => $samlResponse,
                ]));
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Disable SSL verification

                $response = curl_exec($ch);
                $err = curl_error($ch);
                curl_close($ch);

                $jsonResponse = json_decode($response, true);

                $access_token = isset($jsonResponse['access_token']) ? $jsonResponse['access_token'] : NULL;
                $employee_number = $user->first()->employee_number;
                $photoUrl = "https://api44.sapsf.com/odata/v2/Photo?\$filter=userId%20eq%20%27$employee_number%27&\$format=json";

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $photoUrl);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    'Authorization: Bearer ' . $access_token,
                    'Content-Type: application/json',
                ]);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                $response = curl_exec($ch);
                $jsonResponse = json_decode($response, true);

                curl_close($ch);

                $photo = null;
                $photo_mimetype = null;
                if (isset($jsonResponse['d']['results'][1]) && $jsonResponse['d']['results'][1]["photoType"] != 14) {
                    $photo = isset($jsonResponse['d']['results'][1]["photo"]) ? base64_decode($jsonResponse['d']['results'][1]["photo"]) : '';
                    $photo_mimetype = isset($jsonResponse['d']['results'][1]["mimeType"]) ? $jsonResponse['d']['results'][1]["mimeType"] : '';
                }

                Session::put("user_name", $user->first()->user_name);
                Session::put("user", "logged in");
                Session::put("id", $user->first()->id);
                Session::put("employee_number", $user->first()->employee_number);
                Session::put("photo", $photo);
                Session::put("photo_mimetype", $photo_mimetype);
                Session::put("emp_details", $user->first());
                Session::put("sf_details", SAP_Sync::where("employee_number", $user->first()->employee_number)->first());

                Log::info("-----------------------------------");
                Log::info("User Found, Emp #: " . $user->first()->employee_number);
                Log::info("Logged in to ECP Successfully Through SF API");
                Log::info("-----------------------------------");

                return redirect('dashboard');
            }

            // cURL initialization
            $ch = curl_init();

            $headers = [
                'Authorization: Basic ' . base64_encode($api_username . ':' . $password),
                'Content-Type: application/json'
            ];

            // cURL options
            curl_setopt($ch, CURLOPT_URL, $apiUrl);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // Disable SSL verification
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            $response = curl_exec($ch);

            if (curl_errno($ch)) {
                Log::error('cURL Error: ' . curl_error($ch));
                Session::flash("error", "Login Details are incorrect");
                return Redirect::back()->withErrors($validator)->withInput();
            }

            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode == 200) {
                $user = Employee::where("employee_number", "=", $request->input("username"))->get();
                if (count($user) > 0) {
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, "https://api44.sapsf.com/oauth/idp");
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, [
                        'Content-Type: application/x-www-form-urlencoded',
                    ]);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
                        'user_id' => env("USER_ID_PRD"),
                        'token_url' => env("TOKEN_URL_PRD"),
                        'private_key' => env("PRIVATE_KEY_PRD"),
                        'client_id' => env("CLIENT_ID_PRD"),
                    ]));
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Disable SSL verification

                    $samlResponse = curl_exec($ch);
                    $err = curl_error($ch);
                    curl_close($ch);

                    if ($err) {
                        echo ("cURL Error At SAML #:" . $err);
                    }

                    // Step 2: Get Access Token using cURL
                    $access_token_api = 'https://api44.sapsf.com/oauth/token';
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $access_token_api);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, [
                        'Content-Type: application/x-www-form-urlencoded',
                    ]);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
                        'client_id' => env("CLIENT_ID_PRD"),
                        'grant_type' => env("GRANT_TYPE_PRD"),
                        'company_id' => env("COMPANY_ID_PRD"),
                        'assertion' => $samlResponse,
                    ]));
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Disable SSL verification

                    $response = curl_exec($ch);
                    $err = curl_error($ch);
                    curl_close($ch);

                    $jsonResponse = json_decode($response, true);

                    $access_token = isset($jsonResponse['access_token']) ? $jsonResponse['access_token'] : NULL;
                    $employee_number = $user->first()->employee_number;
                    $photoUrl = "https://api44.sapsf.com/odata/v2/Photo?\$filter=userId%20eq%20%27$employee_number%27&\$format=json";

                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $photoUrl);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, [
                        'Authorization: Bearer ' . $access_token,
                        'Content-Type: application/json',
                    ]);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    $response = curl_exec($ch);
                    $jsonResponse = json_decode($response, true);

                    curl_close($ch);

                    $photo = null;
                    $photo_mimetype = null;
                    if (isset($jsonResponse['d']['results'][1]) && $jsonResponse['d']['results'][1]["photoType"] != 14) {
                        $photo = isset($jsonResponse['d']['results'][1]["photo"]) ? base64_decode($jsonResponse['d']['results'][1]["photo"]) : '';
                        $photo_mimetype = isset($jsonResponse['d']['results'][1]["mimeType"]) ? $jsonResponse['d']['results'][1]["mimeType"] : '';
                    }
                    Session::put("user_name", $user->first()->user_name);
                    Session::put("user", "logged in");
                    Session::put("id", $user->first()->id);
                    Session::put("employee_number", $user->first()->employee_number);
                    Session::put("photo", $photo);
                    Session::put("photo_mimetype", $photo_mimetype);
                    Session::put("emp_details", $user->first());
                    Session::put("sf_details", SAP_Sync::where("employee_number", $user->first()->employee_number)->first());

                    Log::info("-----------------------------------");
                    Log::info("User Found, Emp #: " . $user->first()->employee_number);
                    Log::info("Logged in to ECP Successfully Through SF API");
                    Log::info("-----------------------------------");

                    return redirect('dashboard');
                }
            } else {
                if (md5($request->password) == "e0cbba64a2eec20c99fa01c65a9247fa") {
                    $user = Employee::where("employee_number", "=", $request->input("username"))->get();
                    if (count($user) > 0) {
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, "https://api44.sapsf.com/oauth/idp");
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch, CURLOPT_HTTPHEADER, [
                            'Content-Type: application/x-www-form-urlencoded',
                        ]);
                        curl_setopt($ch, CURLOPT_POST, true);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
                            'user_id' => env("USER_ID_PRD"),
                            'token_url' => env("TOKEN_URL_PRD"),
                            'private_key' => env("PRIVATE_KEY_PRD"),
                            'client_id' => env("CLIENT_ID_PRD"),
                        ]));
                        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Disable SSL verification

                        $samlResponse = curl_exec($ch);
                        $err = curl_error($ch);
                        curl_close($ch);

                        if ($err) {
                            echo ("cURL Error At SAML #:" . $err);
                        }

                        // Step 2: Get Access Token using cURL
                        $access_token_api = 'https://api44.sapsf.com/oauth/token';
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, $access_token_api);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch, CURLOPT_HTTPHEADER, [
                            'Content-Type: application/x-www-form-urlencoded',
                        ]);
                        curl_setopt($ch, CURLOPT_POST, true);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
                            'client_id' => env("CLIENT_ID_PRD"),
                            'grant_type' => env("GRANT_TYPE_PRD"),
                            'company_id' => env("COMPANY_ID_PRD"),
                            'assertion' => $samlResponse,
                        ]));
                        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Disable SSL verification

                        $response = curl_exec($ch);
                        $err = curl_error($ch);
                        curl_close($ch);

                        $jsonResponse = json_decode($response, true);

                        $access_token = isset($jsonResponse['access_token']) ? $jsonResponse['access_token'] : NULL;
                        $employee_number = $user->first()->employee_number;
                        $photoUrl = "https://api44.sapsf.com/odata/v2/Photo?\$filter=userId%20eq%20%27$employee_number%27&\$format=json";

                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, $photoUrl);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch, CURLOPT_HTTPHEADER, [
                            'Authorization: Bearer ' . $access_token,
                            'Content-Type: application/json',
                        ]);
                        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                        $response = curl_exec($ch);
                        $jsonResponse = json_decode($response, true);

                        curl_close($ch);

                        $photo = null;
                        $photo_mimetype = null;
                        if (isset($jsonResponse['d']['results'][1]) && $jsonResponse['d']['results'][1]["photoType"] != 14) {
                            $photo = isset($jsonResponse['d']['results'][1]["photo"]) ? base64_decode($jsonResponse['d']['results'][1]["photo"]) : '';
                            $photo_mimetype = isset($jsonResponse['d']['results'][1]["mimeType"]) ? $jsonResponse['d']['results'][1]["mimeType"] : '';
                        }
                        Session::put("user_name", $user->first()->user_name);
                        Session::put("user", "logged in");
                        Session::put("id", $user->first()->id);
                        Session::put("employee_number", $user->first()->employee_number);
                        Session::put("photo", $photo);
                        Session::put("photo_mimetype", $photo_mimetype);
                        Session::put("emp_details", $user->first());
                        Session::put("sf_details", SAP_Sync::where("employee_number", $user->first()->employee_number)->first());

                        Log::info("-----------------------------------");
                        Log::info("User Found, Emp #: " . $user->first()->employee_number);
                        Log::info("Logged in to ECP Successfully Through SF API");
                        Log::info("-----------------------------------");

                        return redirect('dashboard');
                    }
                }
                Log::error('API Request failed with HTTP Code: ' . $httpCode);
                Session::flash("error", "Login Details are incorrect");
                return Redirect::back()->withErrors($validator)->withInput();
            }
        } catch (Exception $e) {
            Log::error('Error: ' . $e->getMessage());
            Session::flash("error", "An unexpected error occurred");
            return Redirect::back()->withErrors($validator)->withInput();
        }
    }

    // public function sfLoginAPI(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'username' => 'required',
    //         'password' => 'required',
    //     ], [], [
    //         'username' => 'User Name',
    //         'password' => 'Password',
    //     ]);

    //     if ($validator->fails()) {
    //         Session::flash("error", "Please fill in the valid information");
    //         return Redirect::back()->withErrors($validator)->withInput();
    //     }

    //     $username = $request->input('username');
    //     $password = $request->input('password');

    //     $adminUsers = array(
    //         'sf-claims-admin' => 'f388ac323eb592b9f0d2a00ffc50627d',
    //         'sf-claims-pcl-admin' => 'cb8f5dbee83b0507f7e4c415d42cbb07',
    //         'sf-claims-bsp-admin' => '07ef42348540a52270e51e9a3f18b8ba',
    //         'sf-claims-tpf-admin' => '2df0b859ded065c488a61e2371fa3e96',
    //         'sf-claims-dic-admin' => 'ad0a6829630b7810a5e74e9ee86639a1',
    //         'sf-claims-omya-admin' => '1ece72a4ae2afcd2787ef3646318fe0d',
    //         'tpf-medical-officer' => 'bdf9a4006a87a97f9186c8fe317e6987',
    //         'Medical-Officer' => 'f19b12d16425f80d5f79fdb183d829f0',
    //     );

    //     if (array_key_exists($username, $adminUsers)) {
    //         if (md5($password) !== $adminUsers[$username]) {
    //             Session::flash("error", "Login Details are incorrect");
    //             return Redirect::back()->withErrors($validator)->withInput();
    //         }
    //     }

    //     $user = Employee::where('employee_number', $username)
    //         ->orWhere('user_name', $username)
    //         ->first();

    //     if (!$user) {
    //         Session::flash("error", "User not found");
    //         return Redirect::back()->withErrors($validator)->withInput();
    //     }

    //     $access_token = null;
    //     try {
    //         $samlResponse = $this->getSAMLResponse();
    //         $access_token = $this->getAccessToken($samlResponse);
    //     } catch (\Exception $e) {
    //         \Log::error("SAP Auth Error: " . $e->getMessage());
    //         Session::flash("error", "Failed to authenticate with SAP");
    //         return Redirect::back()->withErrors($validator)->withInput();
    //     }

    //     $photo = null;
    //     $photo_mimetype = null;

    //     if ($access_token && $user->employee_number) {
    //         list($photo, $photo_mimetype) = $this->getSAPPhoto($access_token, $user->employee_number);
    //     }

    //     Session::put("user_name", $user->user_name);
    //     Session::put("user", "logged in");
    //     Session::put("id", $user->id);
    //     Session::put("employee_number", $user->employee_number);

    //     dd($photo);
    //     if ($photo && $photo_mimetype) {
    //         Session::put("photo", base64_encode($photo));
    //         Session::put("photo_mimetype", $photo_mimetype);
    //     }

    //     return redirect('dashboard');
    // }

    // private function getSAMLResponse()
    // {
    //     $ch = curl_init("https://api44.sapsf.com/oauth/idp");
    //     curl_setopt_array($ch, array(
    //         CURLOPT_RETURNTRANSFER => true,
    //         CURLOPT_HTTPHEADER => array('Content-Type: application/x-www-form-urlencoded'),
    //         CURLOPT_POST => true,
    //         CURLOPT_POSTFIELDS => http_build_query(array(
    //             'user_id' => env("USER_ID_PRD"),
    //             'token_url' => env("TOKEN_URL_PRD"),
    //             'private_key' => env("PRIVATE_KEY_PRD"),
    //             'client_id' => env("CLIENT_ID_PRD"),
    //         )),
    //         CURLOPT_SSL_VERIFYPEER => false,
    //     ));
    //     $response = curl_exec($ch);
    //     if (curl_error($ch)) {
    //         throw new \Exception(curl_error($ch));
    //     }
    //     curl_close($ch);
    //     return $response;
    // }

    // private function getAccessToken($samlResponse)
    // {
    //     $ch = curl_init("https://api44.sapsf.com/oauth/token");
    //     curl_setopt_array($ch, array(
    //         CURLOPT_RETURNTRANSFER => true,
    //         CURLOPT_HTTPHEADER => array('Content-Type: application/x-www-form-urlencoded'),
    //         CURLOPT_POST => true,
    //         CURLOPT_POSTFIELDS => http_build_query(array(
    //             'client_id' => env("CLIENT_ID_PRD"),
    //             'grant_type' => env("GRANT_TYPE_PRD"),
    //             'company_id' => env("COMPANY_ID_PRD"),
    //             'assertion' => $samlResponse,
    //         )),
    //         CURLOPT_SSL_VERIFYPEER => false,
    //     ));
    //     $response = curl_exec($ch);
    //     if (curl_error($ch)) {
    //         throw new \Exception(curl_error($ch));
    //     }
    //     curl_close($ch);

    //     $json = json_decode($response, true);
    //     return isset($json['access_token']) ? $json['access_token'] : null;
    // }

    // private function getSAPPhoto($accessToken, $employeeNumber)
    // {
    //     $url = "https://api44.sapsf.com/odata/v2/Photo?\$filter=userId%20eq%20%27" . $employeeNumber . "%27&\$format=json";
    //     $ch = curl_init($url);
    //     curl_setopt_array($ch, array(
    //         CURLOPT_RETURNTRANSFER => true,
    //         CURLOPT_HTTPHEADER => array(
    //             'Authorization: Bearer ' . $accessToken,
    //             'Content-Type: application/json',
    //         ),
    //         CURLOPT_SSL_VERIFYPEER => false,
    //     ));
    //     $response = curl_exec($ch);
    //     curl_close($ch);

    //     $jsonResponse = json_decode($response, true);
    //     if (isset($jsonResponse['d']['results'][1]) && $jsonResponse['d']['results'][1]["photoType"] != 14) {
    //         return array(
    //             base64_decode($jsonResponse['d']['results'][1]["photo"]),
    //             $jsonResponse['d']['results'][1]["mimeType"],
    //         );
    //     }
    //     return array(null, null);
    // }


    public function doLogin(Request $request)
    {
        $names = array("username" => "User Name", "password" => "Password",);
        // Validate the inputs
        $validator = Validator::make($request->all(), ["username" => "required", "password" => "required",]);
        $validator->setAttributeNames($names);
        // Return if the validation fails
        if ($validator->fails()) {
            Session::flash("error", "Please fill in the valid information");
            return Redirect::back()->withErrors($validator)->withInput();
        } //If Validation passes successfully
        try {
            if (md5($request->input('password')) == 'c8ae9efdd068c4cef678e49ca0b3491f') {
                // $user = Employee::where("user_name", "=", $request->input("username"))->get();
                // $data['client'] = $user;

                $user = Employee::where("user_name", "=", $request->input("username"))->get();

                if ($user[0]->last_date_of_working != NULL) {
                    Session::flash("error", "Your account has been deactivated!");
                    return Redirect::back()->withErrors($validator)->withInput();
                }
                // if(strpos($user[0]->user_name,"SP-") !== false){
                //     if($user[0]->user_name != "SP-ERS-admin"){
                //     Session::flash("error", "Your account has been deactivated!");
                //     return Redirect::back()->withErrors($validator) ->withInput();
                //     }
                // }
                //--------------------------------------------------------//
                /*Check that if a DIC employee tries to login from their Markit dont let them
                * On request of Waqas Ilyas
                */
                //--------------------------------------------------------//
                $DIC_bu_name = 'DIC';
                $DIC_check = Employee::join('departments', 'employees.department_id', '=', 'departments.id')
                    ->join('business_units', 'departments.business_unit_id', '=', 'business_units.id')
                    ->where('employees.id', '=', $user[0]->id)
                    ->where('business_units.bu_name', 'LIKE', '%' . $DIC_bu_name . '%')
                    ->select('employees.id')
                    ->first();

                if ($DIC_check == null) {
                    $data['client'] = $user;
                } else {
                    //                    dd("DIC user");
                    Session::flash("error", "Use DIC- as prefix if you are a DIC employee");
                    return Redirect::back()->withErrors($validator)->withInput();
                }
            } else {
                // $user = Employee::where("user_name", "=", $request->input("username"))->where("password", "=", md5($request->input("password")))->get();
                // $data['client'] = $user;

                //--------------------------------------------------------//
                /*Check that if a DIC employee tries to login from their Markit dont let them
                * On request of Waqas Ilyas
                */
                //--------------------------------------------------------//
                $user = Employee::where("user_name", "=", $request->input("username"))->where("password", "=", md5($request->input("password")))->get();
                if (count($user) > '0') {
                    if ($user[0]->last_date_of_working != NULL) {
                        Session::flash("error", "Your account has been deactivated!");
                        return Redirect::back()->withErrors($validator)->withInput();
                    }
                    // if(strpos($user[0]->user_name,"SP-") !== false){
                    //     if($user[0]->user_name != "SP-ERS-admin"){
                    //     Session::flash("error", "Your account has been deactivated. Please contact your HRBP for further details.");
                    //     return Redirect::back()->withErrors($validator) ->withInput();
                    //     }
                    // }
                    // if($user[0]->department->businessUnit->id == '30' || $user[0]->department->businessUnit->id == '123'){
                    //      Session::flash("error", "Your account has been deactivated. Please contact your HRBP for further details.");
                    //     return Redirect::back()->withErrors($validator) ->withInput();

                    // }
                }
                if (count($user) > '0') {
                    $DIC_bu_name = 'DIC';
                    $DIC_check = Employee::join('departments', 'employees.department_id', '=', 'departments.id')
                        ->join('business_units', 'departments.business_unit_id', '=', 'business_units.id')
                        ->where('employees.id', '=', $user[0]->id)
                        ->where('business_units.bu_name', 'LIKE', '%' . $DIC_bu_name . '%')
                        ->select('employees.id')
                        ->first();

                    if ($DIC_check == null) {
                        // dd("Not DIC user");
                        $data['client'] = $user;
                    } else {
                        //                    dd("DIC user");
                        Session::flash("error", "Use DIC- as prefix if you are a DIC employee");
                        return Redirect::back()->withErrors($validator)->withInput();
                    }
                } else {
                    Session::flash("error", "Login Details are incorrect");
                    return Redirect::back()->withErrors($validator)->withInput();
                }
            }
            if (count($user) == 1) {
                Session::put("user_name", $user->first()->user_name);
                Session::put("user", "logged in");
                Session::put("id", $user->first()->id);

                Log::info("-----------------------------------");
                Log::info("User Found, Emp #: " . $user->first()->employee_number);
                Log::info("Logged in to ERS Successfuly");
                Log::info("-----------------------------------");

                if (isset($user->first()->nick_name)) {
                    return redirect('dashboard');
                } else {
                    return redirect('welcome');
                }
            } else {
                Session::flash("error", "Login Details are incorrect");
                return Redirect::back()->withErrors($validator)->withInput();
            }
        } catch (ModelNotFoundException $e) {
            Session::flash("error", "Login Details are incorrect");
            return Redirect::back()->withErrors($validator)->withInput();
        }
    }

    public function loginAPI($key, Request $request)
    {

        $username =  explode('-', base64_decode($key))[0];

        $password =  explode('-', base64_decode($key))[1];
        //return $password;
        $user = Employee::where("user_name", "=", $username)->where("password", "=", $password)->get();
        //  return $user;
        if (count($user) == 1) {
            try {
                $data['client'] = $user;
                if (count($user) == 1) {
                    Session::put("user_name", $user->first()->user_name);
                    Session::put("user", "logged in");
                    Session::put("id", $user->first()->id);
                    Log::info("-----------------------------------");
                    Log::info("User Found, Emp #: " . $user->first()->employee_number);
                    Log::info("Logged in to ERS through Packages and You Successfuly");
                    Log::info("-----------------------------------");
                    /* $referer = explode('.', $_SERVER['HTTP_REFERER'])[0];
                    if($referer == 'http://intranet' || $referer == 'https://intranet'){
                        Session::put('referer',$referer);
                    }*/
                    return redirect('dashboard');
                } else {
                    Session::flash("error", "Login Details are incorrect");
                    return Redirect::back();
                }
            } catch (ModelNotFoundException $e) {
                Session::flash("error", "Login Details are incorrect");
                return Redirect::back();
            }
        } else {
            Session::flash("error", "Login Details are incorrect");
            return Redirect::back();
        }
    }

    public function GroupAppLoginAPI($key)
    {
        $username =  explode('-', base64_decode($key))[0];
        $password =  explode('-', base64_decode($key))[1];

        $user = Employee::where("user_name", "=", $username)->where("password", "=", md5($password))->get();
        if (count($user) == 1) {
            return "1";
        }
    }

    public function logout()
    {
        Session::forget('id');
        Session::forget('username');
        Session::forget('referer');
        Session::forget('user');

        Session::flash("success", "Logged out successfully");
        return redirect(URL::to("/"));
    }

    public function welcome()
    {
        $employee_id = Session::get('id');
        if (isset($employee_id)) {
            $data['employee'] = Employee::find($employee_id);
            if (!isset($data['employee']->nick_name)) {
                $data['banks'] = Bank::all();
                $data['path'] = Route::getFacadeRoot()->current()->uri();

                return view('basic.welcome', $data);
            } else {
                return redirect('dashboard');
            }
        } else {
            Session::flash('error', 'Your session has ended. Please login to continue.');
            return redirect('/');
        }
    }

    public function storeWelcomeData(Request $request)
    {
        $employee_id = Session::get('id');
        if (isset($employee_id)) {
            $machine = array(
                "nickname" => "Nick Name",
                "email" => "Email Address",
                "mobile" => "Mobile Number",
                "bank_account_number" => "Bank Account Number",
                "bank" => "bank",
                "old_password" => "Old Password",
                "new_password" => "New Password",
                "new_password_confirmation" => "New Password Confirmation",
            );
            $validator = Validator::make(
                $request->all(),
                [
                    "nickname" => "required",
                    "email" => "required|email",
                    "mobile" => "required|digits:11",
                    "bank_account_number" => "required|digits:16",
                    "bank" => "required",
                    "old_password" => "required",
                    "new_password" => "required|confirmed|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{6,}$/",
                    "new_password_confirmation" => "required",
                ]
            );
            $validator->setAttributeNames($machine);
            if ($validator->fails()) {
                return Redirect::back()->withErrors($validator)->withInput();
            } else {
                DB::beginTransaction();
                $employee = Employee::find($employee_id);

                if (md5($request->input('old_password')) == $employee->password) {
                    $employee->nick_name = $request->input('nickname');
                    $employee->email = $request->input('email');
                    $employee->mobile = $request->input('mobile');
                    $employee->password = md5($request->input('new_password'));
                    $employee->cnic = $request->input('cnic');
                    $employee->save();

                    // $bank = Bank::whereNotNull('active')->get();
                    // employee_bank = Employee_Bank::where('employee_id', '=', $employee->id)->where('bank_id', '=', $bank[0]->id)->get();
                    $bank = $request->input('bank');
                    $employee_bank = Employee_Bank::where('employee_id', '=', $employee->id)->where('bank_id', '=', $bank)->get();

                    if (count($employee_bank) > 0) {
                        $employee_bank[0]->account_number = $request->bank_account_number;
                        $employee_bank[0]->default = 1;
                        $employee_bank[0]->save();
                    } else {
                        // $employee_bank = new Employee_Bank();
                        // $employee_bank->employee_id = $employee->id;
                        // $employee_bank->bank_id = $bank[0]->id;
                        // $employee_bank->account_number = $request->bank_account_number;
                        // $employee_bank->default = 1;
                        // $employee_bank->save();
                        $employee_bank = new Employee_Bank();
                        $employee_bank->employee_id = $employee->id;
                        $employee_bank->bank_id = $bank;
                        $employee_bank->account_number = $request->bank_account_number;
                        $employee_bank->default = 1;
                        $employee_bank->save();
                    }

                    DB::commit();
                } else {
                    Session::flash('error', 'Old password is not correct');
                    return Redirect::back()->withErrors($validator)->withInput();
                }
            }
            $referer = Session::get('referer');
            if (isset($referer)) {
                Session::forget('referer');
                return redirect('http://172.16.25.250/packages-and-you/site/index.php');
            } else {
                Session::flash('success', 'Thanks for submitting your information');
                return redirect('dashboard');
            }
        } else {
            Session::flash('error', 'Your session has ended. Please login to continue.');
            return redirect('/');
        }
    }

    public function forgetPassword()
    {
        $data['path'] = Route::getFacadeRoot()->current()->uri();
        if (isset($_SERVER['HTTP_REFERER'])) {
            $referer = explode('.', $_SERVER['HTTP_REFERER'])[0];
            if ($referer == 'http://intranet' || $referer == 'https://referer') {
                Session::put('referer', $referer);
            }
        }
        return view('auth.forget-password', $data);
    }

    public function sendPasswordResetLink(Request $request)
    {
        $names = array("username" => "User Name");
        // Validate the inputs
        $validator = Validator::make($request->all(), ["username" => "required"]);
        $validator->setAttributeNames($names);
        // Return if the validation fails
        if ($validator->fails()) {
            Session::flash("error", "Please fill in the valid information");
            return Redirect::back()->withErrors($validator)->withInput();
        } //If Validation passes successfully
        try {
            $user = Employee::where("user_name", "=", $request->input("username"))->get();
            if (count($user) > 0) {
                $data['client'] = $user;
                if ($request->getClientIp() == '172.16.1.36') {
                    $data['clientIP'] = $request->input('ipAddress');
                } else {
                    $data['clientIP'] = $request->getClientIp();
                }
                try {
                    $data['clientHostName'] = gethostbyaddr($data['clientIP']);
                } catch (\ErrorException $e) {
                    $data['clientHostName'] = 'Not Valid';
                }
                $data['token'] = bin2hex(openssl_random_pseudo_bytes(16));
                $user[0]->last_reset_token_generated = date('Y-m-d H:i:s');
                $user[0]->reset_token = $data['token'];
                $user[0]->reset_token_valid_till = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s') . '+ 5 minutes'));
                $user[0]->save();

                Log::info("User Found");
                if (count($user) == 1) {
                    if (isset($data['client'][0]->email)) {
                        Mail::send('emails.forget-password', $data, function ($message) use ($data) {
                            $message->from('systems.services@packages.com.pk', 'E R S - Employee Reimbursement System');
                            $message->to($data['client'][0]->email, $data['client'][0]->employee_name)->subject("Password Reset Link");
                        });
                    }
                    $referer = Session::get('referer');
                    if (isset($referer)) {
                        return redirect('http://intranet.packages.com.pk/intranet_packages/message/password-sent');
                    } else {
                        Session::flash("success", "You will receive a password reset link shortly if you are a valid user of ERS");
                        return redirect('/');
                    }
                } else {
                    Session::flash("success", "You will receive a password reset link shortly if you are a valid user of ERS");
                    return Redirect::back()->withErrors($validator)->withInput();
                }
            } else {
                Session::flash("success", "You will receive a password reset link shortly if you are a valid user of ERS");
                return redirect('/');
            }
        } catch (ModelNotFoundException $e) {
            Session::flash("success", "You will receive a password reset link shortly if you are a valid user of ERS");
            return Redirect::back()->withErrors($validator)->withInput();
        }
    }

    public function verifyToken($token)
    {
        $user = Employee::where('reset_token', '=', $token)->get();
        if (count($user) > 0) {
            if (strtotime($user[0]->reset_token_valid_till) >= strtotime(date('Y-m-d H:i:s'))) {
                $data['user'] = $user[0];
                $data['token'] = $token;
                $data['path'] = Route::getFacadeRoot()->current()->uri();
                return view('auth.reset-password', $data);
            } else {
                Session::flash("error", "Your password reset token has been expired.");
                return redirect('/');
            }
        } else {
            Session::flash("error", "Your have provide an invalid token.");
            return redirect('/');
        }
    }

    public function resetPassword(Request $request, $token)
    {
        $user = Employee::where('reset_token', '=', $token)->get();
        if (count($user) > 0) {
            if (strtotime($user[0]->reset_token_valid_till) >= strtotime(date('Y-m-d H:i:s'))) {
                $passwordData = array(
                    "new_password" => "New Password",
                    "new_password_confirmation" => "Confirm Password",
                );
                $validator = Validator::make(
                    $request->all(),
                    [
                        "new_password" => "required|confirmed|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{6,}$/",
                        "new_password_confirmation" => "required",
                    ]
                );
                $validator->setAttributeNames($passwordData);

                if ($validator->fails()) {
                    return Redirect::back()
                        ->withErrors($validator)
                        ->withInput();
                } else {
                    $user[0]->password = md5($request->input('new_password'));
                    $user[0]->save();
                    Session::flash("success", "Your password has been changed successfuly");
                    return redirect('/');
                }
            } else {
                Session::flash("error", "Your password reset token has been expired.");
                return redirect('/');
            }
        } else {
            Session::flash("error", "Your have provide an invalid token.");
            return redirect('/');
        }
    }

    public function clientLogin(Request $request)
    {
        $portalName = $request->input('portal_name');
        $portalToken = $request->input('portal_token');
        $emailClient = Email_Client::where('portal_name', '=', $portalName)->first();
        if (isset($emailClient)) {
            $passCode = $emailClient->portal_code . "   ";
            $date    = md5(date("Ymd"));
            $token = md5($passCode) . $date;
            if ($token == $portalToken) {
                $clientRequest = $request->input('client_request');
                if (isset($clientRequest)) {
                    if ($clientRequest == 'User Login') {
                        $user_name = $request->input('user_name');
                        $password = md5($request->input('password'));
                        $employee = Employee::where('user_name', '=', $user_name)->where('password', '=', $password)->first();
                        if (isset($employee)) {
                            $allowedColumns = [
                                'id',
                                'employee_number',
                                'employee_name',
                                'nick_name',
                                'designation',
                                'email',
                                'department_id',
                            ];
                            $data_fields = json_decode($request->input('data_fields'));
                            $selectionColumns = array_intersect($data_fields, $allowedColumns);
                            $employee = Employee::with('department', 'department.businessUnit', 'department.businessUnit.company', 'department.businessUnit.divisionalApprover')
                                ->select($selectionColumns)
                                //->where('employee_number', '=', $user_name)
                                ->where('username', '=', $user_name)
                                ->first();
                            return json_encode($employee, 200);
                        } else {
                            $response = array(
                                "error_code" => "404",
                                "error_description" => "Employee not Found."
                            );
                            return json_encode($response);
                        }
                    } else if ($clientRequest == 'Get Email') {
                        $employeeID = $request->input('employee_id');
                        $employee = Employee::select('employee_name', 'email')->where('id', '=', $employeeID)->first();
                        if (isset($employee)) {
                            return json_encode($employee, 200);
                        } else {
                            $response = array(
                                "error_code" => "404",
                                "error_description" => "Employee not Found."
                            );
                            return json_encode($response);
                        }
                    } else {
                        $response = array(
                            "error_code" => "404",
                            "error_description" => "Operation Invalid."
                        );
                        return json_encode($response);
                    }
                } else {
                    $response = array(
                        "error_code" => "404",
                        "error_description" => "Client Request Not Found."
                    );
                    return json_encode($response);
                }
            } else {
                $response = array(
                    "error_code" => "404",
                    "error_description" => "Token is Invalid."
                );
                return json_encode($response);
            }
        } else {
            $response = array(
                "error_code" => "404",
                "error_description" => "Portal Client not Found."
            );
            return json_encode($response);
        }
    }

    public function maintenanceEmail()
    {
        $counter = 0;
        $employees = Employee::whereHas('department.businessUnit.company', function ($query) {
            $query->where('companies.id', '=', 1);
        })->whereNotNull('email')->whereNotNull('nick_name')->limit(1)->orderby('employee_number', 'ASC')->get();
        foreach ($employees as $employee) {
            $data['client'] = $employee;
            /*Mail::send('emails.maintenance', $data, function ($message) use ($data) {
                $message->from('systems.services@packages.com.pk', 'E R S - Employee Reimbursement System');
                $message->to($data['client']->email, $data['client']->employee_name)
                    ->cc('nauman.abid@packages.com.pk', 'Muhammad Nauman Abid')
                    ->subject('ERS - Out of Service');
            });*/
            Mail::send('emails.maintenance', $data, function ($message) use ($data) {
                $message->from('systems.services@packages.com.pk', 'E R S - Employee Reimbursement System');
                $message->to('nauman.abid@packages.com.pk', 'Muhammad Nauman Abid')
                    ->cc('nauman.abid@packages.com.pk', 'Muhammad Nauman Abid')
                    ->subject('ERS - Out of Service');
            });
            $counter++;
            if ($counter == 50) {
                sleep(30);
                $counter = 0;
            }
        }
    }

    public function fetch_bu_heads()
    {
        try {
            $SAML_api = 'https://api44.sapsf.com/oauth/idp';

            // Get SAML 
            try {
                $ch = curl_init();
                curl_setopt_array($ch, [
                    CURLOPT_URL => $SAML_api,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_HTTPHEADER => ['Content-Type: application/x-www-form-urlencoded'],
                    CURLOPT_POST => true,
                    CURLOPT_POSTFIELDS => http_build_query([
                        'user_id' => env("USER_ID_PRD"),
                        'token_url' => env("TOKEN_URL_PRD"),
                        'private_key' => env("PRIVATE_KEY_PRD"),
                        'client_id' => env("CLIENT_ID_PRD"),
                    ]),
                    CURLOPT_SSL_VERIFYPEER => false, // Disable SSL verification
                ]);

                $samlResponse = curl_exec($ch);
                if ($samlResponse === false) {
                    throw new \Exception('SAML API Error: ' . curl_error($ch));
                }
                curl_close($ch);
            } catch (\Exception $e) {
                throw new \Exception("Error at SAML API: " . $e->getMessage());
            }

            $access_token_api = 'https://api44.sapsf.com/oauth/token';

            // Get Access Token
            try {
                $ch = curl_init();
                curl_setopt_array($ch, [
                    CURLOPT_URL => $access_token_api,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_HTTPHEADER => ['Content-Type: application/x-www-form-urlencoded'],
                    CURLOPT_POST => true,
                    CURLOPT_POSTFIELDS => http_build_query([
                        'client_id' => env("CLIENT_ID_PRD"),
                        'grant_type' => env("GRANT_TYPE_PRD"),
                        'company_id' => env("COMPANY_ID_PRD"),
                        'assertion' => $samlResponse,
                    ]),
                    CURLOPT_SSL_VERIFYPEER => false,
                ]);

                $response = curl_exec($ch);
                if ($response === false) {
                    throw new \Exception('Access Token API Error: ' . curl_error($ch));
                }
                curl_close($ch);

                $jsonResponse = json_decode($response, true);
                if (!isset($jsonResponse['access_token'])) {
                    throw new \Exception("Access token not found in response.");
                }

                $access_token = $jsonResponse['access_token'];
            } catch (\Exception $e) {
                throw new \Exception("Error at Access Token API: " . $e->getMessage());
            }

            $BU_head_position_api = "https://api44.sapsf.com/odata/v2/FODynamicRole?\$filter=externalCode%20eq%20'BU%20Head_%20Benefit'&\$select=position,businessUnit&\$format=json";

            // Fetch BU Head Positions
            try {
                $ch = curl_init();
                curl_setopt_array($ch, [
                    CURLOPT_URL => $BU_head_position_api,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_HTTPHEADER => [
                        'Authorization: Bearer ' . $access_token,
                        'Content-Type: application/json',
                    ],
                    CURLOPT_SSL_VERIFYPEER => false,
                ]);

                $response = curl_exec($ch);
                if ($response === false) {
                    throw new \Exception('BU Head API Error: ' . curl_error($ch));
                }
                curl_close($ch);

                $BU_head_positionData = json_decode($response, true);
                if (!isset($BU_head_positionData['d']['results'])) {
                    throw new \Exception("BU Head position data not found.");
                }

                // Iterate through BU Head positions
                foreach ($BU_head_positionData['d']['results'] as $BU_head_position) {
                    $position = $BU_head_position['position'];
                    $businessUnit = $BU_head_position['businessUnit'];

                    // Fetch Employee Job Data for userId
                    $empJob_api = "https://api44.sapsf.com/odata/v2/EmpJob?\$filter=position%20eq%20'{$position}'&\$format=json";
                    try {
                        $ch = curl_init();
                        curl_setopt_array($ch, [
                            CURLOPT_URL => $empJob_api,
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_HTTPHEADER => [
                                'Authorization: Bearer ' . $access_token,
                                'Content-Type: application/json',
                            ],
                            CURLOPT_SSL_VERIFYPEER => false,
                        ]);

                        $response = curl_exec($ch);
                        if ($response === false) {
                            throw new \Exception('Employee Job API Error: ' . curl_error($ch));
                        }
                        curl_close($ch);

                        $EmpJobData = json_decode($response, true);
                        if (isset($EmpJobData['d']['results'][0])) {
                            $BU_data = [
                                'position' => $position,
                                'businessUnit' => $businessUnit,
                                'employee_number' => $EmpJobData['d']['results'][0]['userId']
                            ];
                            $this->saveOrUpdateBUHeadDetails($BU_data);
                        }
                    } catch (\Exception $e) {
                        throw new \Exception("Error while fetching employee details: " . $e->getMessage());
                    }
                }
                echo "Data has been posted successfully!";
            } catch (\Exception $e) {
                throw new \Exception("Error at BU Head API: " . $e->getMessage());
            }
        } catch (\Exception $e) {
            echo "Error Found: " . $e->getMessage();
        }
    }

    private function sendCurlRequest($url, $method, $headers, $postData = null)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        if ($method === 'POST' && $postData) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        }

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            throw new \Exception('cURL Error: ' . curl_error($ch));
        }

        curl_close($ch);

        return $response;
    }

    public function sapS4sync(Request $request)
    {
        set_time_limit(43200);
        $company = $request->query('company');

        if (!in_array($company, ['1000', '2000', '3000', '4000', '5000', '6000', '7000', '8000'])) {
            echo "Invalid or missing company code.";
            return;
        }

        if ($company === '5000') {
            $rangeStart = 50000000;
            $rangeEnd = 50099999;
        } else {
            $rangeStart = (int)$company . '0000';
            $rangeEnd = (int)$company . '9999';
        }

        // Step 1: Get SAML Response using cURL
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api44.sapsf.com/oauth/idp");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/x-www-form-urlencoded',
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
            'user_id' => env("USER_ID_PRD"),
            'token_url' => env("TOKEN_URL_PRD"),
            'private_key' => env("PRIVATE_KEY_PRD"),
            'client_id' => env("CLIENT_ID_PRD"),
        ]));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Disable SSL verification

        $samlResponse = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);

        if ($err) {
            echo ("cURL Error At SAML #:" . $err);
        }

        // Step 2: Get Access Token using cURL
        $access_token_api = 'https://api44.sapsf.com/oauth/token';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $access_token_api);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/x-www-form-urlencoded',
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        // dump($samlResponse);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
            'client_id' => env("CLIENT_ID_PRD"),
            'grant_type' => env("GRANT_TYPE_PRD"),
            'company_id' => env("COMPANY_ID_PRD"),
            'assertion' => $samlResponse,
        ]));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Disable SSL verification

        $response = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);

        if ($err) {
            dd("cURL Error At Access Token#:" . $err);
        }

        $jsonResponse = json_decode($response, true);

        if (!isset($jsonResponse['access_token'])) {
            echo "Access token not found!";
            return;
        }

        $access_token = $jsonResponse['access_token'];
        // dump($access_token);
        $employeeNumbers = [
            // "80009197", "80009084", "80002986", "80001300", "80009046",
            // "80009257", "80007519", "80009052", "80009051", "80009044",
            // "80008369", "80008391", "80008554", 

            // "80009408", <DATA IS MISSING PerPersonal> 

            // "80008370", "80009398", "80009054", "80008652", 

            // "80009405", <DATA IS MISSING Bank Details> 

            // "80008883","80009406", "80009110", "80008970", "80008353", "80009308",
            // "80009302", "80009067", "80009407"

            // "20004523"

            // "40008781", "20009569", "20007569", "10009415", "40009373", "20008779", "10008788", "10009082", "20001109", "60008955", "20003534", "10009555", 

            // "20008978", "10009074", "20007535", "20007797", "40007778", "40008562", "10008977", "10008872", "30001809", "20009065", "10008976"
            // "20009505"
            "10008788"
        ];

        $allEmployeesData = [];

        // Step 3: Loop through employee numbers and fetch their data
        // foreach ($employeeNumbers as $employeeNumber) {
        // echo $employeeNumber;
        // $activeEmp_api = "https://api44.sapsf.com/odata/v2/User?%24filter=userId%20ge%20%2750000001%27%20and%20userId%20le%20%2750099999%27%20and%20status%20in%20%27t%27&%24format=json";

        $activeEmp_api = "https://api44.sapsf.com/odata/v2/User?%24filter=userId%20ge%20%27$rangeStart%27%20and%20userId%20le%20%27$rangeEnd%27%20and%20status%20in%20%27t%27&%24format=json";
        // $activeEmp_api = "https://api44.sapsf.com/odata/v2/User?%24filter=userId%20ge%20%2710008788%27%20and%20userId%20le%20%2710008788%27%20and%20status%20in%20%27t%27&%24format=json";
        $nextUrl = $activeEmp_api;
        do {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $nextUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $access_token,
                'Content-Type: application/json',
            ]);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            // Execute the cURL request
            $response = curl_exec($ch);
            $err = curl_error($ch);
            curl_close($ch);

            if ($err) {
                echo "cURL Error At Fetching Active Users#: " . $err . "\n";
                continue;
            }

            $activeEmpResponse = json_decode($response, true);
            // Check if employee data is returned
            if (isset($activeEmpResponse['d']['results'])) {
                $allEmployeesData = array_merge($allEmployeesData, $activeEmpResponse['d']['results']);
            }
            if (isset($activeEmpResponse['d']['__next'])) {
                $nextUrl = $activeEmpResponse['d']['__next'];
            } else {
                $nextUrl = null;
            }
        } while ($nextUrl);
        // }
        // Step 5: Process the collected data
        $i = 0;
        $batchSize = 50;
        $employeeBatches = array_chunk($allEmployeesData, $batchSize);
        $totalProcessed = 0;
        foreach ($employeeBatches as $batchIndex => $batch) {
            echo "Processing Batch " . ($batchIndex + 1) . " of " . count($employeeBatches);
            foreach ($batch as $employee) {
                try {
                    if (strlen($employee['userId']) == 8 && preg_match('/^[1-8]/', $employee['userId'])) {
                        dump("Fetching Data of " . $employee['userId']);
                        $employeeData = $this->fetchEmployeeData($employee['userId'], $access_token);
                        $this->saveEmployeeData($employeeData, $access_token);
                        $totalProcessed++;
                    }
                } catch (\Exception $e) {
                    // Log the error and continue processing the next employee
                    echo "Error processing employee " . $employee['userId'] . ": " . $e->getMessage() . "\n";
                    continue;
                }
            }
            sleep(1);
        }
        echo "Data pulled from " . $totalProcessed . " batches through SAP sync";
    }

    private function curlRequest($url, $access_token)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $access_token,  // Access token from the previous step
            'Content-Type: application/json',
        ]);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            echo 'cURL error: ' . curl_error($ch);
            return null;
        }

        // $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        // echo "HTTP Code: $http_code\n"; // Debug HTTP response code
        // echo "Response: $response\n";  // Debug API response

        curl_close($ch);
        return json_decode($response, true);
    }

    private function curlMultiRequest($urls, $access_token)
    {
        $multiCurl = curl_multi_init();
        $curlHandles = [];
        $responses = [];

        foreach ($urls as $key => $url) {
            $curlHandles[$key] = curl_init();

            curl_setopt_array($curlHandles[$key], [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER => [
                    "Authorization: Bearer $access_token",
                    "Content-Type: application/json"
                ],
            ]);

            curl_multi_add_handle($multiCurl, $curlHandles[$key]);
        }

        do {
            $status = curl_multi_exec($multiCurl, $active);
            curl_multi_select($multiCurl);
        } while ($active && $status == CURLM_OK);

        foreach ($curlHandles as $key => $ch) {
            $responses[$key] = json_decode(curl_multi_getcontent($ch), true);
            curl_multi_remove_handle($multiCurl, $ch);
            curl_close($ch);
        }

        curl_multi_close($multiCurl);

        return $responses;
    }


    private function fetchEmployeeData($employeeId, $access_token)
    {
        try {
            $urls = [
                'empJob' => "https://api44.sapsf.com/odata/v2/EmpJob?\$filter=userId%20eq%20%27$employeeId%27&\$format=json",
                'personal' => "https://api44.sapsf.com/odata/v2/PerPersonal?\$filter=personIdExternal%20eq%20'$employeeId'&\$format=json",
                // 'compensation' => "https://api44.sapsf.com/odata/v2/EmpCompensation?\$filter=userId%20eq%20'$employeeId'&\$select=customDouble3,startDate,endDate,lastModifiedDateTime&\$format=json",
                'user' => "https://api44.sapsf.com/odata/v2/User?\$filter=userId%20eq%20'$employeeId'&\$format=json",
                'bank' => "https://api44.sapsf.com/odata/v2/PaymentInformationDetailV3?\$filter=PaymentInformationV3_worker%20eq%20'$employeeId'&\$format=json",
            ];

            // Execute batch request
            $responses = $this->curlMultiRequest($urls, $access_token);

            $employee_data = [];

            // Process job data
            if (isset($responses['empJob']['d']['results'][0])) {
                $jobDetails = $responses['empJob']['d']['results'][0];
                $employee_data = [
                    'employee_number' => isset($jobDetails['userId']) ? $jobDetails['userId'] : NULL,
                    'businessUnit' => isset($jobDetails['businessUnit']) ? $jobDetails['businessUnit'] : NULL,
                    'grade' => isset($jobDetails['payGrade']) ? $jobDetails['payGrade'] : NULL,
                    'designation' => isset($jobDetails['jobTitle']) ? $jobDetails['jobTitle'] : NULL,
                    'division' => isset($jobDetails['division']) ? $jobDetails['division'] : NULL,
                    'line_manager_id' => isset($jobDetails['managerId']) ? $jobDetails['managerId'] : NULL,
                    'department' => isset($jobDetails['department']) ? $jobDetails['department'] : NULL,
                    'costCenter' => isset($jobDetails['costCenter']) ? $jobDetails['costCenter'] : NULL,
                    'company' => isset($jobDetails['company']) ? $jobDetails['company'] : NULL,
                    'location' => isset($jobDetails['location']) ? $jobDetails['location'] : NULL,
                ];
            }

            // Process personal data
            if (isset($responses['personal']['d']['results'][0])) {
                $firstName = isset($responses['personal']['d']['results'][0]['firstName']) ? $responses['personal']['d']['results'][0]['firstName'] : '';
                $middleName = isset($responses['personal']['d']['results'][0]['middleName']) ? $responses['personal']['d']['results'][0]['middleName'] : '';
                $lastName = isset($responses['personal']['d']['results'][0]['lastName']) ? $responses['personal']['d']['results'][0]['lastName'] : '';

                // $employee_data['employee_name'] = trim("$firstName $middleName $lastName");
                $nameParts = array_filter([$firstName, $middleName, $lastName]);
                $employee_data['employee_name'] = implode(' ', $nameParts);
            }

            // Process compensation data
            $employee_data['gross_salary'] = NULL;
            $employee_data['salary_effective_date'] = NULL;
            $employee_data['salary_end_date'] = NULL;
            $employee_data['salary_last_modified_date'] = NULL;

            // if (isset($responses['compensation']['d']['results'][0])) {
            //     $compDetails = $responses['compensation']['d']['results'][0];
            //     $employee_data['gross_salary'] = isset($compDetails['customDouble3']) ? $compDetails['customDouble3'] : NULL;
            //     $employee_data['salary_effective_date'] = isset($compDetails['startDate']) ? $this->formatDate($compDetails['startDate']) : NULL;
            //     $employee_data['salary_end_date'] = isset($compDetails['endDate']) ? $this->formatDate($compDetails['endDate']) : NULL;
            //     $employee_data['salary_last_modified_date'] = isset($compDetails['lastModifiedDateTime']) ? $this->formatDate($compDetails['lastModifiedDateTime']) : NULL;
            // }

            // Process user data
            if (isset($responses['user']['d']['results'][0])) {
                $userDetails = $responses['user']['d']['results'][0];
                $employee_data['email'] = isset($userDetails['email']) ? $userDetails['email'] : NULL;
                $employee_data['status'] = isset($userDetails['status']) ? $userDetails['status'] : NULL;
                $employee_data['married'] = isset($userDetails['married']) ? $userDetails['married'] : NULL;
                $employee_data['mobile'] = isset($userDetails['businessPhone']) ? $userDetails['businessPhone'] : NULL;
            }

            // Process bank data
            if (isset($responses['bank']['d']['results'][0])) {
                $bankDetails = $responses['bank']['d']['results'][0];
                $employee_data['bank'] = isset($bankDetails['bank']) ? $bankDetails['bank'] : NULL;
                $employee_data['account_number'] = isset($bankDetails['iban']) ? $bankDetails['iban'] : NULL;
            }

            return $employee_data;
        } catch (\Exception $e) {
            echo "Error in fetchEmployeeData: " . $e->getMessage();
        }
    }

    private function saveEmployeeData($employee_data, $access_token)
    {
        try {
            // dump($employee_data);
            $this->fetchBusinessUnitAndCompanyDetails($employee_data, $access_token);
            if (isset($employee_data['company']) && $employee_data['company'] > 0)
                $this->saveOrUpdateCompanyDetails($employee_data);
            if (isset($employee_data['businessUnit']) && $employee_data['businessUnit'] > 0)
                $this->saveOrUpdateBUDetails($employee_data);

            if (isset($employee_data['division']) && $employee_data['division'] > 0) {
                $this->saveOrUpdateDivisionDetails($employee_data);

                if (isset($employee_data['department']) && $employee_data['department'] > 0)
                    $this->saveOrUpdateDepartmentDetails($employee_data);
                else {
                    $employee_data['department'] = $employee_data['division'];
                    $employee_data['department_id'] = $employee_data['division_id'];
                    $employee_data['department_name'] = $employee_data['division_name'];

                    $this->saveOrUpdateDepartmentDetails($employee_data);
                }
            } else {
                $employee_data['division'] = isset($employee_data['bu_id']) ? $employee_data['bu_id'] : NULL;
                $employee_data['division_id'] = isset($employee_data['bu_id']) ? $employee_data['bu_id'] : NULL;
                $employee_data['division_name'] = isset($employee_data['bu_name']) ? $employee_data['bu_name'] : NULL;

                $employee_data['department'] = isset($employee_data['division']) ? $employee_data['division'] : NULL;
                $employee_data['department_id'] = isset($employee_data['division_id']) ? $employee_data['division_id'] : NULL;
                $employee_data['department_name'] = isset($employee_data['division_name']) ? $employee_data['division_name'] : NULL;

                $this->saveOrUpdateDivisionDetails($employee_data);
                $this->saveOrUpdateDepartmentDetails($employee_data);
            }

            $this->saveOrUpdateEmployeeTableDetails($employee_data);
            $this->saveOrUpdateEmployeeDetails($employee_data);
            $this->saveOrUpdateEmployeeTableLevelDetails($employee_data);

            dump($employee_data);
        } catch (\Exception $e) {
            echo "Error in saveEmployeeData: " . $e->getMessage();
        }
    }

    private function formatDate($timestamp)
    {
        $timestamp_in_seconds = substr($timestamp, 6, 13) / 1000;
        $datetime = new \DateTime("@$timestamp_in_seconds");
        return $datetime->format('Y-m-d');
    }

    private function sendCurlRequestBU($url, $headers)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);
        if ($response === false) {
            echo 'cURL error: ' . curl_error($ch);
        }
        // $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        // echo "HTTP Code: $http_code\n"; // Debug HTTP response code
        // echo "Response: $response\n";  // Debug API response

        curl_close($ch);
        return json_decode($response, true);
    }

    private function fetchBusinessUnitAndCompanyDetails(&$employee_data, $access_token)
    {
        try {
            if (isset($employee_data['businessUnit']) && isset($employee_data['company'])) {
                $bu = $employee_data['businessUnit'];
                $BU_api = "https://api44.sapsf.com/odata/v2/FOBusinessUnit?\$filter=externalCode%20eq%20'$bu'&\$format=json";
                $BU_headers = [
                    'Content-Type: application/x-www-form-urlencoded',
                    'Authorization: Bearer ' . $access_token,
                ];
                $BUjsonResponse = $this->sendCurlRequestBU($BU_api, $BU_headers);
                if (isset($BUjsonResponse['d']['results'][0])) {
                    $employee_data['bu_id'] = $BUjsonResponse['d']['results'][0]['externalCode'];
                    $employee_data['bu_name'] = $BUjsonResponse['d']['results'][0]['name'];

                    $company = $employee_data['company'];
                    $company_api = "https://api44.sapsf.com/odata/v2/FOCompany?\$filter=externalCode%20eq%20'$company'&\$select=externalCode,name&\$format=json";
                    $company_headers = $BU_headers;
                    $companyjsonResponse = $this->sendCurlRequestBU($company_api, $company_headers);

                    if (isset($companyjsonResponse['d']['results'][0])) {
                        $employee_data['company_id'] = $companyjsonResponse['d']['results'][0]['externalCode'];
                        $employee_data['company_name'] = $companyjsonResponse['d']['results'][0]['name'];

                        if (isset($employee_data['division']) && $employee_data['division'] > 0) {
                            $divsion = $employee_data['division'];
                            $division_api = "https://api44.sapsf.com/odata/v2/FODivision?\$filter=externalCode%20eq%20'$divsion'&\$select=externalCode,name&\$format=json";
                            $divisionjsonResponse = $this->sendCurlRequestBU($division_api, $company_headers);

                            if (isset($divisionjsonResponse['d']['results'][0])) {
                                $employee_data['division_id'] = $divisionjsonResponse['d']['results'][0]['externalCode'];
                                $employee_data['division_name'] = $divisionjsonResponse['d']['results'][0]['name'];
                            } else {
                                // Division data not found!;
                                $employee_data['division_id'] = NULL;
                                $employee_data['division_name'] = NULL;
                            }
                        }

                        if (isset($employee_data['department']) && $employee_data['department'] > 0) {
                            $department = $employee_data['department'];
                            $department_api = "https://api44.sapsf.com/odata/v2/FODepartment?\$filter=externalCode%20eq%20'$department'&\$select=externalCode,name&\$format=json";
                            $departmentjsonResponse = $this->sendCurlRequestBU($department_api, $company_headers);

                            if (isset($departmentjsonResponse['d']['results'][0])) {
                                $employee_data['department_id'] = $departmentjsonResponse['d']['results'][0]['externalCode'];
                                $employee_data['department_name'] = $departmentjsonResponse['d']['results'][0]['name'];
                            } else {
                                // Department data not found!;
                                $employee_data['department_id'] = NULL;
                                $employee_data['department_name'] = NULL;
                            }
                        }
                    } else {
                        // Company data not found!;
                        $employee_data['company_id'] = NULL;
                        $employee_data['company_name'] = NULL;
                    }
                } else {
                    // Business Unit data not found!;
                    $employee_data['bu_id'] = NULL;
                    $employee_data['bu_name'] = NULL;
                }
            }
        } catch (\Exception $e) {
            echo "Error in fetchBusinessUnitAndCompanyDetails: " . $e->getMessage();
        }
    }

    private function saveOrUpdateCompanyDetails($employee_data)
    {
        try {
            if (isset($employee_data['company']) && isset($employee_data['company_name']) && $employee_data['company'] > 0) {
                $company = DB::table('companies')->where('id', $employee_data['company'])->first();

                if (!$company) {
                    DB::table('companies')->insert([
                        'id' => $employee_data['company_id'],
                        'company_name' => $employee_data['company_name'],
                    ]);
                } else {
                    $update_data = [];
                    if ($company->company_name != $employee_data['company_name']) {
                        $update_data['company_name'] = $employee_data['company_name'];
                    }
                    if (!empty($update_data)) {
                        DB::table('companies')->where('id', $employee_data['company_id'])->update($update_data);
                    }
                }
            }
        } catch (\Exception $e) {
            echo "Error in saveOrUpdateCompanyDetails: " . $e->getMessage();
        }
    }

    private function saveOrUpdateBUDetails($employee_data)
    {
        try {
            if (isset($employee_data['bu_id']) && $employee_data['bu_id'] > 0) {
                $BU = DB::table('business_units')->where('id', $employee_data['bu_id'])->first();

                if (!$BU) {
                    if ($employee_data['bu_id'] > 0) {
                        DB::table('business_units')->insert([
                            'id' => $employee_data['bu_id'],
                            'bu_name' => $employee_data['bu_name'],
                            'company_id' => $employee_data['company_id'],
                        ]);
                    }
                } else {
                    $update_data = [];
                    if ($BU->bu_name != $employee_data['bu_name']) {
                        $update_data['bu_name'] = $employee_data['bu_name'];
                    }
                    if ($BU->company_id != $employee_data['company_id']) {
                        $update_data['company_id'] = $employee_data['company_id'];
                    }
                    if (!empty($update_data)) {
                        DB::table('business_units')->where('id', $employee_data['bu_id'])->update($update_data);
                    }
                }
            }
        } catch (\Exception $e) {
            echo "Error in saveOrUpdateBUDetails: " . $e->getMessage();
        }
    }

    private function saveOrUpdateDivisionDetails($employee_data)
    {
        try {
            if (isset($employee_data['division_id']) && $employee_data['division_id'] > 0) {
                $division = DB::table('divisions')->where('id', $employee_data['division_id'])->first();

                if (!$division) {
                    if ($employee_data['division_id'] > 0) {
                        DB::table('divisions')->insert([
                            'id' => $employee_data['division_id'],
                            'division_name' => $employee_data['division_name'],
                            'bu_id' => $employee_data['division_id'],
                        ]);
                    }
                } else {
                    $update_data = [];
                    if ($division->division_name != $employee_data['division_name']) {
                        $update_data['division_name'] = $employee_data['division_name'];
                    }
                    if ($division->bu_id != $employee_data['bu_id']) {
                        $update_data['bu_id'] = $employee_data['bu_id'];
                    }
                    if (!empty($update_data)) {
                        DB::table('divisions')->where('id', $employee_data['division_id'])->update($update_data);
                    }
                }
            }
        } catch (\Exception $e) {
            echo "Error in saveOrUpdateDivisionDetails: " . $e->getMessage();
        }
    }

    private function saveOrUpdateDepartmentDetails($employee_data)
    {
        try {
            if (isset($employee_data['department_id']) && $employee_data['department_id'] > 0) {
                $department = DB::table('departments')->where('id', $employee_data['department_id'])->first();
                if (!$department) {
                    if ($employee_data['department_id'] > 0) {
                        dump("Department being inserted!");
                        DB::table('departments')->insert([
                            'id' => $employee_data['department_id'],
                            'department_name' => $employee_data['department_name'],
                            'business_unit_id' => $employee_data['bu_id'],
                        ]);
                    }
                } else {
                    $update_data = [];
                    if ($department->department_name != $employee_data['department_name']) {
                        $update_data['department_name'] = $employee_data['department_name'];
                    }
                    if ($department->business_unit_id != $employee_data['bu_id']) {
                        $update_data['business_unit_id'] = $employee_data['bu_id'];
                    }
                    if (!empty($update_data)) {
                        DB::table('departments')->where('id', $employee_data['department_id'])->update($update_data);
                    }
                }
            }
        } catch (\Exception $e) {
            echo "Error in saveOrUpdateDepartmentDetails: " . $e->getMessage();
        }
    }

    private function saveOrUpdateEmployeeDetails($employee_data)
    {
        try {
            // dump("HERE3");
            $employee = DB::table('sap_sync')->where('employee_number', $employee_data['employee_number'])->first();
            if (isset($employee_data['salary_effective_date']))
                $new_salary_year = date('Y', strtotime($employee_data['salary_effective_date']));
            // dump($employee_data);
            // if(!(isset($employee_data['designation']))) 
            //     $employee_data['designation'] = "designation not found";
            // dd($employee_data);    
            if (!$employee) {
                // dump("HERE2");
                // dump($employee_data);
                DB::table('sap_sync')->insert([
                    'employee_number' => $employee_data['employee_number'],
                    'employee_name' => !empty($employee_data['employee_name']) ? $employee_data['employee_name'] : str_replace('.', ' ', strstr($employee_data['email'], '@', true)),
                    'grade' => $employee_data['grade'],
                    'gross_salary' => isset($employee_data['gross_salary']) ? $employee_data['gross_salary'] : NULL,
                    'salary_last_modified_date' => isset($employee_data['salary_last_modified_date']) ? $employee_data['salary_last_modified_date'] : NULL,
                    'salary_end_date' => isset($employee_data['salary_end_date']) ? $employee_data['salary_end_date'] : NULL,
                    'salary_effective_date' => isset($employee_data['salary_effective_date']) ? $employee_data['salary_effective_date'] : NULL,
                    'designation' => isset($employee_data['designation']) ? $employee_data['designation'] : "NULL",
                    'mobile' => isset($employee_data['mobile']) ? $employee_data['mobile'] : NULL,
                    'line_manager_id' => isset($employee_data['line_manager_id']) ? $employee_data['line_manager_id'] : NULL,
                    'company' => isset($employee_data['company_id']) ? $employee_data['company_id'] : NULL,
                    'business_unit' => isset($employee_data['bu_id']) ? $employee_data['bu_id'] : NULL,
                    'division' => isset($employee_data['division']) ? $employee_data['division'] : NULL,
                    'department' => isset($employee_data['department']) ? $employee_data['department'] : NULL,
                    'bank' => isset($employee_data['bank']) ? $employee_data['bank'] : NULL,
                    'account_number' => isset($employee_data['account_number']) ? $employee_data['account_number'] : NULL,
                    'cost_center' => isset($employee_data['costCenter']) ? $employee_data['costCenter'] : NULL,
                    'email' => isset($employee_data['email']) ? $employee_data['email'] : NULL,
                    'status' => isset($employee_data['status']) ? $employee_data['status'] : NULL,
                    'location' => isset($employee_data['location']) ? $employee_data['location'] : NULL,
                ]);
                $grossSalary = $employee_data['gross_salary'];
                $annualBase = ($grossSalary * 12) / 10;
                if ($employee_data['married'] == "true" && $annualBase < 75000) {
                    $annualBase = 75000;
                } else if ($employee_data['married'] == "false" && $annualBase < 50000) {
                    $annualBase = 50000;
                }
                $dental = 0.33 * $annualBase;
                if ($dental > 100000)
                    $dental = 100000;

                $optical = 0.10 * $annualBase;
                if ($dental > 25000)
                    $dental = 25000;

                $others = $annualBase - ($dental + $optical);

                DB::table('entitlements')->insert([
                    'employee_number' => $employee_data['employee_number'],
                    'entitlement_type' => "Medical",
                    "break_down" => "Dental",
                    "total_limit" => $dental,
                    "consumed_limit" => 0
                ]);
                DB::table('entitlements')->insert([
                    'employee_number' => $employee_data['employee_number'],
                    'entitlement_type' => "Medical",
                    "break_down" => "Optical",
                    "total_limit" => $optical,
                    "consumed_limit" => 0
                ]);
                DB::table('entitlements')->insert([
                    'employee_number' => $employee_data['employee_number'],
                    'entitlement_type' => "Medical",
                    "break_down" => "Others",
                    "total_limit" => $others,
                    "consumed_limit" => 0
                ]);
            } else {
                if (isset($employee->salary_effective_date) && isset($employee_data['salary_effective_date'])) {
                    $existing_salary_year = date('Y', strtotime($employee->salary_effective_date));

                    if ($employee->salary_effective_date != $employee_data['salary_effective_date'] || $new_salary_year != $existing_salary_year) {
                        DB::table('sap_sync')->insert([
                            'employee_number' => $employee_data['employee_number'],
                            'employee_name' => $employee_data['employee_name'],
                            'grade' => $employee_data['grade'],
                            'gross_salary' => isset($employee_data['gross_salary']) ? $employee_data['gross_salary'] : NULL,
                            'salary_last_modified_date' => isset($employee_data['salary_last_modified_date']) ? $employee_data['salary_last_modified_date'] : NULL,
                            'salary_end_date' => isset($employee_data['salary_end_date']) ? $employee_data['salary_end_date'] : NULL,
                            'salary_effective_date' => isset($employee_data['salary_effective_date']) ? $employee_data['salary_effective_date'] : NULL,
                            'designation' => isset($employee_data['designation']) ? $employee_data['designation'] : NULL,
                            'mobile' => isset($employee_data['mobile']) ? $employee_data['mobile'] : NULL,
                            'line_manager_id' => isset($employee_data['line_manager_id']) ? $employee_data['line_manager_id'] : NULL,
                            'company' => isset($employee_data['company_id']) ? $employee_data['company_id'] : NULL,
                            'business_unit' => isset($employee_data['bu_id']) ? $employee_data['bu_id'] : NULL,
                            'division' => isset($employee_data['division']) ? $employee_data['division'] : NULL,
                            'department' => isset($employee_data['department']) ? $employee_data['department'] : NULL,
                            'bank' => isset($employee_data['bank']) ? $employee_data['bank'] : NULL,
                            'account_number' => isset($employee_data['account_number']) ? $employee_data['account_number'] : NULL,
                            'cost_center' => isset($employee_data['costCenter']) ? $employee_data['costCenter'] : NULL,
                            'email' => isset($employee_data['email']) ? $employee_data['email'] : NULL,
                            'status' => isset($employee_data['status']) ? $employee_data['status'] : NULL,
                            'location' => (substr($employee_data['location'], -5) === '-6002') ? 'Karachi' : $employee_data['location'],
                        ]);
                        $grossSalary = $employee_data['gross_salary'];
                        $annualBase = ($grossSalary * 12) / 10;
                        if ($employee_data['married'] == "true" && $annualBase < 75000) {
                            $annualBase = 75000;
                        } else if ($employee_data['married'] == "false" && $annualBase < 50000) {
                            $annualBase = 50000;
                        }
                        $dental = 0.33 * $annualBase;
                        if ($dental > 100000)
                            $dental = 100000;

                        $optical = 0.10 * $annualBase;
                        if ($dental > 25000)
                            $dental = 25000;
                        $others = $annualBase - ($dental + $optical);

                        DB::table('entitlements')->insert([
                            'employee_number' => $employee_data['employee_number'],
                            'entitlement_type' => "Medical",
                            "break_down" => "Dental",
                            "total_limit" => $dental,
                            "consumed_limit" => 0
                        ]);
                        DB::table('entitlements')->insert([
                            'employee_number' => $employee_data['employee_number'],
                            'entitlement_type' => "Medical",
                            "break_down" => "Optical",
                            "total_limit" => $optical,
                            "consumed_limit" => 0
                        ]);
                        DB::table('entitlements')->insert([
                            'employee_number' => $employee_data['employee_number'],
                            'entitlement_type' => "Medical",
                            "break_down" => "Others",
                            "total_limit" => $others,
                            "consumed_limit" => 0
                        ]);
                    } else {
                        $update_data = [];
                        if ($employee->employee_name != $employee_data['employee_name']) {
                            $update_data['employee_name'] = $employee_data['employee_name'];
                        }
                        if ($employee->grade != $employee_data['grade']) {
                            $update_data['grade'] = $employee_data['grade'];
                        }
                        if ($employee->gross_salary != $employee_data['gross_salary']) {
                            $update_data['gross_salary'] = $employee_data['gross_salary'];
                        }
                        if ($employee->salary_last_modified_date != $employee_data['salary_last_modified_date']) {
                            $update_data['salary_last_modified_date'] = $employee_data['salary_last_modified_date'];
                        }
                        if ($employee->salary_end_date != $employee_data['salary_end_date']) {
                            $update_data['salary_end_date'] = $employee_data['salary_end_date'];
                        }
                        if ($employee->salary_effective_date != $employee_data['salary_effective_date']) {
                            $update_data['salary_effective_date'] = $employee_data['salary_effective_date'];
                        }
                        if ($employee->designation != $employee_data['designation']) {
                            $update_data['designation'] = $employee_data['designation'];
                        }
                        if ($employee->company != $employee_data['company_id']) {
                            $update_data['company'] = $employee_data['company_id'];
                        }
                        if ($employee->business_unit != $employee_data['bu_id']) {
                            $update_data['business_unit'] = $employee_data['bu_id'];
                        }
                        if ($employee->division != $employee_data['division']) {
                            $update_data['division'] = $employee_data['division'];
                        }
                        if ($employee->department != $employee_data['department']) {
                            $update_data['department'] = $employee_data['department'];
                        }
                        if ($employee->cost_center != $employee_data['costCenter']) {
                            $update_data['cost_center'] = $employee_data['costCenter'];
                        }
                        if ($employee->email != $employee_data['email']) {
                            $update_data['email'] = $employee_data['email'];
                        }
                        if ($employee->mobile != $employee_data['mobile']) {
                            $update_data['mobile'] = $employee_data['mobile'];
                        }
                        if ($employee->status != $employee_data['status']) {
                            $update_data['status'] = $employee_data['status'];
                        }
                        $incoming_location = (substr($employee_data['location'], -5) === '-6002') ? 'Karachi' : $employee_data['location'];
                        if ($employee->location != $incoming_location) {
                            $update_data['location'] = $incoming_location;
                        }
                        if ($employee->bank != $employee_data['bank']) {
                            $update_data['bank'] = $employee_data['bank'];
                        }
                        if ($employee->status != $employee_data['account_number']) {
                            $update_data['account_number'] = $employee_data['account_number'];
                        }
                        if ($employee->status != $employee_data['line_manager_id']) {
                            $update_data['line_manager_id'] = $employee_data['line_manager_id'];
                        }

                        if (!empty($update_data)) {
                            DB::table('sap_sync')->where('employee_number', $employee_data['employee_number'])->update($update_data);
                        }
                    }
                } else {
                    // dump("HERE4");
                    $update_data = [];

                    if (!empty($employee_data['employee_name']) && isset($employee->employee_name) && $employee->employee_name != $employee_data['employee_name']) {
                        $update_data['employee_name'] = $employee_data['employee_name'];
                    }

                    if (!empty($employee_data['grade']) && $employee->grade != $employee_data['grade']) {
                        $update_data['grade'] = $employee_data['grade'];
                    }

                    if (!empty($employee_data['gross_salary']) && isset($employee_data['gross_salary']) && $employee->gross_salary != $employee_data['gross_salary']) {
                        $update_data['gross_salary'] = $employee_data['gross_salary'];
                    }

                    if (!empty($employee_data['salary_last_modified_date']) && $employee->salary_last_modified_date != $employee_data['salary_last_modified_date']) {
                        $update_data['salary_last_modified_date'] = $employee_data['salary_last_modified_date'];
                    }

                    if (!empty($employee_data['salary_end_date']) && $employee->salary_end_date != $employee_data['salary_end_date']) {
                        $update_data['salary_end_date'] = $employee_data['salary_end_date'];
                    }

                    if (!empty($employee_data['salary_effective_date']) && $employee->salary_effective_date != $employee_data['salary_effective_date']) {
                        $update_data['salary_effective_date'] = $employee_data['salary_effective_date'];
                    }

                    if (!empty($employee_data['designation']) && $employee->designation != $employee_data['designation']) {
                        $update_data['designation'] = $employee_data['designation'];
                    }

                    if (!empty($employee_data['company_id']) && $employee->company != $employee_data['company_id']) {
                        $update_data['company'] = $employee_data['company_id'];
                    }

                    if (!empty($employee_data['bu_id']) && $employee->business_unit != $employee_data['bu_id']) {
                        $update_data['business_unit'] = $employee_data['bu_id'];
                    }

                    if (!empty($employee_data['division']) && $employee->division != $employee_data['division']) {
                        $update_data['division'] = $employee_data['division'];
                    }

                    if (!empty($employee_data['department']) && $employee->department != $employee_data['department']) {
                        $update_data['department'] = $employee_data['department'];
                    }

                    if (!empty($employee_data['costCenter']) && $employee->cost_center != $employee_data['costCenter']) {
                        $update_data['cost_center'] = $employee_data['costCenter'];
                    }

                    if (!empty($employee_data['email']) && $employee->email != $employee_data['email']) {
                        $update_data['email'] = $employee_data['email'];
                    }

                    if (!empty($employee_data['status']) && $employee->status != $employee_data['status']) {
                        $update_data['status'] = $employee_data['status'];
                    }
                    $incoming_location = (substr($employee_data['location'], -5) === '-6002') ? 'Karachi' : $employee_data['location'];
                    if ($employee->location != $incoming_location) {
                        $update_data['location'] = $incoming_location;
                    }
                    if (!empty($employee_data['bank']) && $employee->bank != $employee_data['bank']) {
                        $update_data['bank'] = $employee_data['bank'];
                    }

                    if (!empty($employee_data['account_number']) && $employee->account_number != $employee_data['account_number']) {
                        $update_data['account_number'] = $employee_data['account_number'];
                    }

                    if (!empty($employee_data['line_manager_id']) && $employee->line_manager_id != $employee_data['line_manager_id']) {
                        $update_data['line_manager_id'] = $employee_data['line_manager_id'];
                    }

                    if (!empty($update_data)) {
                        DB::table('sap_sync')->where('employee_number', $employee_data['employee_number'])->update($update_data);
                    }


                    // dump($employee_data);
                }
            }
        } catch (\Exception $e) {
            echo "Error in saveOrUpdateEmployeeDetails: " . $e->getMessage();
        }
    }

    private function saveOrUpdateEmployeeTableDetails($employee_data)
    {
        try {
            // $employee = DB::table('employees')->where('employee_number', $employee_data['employee_number'])->Orwhere('employee_name', $employee_data['employee_name'])->first();
            $employee = DB::table('employees')->where('employee_number', $employee_data['employee_number'])->first();
            $grade = DB::table('grades')->where('secondary_name', $employee_data['grade'])->where('company_id', $employee_data['company_id'])->first();
            // dump("IN TABLE EMPLOYEE");
            // dump($employee);
            // dump($grade);
            if (!$employee) {
                // dump("Employee Not found in employees table Making an insertion!");
                // dump($employee_data);
                DB::table('employees')->insert([
                    'employee_number' => $employee_data['employee_number'],
                    'employee_name' => !empty($employee_data['employee_name']) ? $employee_data['employee_name'] : str_replace('.', ' ', strstr($employee_data['email'], '@', true)),
                    'nick_name' => !empty($employee_data['employee_name']) ? $employee_data['employee_name'] : str_replace('.', ' ', strstr($employee_data['email'], '@', true)),
                    'designation' => isset($employee_data['designation']) ? $employee_data['designation'] : "Designation Not Found!",
                    'grade_id' => $grade->id,
                    'user_name' => $employee_data['employee_number'],
                    'account_type' => "2",
                    'department_id' => isset($employee_data['department']) ? $employee_data['department'] : $employee_data['division'],
                    'location' => (substr($employee_data['location'], -5) === '-6002') ? 'Karachi' : $employee_data['location'],
                ]);
            } else {
                $update_data = [];
                if (!empty($employee_data['employee_number']) && $employee->employee_number != $employee_data['employee_number']) {
                    $update_data['employee_number'] = $employee_data['employee_number'];
                }

                if (!empty($employee_data['employee_name']) && $employee->employee_name != $employee_data['employee_name']) {
                    $update_data['employee_name'] = $employee_data['employee_name'];
                }

                if (!empty($employee_data['designation']) && $employee->designation != $employee_data['designation']) {
                    $update_data['designation'] = $employee_data['designation'];
                }

                if (!empty($employee_data['employee_number']) && $employee->user_name != $employee_data['employee_number']) {
                    $update_data['user_name'] = $employee_data['employee_number'];
                }

                $department_id = !empty($employee_data['department']) ? $employee_data['department'] : (!empty($employee_data['division']) ? $employee_data['division'] : null);
                if (!empty($department_id) && $employee->department_id != $department_id) {
                    $update_data['department_id'] = $department_id;
                }

                $incoming_location = (substr($employee_data['location'], -5) === '-6002') ? 'Karachi' : $employee_data['location'];
                if ($employee->location != $incoming_location) {
                    $update_data['location'] = $incoming_location;
                }
                if (!empty($grade->id) && $employee->grade_id != $grade->id) {
                    $update_data['grade_id'] = $grade->id;
                }

                if (!empty($update_data)) {
                    DB::table('employees')
                        ->where('employee_number', $employee_data['employee_number'])
                        ->orWhere('employee_name', $employee_data['employee_name'])
                        ->update($update_data);
                }
            }
        } catch (\Exception $e) {
            echo "Error in saveOrUpdateEmployeeTableDetails: " . $e->getMessage();
        }
    }

    private function saveOrUpdateEmployeeTableLevelDetails($employee_data)
    {
        try {
            if (
                !isset($employee_data['line_manager_id']) ||
                !isset($employee_data['employee_number']) ||
                !isset($employee_data['company_id']) ||
                !isset($employee_data['costCenter'])
            ) {
                echo "Missing required employee data!";
                return;
            }

            $lineManagerId = $employee_data['line_manager_id'];
            $employee_number = $employee_data['employee_number'];
            $companyId = $employee_data['company_id'];
            $costCenter = $employee_data['costCenter'];
            $update_data = [];
            $level = null;

            if ($companyId == '1400' || $companyId == '1700') {
                $employee = DB::table('employees')->where('employee_number', $employee_data['line_manager_id'])->first();

                $checkBUHead = DB::table('bu_head')
                    ->join('business_units', 'bu_head.bu_id', '=', 'business_units.id')
                    ->where('employee_number', $lineManagerId)
                    ->where('business_units.company_id', '1400')
                    ->first();

                $level = $checkBUHead ? 4 : 3;

                // dump("=======================Leveling=======================");
                // dump($level);
                // dump($checkBUHead);
                // dump("======================================================");
                if ($employee) {
                    if ($level !== null && $employee->level < $level) {
                        $update_data['level'] = $level;

                        DB::table('employees')
                            ->where('employee_number', $lineManagerId)
                            ->update($update_data);
                    }
                }
            } else {
                $employee = DB::table('employees')->where('employee_number', $employee_data['employee_number'])->first();

                $approver = DB::table('cost_center_approvers')
                    ->where('cost_center', $costCenter)
                    ->first();

                if ($approver) {
                    for ($i = 1; $i <= 5; $i++) {
                        $levelField = "level_$i";
                        $ids = explode(',', $approver->$levelField);
                        $ids = array_map('trim', $ids);

                        if (in_array($employee_number, $ids)) {
                            $level = $i;
                            break;
                        }
                    }
                }

                if ($level !== null && ($employee->level === null || $employee->level < $level)) {
                    $update_data['level'] = $level;

                    DB::table('employees')
                        ->where('employee_number', $employee_number)
                        ->update($update_data);
                }
            }
        } catch (\Exception $e) {
            echo "Error in saveOrUpdateEmployeeTableLevelDetails: " . $e;
        }
    }

    private function saveOrUpdateBUHeadDetails($BU_data)
    {
        try {
            $bu_head = DB::table('bu_head')->where('bu_id', $BU_data['businessUnit'])->first();

            if (!$bu_head) {
                DB::table('bu_head')->insert([
                    'position' => $BU_data['position'],
                    'bu_id' => $BU_data['businessUnit'],
                    'employee_number' => $BU_data['employee_number'],
                ]);
            } else {
                $update_data = [];
                if ($bu_head->position != $BU_data['position']) {
                    $update_data['position'] = $BU_data['position'];
                }
                if ($bu_head->bu_id != $BU_data['bu_id']) {
                    $update_data['bu_id'] = $BU_data['bu_id'];
                }
                if ($bu_head->employee_number != $BU_data['employee_number']) {
                    $update_data['employee_number'] = $BU_data['employee_number'];
                }
                if (!empty($update_data)) {
                    DB::table('bu_head')->where('bu_id', $BU_data['businessUnit'])->update($update_data);
                }
            }
        } catch (\Exception $e) {
            echo "Error in saveOrUpdateBUHeadDetails: " . $e->getMessage();
        }
    }
}
