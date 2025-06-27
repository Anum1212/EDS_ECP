<?php namespace App\Console;

use App\Employee;
use App\Http\Controllers\MailController;
use App\Voucher;
use App\Voucher_Employee;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\DB;

class Kernel extends ConsoleKernel {

	/**
	 * The Artisan commands provided by your application.
	 *
	 * @var array
	 */
	protected $commands = [
		'App\Console\Commands\Inspire',
	];

	/**
	 * Define the application's command schedule.
	 *
	 * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
	 * @return void
	 */
	protected function schedule(Schedule $schedule)
	{
		$schedule->call(function(){
		    
        
			$vouchers = Voucher::with('voucherStatus')->where('status', '=', 'Approved - Documents in transit')->get();
		
			$data['employee'] = Employee::where('user_name', '=', 'ers-admin')->first();
			foreach($vouchers as $voucher){
				$difference = date_diff(date_create(date('Y-m-d')), date_create($voucher->voucherStatus[count($voucher->voucherStatus)-1]->pivot->updated_at))->format('%a');
		
				if($difference > 30){
			
					DB::beginTransaction();
					$voucher->status = 'Rejected';
					$voucher->save();
					$voucherStatus = new Voucher_Employee();
					$voucherStatus->voucher_id = $voucher->id;
					$voucherStatus->employee_id = $data['employee']->id;
					$voucherStatus->approved = 0;
					$voucherStatus->comments = 'Auto rejection - Documents not received within 30 days';
					$voucherStatus->save();
					DB::commit();
                    $data['voucher'] = $voucher;
                    $data['client'] = Employee::find($voucher->employee->id);
                    $data['rejectionComments'] = 'Auto rejection - Documents not received within 30 days';
                    dump($data);
                    $mail = new MailController();
                    $mail->send($data,$data,'voucher-rejected', 'Voucher #'.$voucher->id.' Rejected');
				}
			}

            $vouchers = Voucher::with('voucherStatus')->where('status', '=', 'Submitted')->get();
            $data['employee'] = Employee::where('user_name', '=', 'ers-admin')->first();
            foreach($vouchers as $voucher){
               
                $difference = date_diff(date_create(date('Y-m-d')), date_create($voucher->voucherStatus[count($voucher->voucherStatus)-1]->pivot->created_at))->format('%a');
                
                if($difference > 30 && !isset($voucher->voucherStatus[count($voucher->voucherStatus)-1]->pivot->approved)){
                    DB::beginTransaction();
                    $voucher->status = 'Rejected';
                    $voucher->save();
                    $voucherStatus = new Voucher_Employee();
                    $voucherStatus->voucher_id = $voucher->id;
                    $voucherStatus->employee_id = $data['employee']->id;
                    $voucherStatus->approved = 0;
                    $voucherStatus->comments = 'Auto rejection - Voucher Not Approved within 30 days';
                    $voucherStatus->save();
                    $prev_voucherStatus = Voucher_Employee::where('voucher_id', $voucher->id)->first();
                    $prev_voucherStatus->approved = 0;
                    $prev_voucherStatus->save();
                    DB::commit();
                    $data['voucher'] = $voucher;
                    $data['client'] = Employee::find($voucher->employee->id);
                    $data['rejectionComments'] = 'Auto rejection - Voucher Not Approved within 30 days';
                    $mail = new MailController();
                    $mail->send($data,$data,'voucher-rejected', 'Voucher #'.$voucher->id.' Rejected');
                }

                if($difference == 30 && !isset($voucher->voucherStatus[count($voucher->voucherStatus)-1]->pivot->approved)){
                    $data['voucher'] = $voucher;
                    $data['client'] = Employee::find($voucher->voucherStatus[count($voucher->voucherStatus)-1]->pivot->employee_id);
                    $data['voucher_employee'] = Employee::find($voucher->employee->id);
                    $data['rejectionComments'] = 'Auto Reminder - Voucher Not Approved within 30 days';
               
                    $mail = new MailController();
                    $mail->send($data,NULL,'voucher-reminder', 'Voucher #'.$voucher->id.' Reminder');
                }
                if($difference == 20 && !isset($voucher->voucherStatus[count($voucher->voucherStatus)-1]->pivot->approved)){
                    $data['voucher'] = $voucher;
                    $data['client'] = Employee::find($voucher->voucherStatus[count($voucher->voucherStatus)-1]->pivot->employee_id);
                    $data['voucher_employee'] = Employee::find($voucher->employee->id);
                    $data['rejectionComments'] = 'Auto Reminder - Voucher Not Approved within 20 days';
                  
                    $mail = new MailController();
                    $mail->send($data,NULL,'voucher-reminder', 'Voucher #'.$voucher->id.' Reminder');
                }
                if($difference == 10 && !isset($voucher->voucherStatus[count($voucher->voucherStatus)-1]->pivot->approved)){
                    $data['voucher'] = $voucher;
                    $data['client'] = Employee::find($voucher->voucherStatus[count($voucher->voucherStatus)-1]->pivot->employee_id);
                    $data['voucher_employee'] = Employee::find($voucher->employee->id);
                    $data['rejectionComments'] = 'Auto Reminder - Voucher Not Approved within 10 days';
                  
                    $mail = new MailController();
                    $mail->send($data,NULL,'voucher-reminder', 'Voucher #'.$voucher->id.' Reminder');
                }
            }

			/*$vouchers = Voucher::with('voucherStatus')->where('status', '=', 'Approved - Documents in transit')->get();
		//	dump($vouchers);
			$data['employee'] = Employee::where('user_name', '=', 'ers-admin')->first();
			foreach($vouchers as $voucher){
				$difference = date_diff(date_create(date('Y-m-d')), date_create($voucher->voucherStatus[count($voucher->voucherStatus)-1]->pivot->updated_at))->format('%a');
				dump($difference);
				//dump($voucher->voucherStatus[count($voucher->voucherStatus)-1]->pivot->approved);
				if($difference > 30 && $voucher->voucherStatus[count($voucher->voucherStatus)-1]->pivot->approved){
				    dump("4");
					DB::beginTransaction();
					$voucher->status = 'Rejected';
					$voucher->save();
					$voucherStatus = new Voucher_Employee();
					$voucherStatus->voucher_id = $voucher->id;
					$voucherStatus->employee_id = $data['employee']->id;
					$voucherStatus->approved = 0;
					$voucherStatus->comments = 'Auto rejection - Documents not received within 30 days';
					$voucherStatus->save();
					//DB::commit();
                    $data['voucher'] = $voucher;
                    $data['client'] = Employee::find($voucher->employee->id);
                    $data['rejectionComments'] = 'Auto rejection - Documents not received within 30 days';
                    $mail = new MailController();
                    $mail->send($data,$data,'voucher-rejected', 'Voucher #'.$voucher->id.' Rejected');
				}
			}

            $vouchers = Voucher::with('voucherStatus')->where('status', '=', 'Submitted')->get();
            $data['employee'] = Employee::where('user_name', '=', 'ers-admin')->first();
            foreach($vouchers as $voucher){
                $difference = date_diff(date_create(date('Y-m-d')), date_create($voucher->voucherStatus[count($voucher->voucherStatus)-1]->pivot->created_at))->format('%a');
                if($difference > 30 && !isset($voucher->voucherStatus[count($voucher->voucherStatus)-1]->pivot->approved)){
                    DB::beginTransaction();
                    $voucher->status = 'Rejected';
                    $voucher->save();
                    $voucherStatus = new Voucher_Employee();
                    $voucherStatus->voucher_id = $voucher->id;
                    $voucherStatus->employee_id = $data['employee']->id;
                    $voucherStatus->approved = 0;
                    $voucherStatus->comments = 'Auto rejection - Voucher Not Approved within 30 days';
                    $voucherStatus->save();
                    DB::commit();
                    $data['voucher'] = $voucher;
                    $data['client'] = Employee::find($voucher->employee->id);
                    $data['rejectionComments'] = 'Auto rejection - Voucher Not Approved within 30 days';
                    $mail = new MailController();
                    $mail->send($data,$data,'voucher-rejected', 'Voucher #'.$voucher->id.' Rejected');
                }

                if($difference == 30 && !isset($voucher->voucherStatus[count($voucher->voucherStatus)-1]->pivot->approved)){
                    $data['voucher'] = $voucher;
                    $data['client'] = Employee::find($voucher->voucherStatus[count($voucher->voucherStatus)-1]->pivot->employee_id);
                    $data['voucher_employee'] = Employee::find($voucher->employee->id);
                    $data['rejectionComments'] = 'Auto Reminder - Voucher Not Approved within 30 days';
//                    dump("1");
                    $mail = new MailController();
                    $mail->send($data,NULL,'voucher-reminder', 'Voucher #'.$voucher->id.' Reminder');
                }
                if($difference == 20 && !isset($voucher->voucherStatus[count($voucher->voucherStatus)-1]->pivot->approved)){
                    $data['voucher'] = $voucher;
                    $data['client'] = Employee::find($voucher->voucherStatus[count($voucher->voucherStatus)-1]->pivot->employee_id);
                    $data['voucher_employee'] = Employee::find($voucher->employee->id);
                    $data['rejectionComments'] = 'Auto Reminder - Voucher Not Approved within 20 days';
//                    dump("2");
                    $mail = new MailController();
                    $mail->send($data,NULL,'voucher-reminder', 'Voucher #'.$voucher->id.' Reminder');
                }
                if($difference == 10 && !isset($voucher->voucherStatus[count($voucher->voucherStatus)-1]->pivot->approved)){
                    $data['voucher'] = $voucher;
                    $data['client'] = Employee::find($voucher->voucherStatus[count($voucher->voucherStatus)-1]->pivot->employee_id);
                    $data['voucher_employee'] = Employee::find($voucher->employee->id);
                    $data['rejectionComments'] = 'Auto Reminder - Voucher Not Approved within 10 days';
                    dump($data);
                    $mail = new MailController();
                    $mail->send($data,NULL,'voucher-reminder', 'Voucher #'.$voucher->id.' Reminder');
                }
//                dump($difference);
            }*/
            
            
		});
	}

}
