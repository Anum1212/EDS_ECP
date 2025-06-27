<?php namespace App\Http\Controllers;

use App\Email_Client;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;

class MailController extends Controller {

	public function send($data, $cc, $view, $subject){
	    
		if($cc == null){
		    try{
                Mail::send('emails.'.$view, $data, function ($message) use ($data, $subject) {
                    $message->from('systems.services@packages.com.pk', 'E C P');
                    $message->to($data['client']->email, $data['client']->employee_name)
                        ->subject($subject);;
                });
            }
            catch(\Exception $e){
		        Session::flash('error', 'Emails are not maintained in SF. Please inform your HRBP');
		        Log::info('------------ Email Communication Error Start ------------');
		        Log::info('Error Code: '.$e->getCode());
		        Log::info('Error Description: '.$e->getMessage());
                Log::info('------------ Email Communication Error End ------------');
            }
		}
		else{
			if(isset($data['employee']->email)){
			    try{
                    Mail::send('emails.'.$view, $data, function ($message) use ($data, $subject) {
                        $message->from('systems.services@packages.com.pk', 'E C P');
                        $message->to($data['client']->email, $data['client']->employee_name)
                            ->cc($data['employee']->email, $data['employee']->employee_name)
                            ->subject($subject);
                    });
                }
                catch(\Exception $e){
                    Session::flash('error', 'Emails are not maintained in SF. Please inform your HRBP');
                    Log::info('------------ Email Communication Error Start ------------');
                    Log::info('Error Code: '.$e->getCode());
                    Log::info('Error Description: '.$e->getMessage());
                    Log::info('------------ Email Communication Error End ------------');
                }
			}
			else{
			    try{
                    Mail::send('emails.'.$view, $data, function ($message) use ($data, $subject) {
                        $message->from('systems.services@packages.com.pk', 'E C P');
                        $message->to($data['client']->email, $data['client']->employee_name)
                            ->subject($subject);
                    });
                }
                catch(\Exception $e) {
                    Session::flash('error', 'Emails are not maintained in SF. Please inform your HRBP');
                    Log::info('------------ Email Communication Error Start ------------');
                    Log::info('Error Code: '.$e->getCode());
                    Log::info('Error Description: '.$e->getMessage());
                    Log::info('------------ Email Communication Error End ------------');
                }
			}
		}
		/*if($cc == null){
			Mail::send('emails.'.$view, $data, function ($message) use ($data, $subject) {
				$message->from('systems.services@packages.com.pk', 'Employee Claims Portal');
				$message->to('nauman.abid@packages.com.pk', $data['client']->employee_name)->subject($subject);
			});
		}
		else{
			Mail::send('emails.'.$view, $data, function ($message) use ($data, $subject) {
				$message->from('systems.services@packages.com.pk', 'Employee Claims Portal');
				$message->to('nauman.abid@packages.com.pk', $data['client']->employee_name)
					->cc('nauman.abid@packags.com.pk', $data['employee']->employee_name)
					->subject($subject);
			});
		}*/
	}

	public function sendMailApi(Request $request){
		$portalName = $request->input('portal_name');
		$portalToken = $request->input('portal_token');
		$emailClient = Email_Client::where('portal_name', '=', $portalName)->first();
		if(isset($emailClient)){
			$passCode = $emailClient->portal_code."   ";
			$date	= md5(date("Ymd"));
			$token = md5($passCode).$date;
			if($token == $portalToken){
				$data['greeting'] = $request->input('greeting');
				$data['body'] = $request->input('body');
				$data['headerText'] = $request->input('headerText');
				$data['subject'] = $request->input('subject');
				$data['fromName'] = $request->input('fromName');
				$data['to'] = $request->input('to');
				$data['headerColor'] = $request->input('headerColor');
				if($request->input('cc') == 'null'){
					Mail::send('emails.mailing-api', $data, function ($message) use ($data) {
						$message->from('systems.services@packages.com.pk', $data['fromName']);
						$message->to($data['to'], $data['greeting'])
							->subject($data['subject']);;
					});
				}
				else{
					$data['cc'] = $request->input('cc');
					Mail::send('emails.mailing-api', $data, function ($message) use ($data) {
						$message->from('systems.services@packages.com.pk', $data['fromName']);
						$message->to($data['to'], $data['greeting'])
							->cc($data['cc'])
							->subject($data['subject']);
					});
				}
				return 'Email Sent Successfuly';
			}
			else{
				return 'Not Allowed';
			}
		}
		else{
			return 'Not Allowed';
		}
	}
}
