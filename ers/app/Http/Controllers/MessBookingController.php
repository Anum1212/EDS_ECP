<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\SAP_Sync;
use App\Employee;
use App\Department;
use App\Mess_Booking;
use App\Mess_Booking_Type;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;

class MessBookingController extends Controller
{
	// mess bookings list for employee
	public function showMessBookings($type = null)
	{
		$employee_id = Session::get('id');

		if (isset($employee_id)) {
			$data['path'] = Route::getFacadeRoot()->current()->uri();
			$employee = Session::get('emp_details');

			// Handle if employee is not found
			if (!$employee) {
				Session::flash('error', 'Employee record not found for your session.');
				return redirect('/');
			}

			$data['employee'] = $employee; // Pass the employee model to the view
			$data['panelHeading'] = 'Mess Bookings';
			$data['forwardingURL'] = 'mess-booking/details/';

			if ($type == 'approved') {
				$data['messBookings'] = Mess_Booking::where('status', 'Approved')->get();
				$data['panelHeading'] = 'Approved Y-Lunch'; // Update heading
			} elseif ($type == 'unapproved' || $type == 'pending') {
				$data['messBookings'] = Mess_Booking::where('status', 'Submitted')->get();
				$data['panelHeading'] = 'Unapproved Y-Lunch'; 
				if ($type == 'pending') {
					$data['panelHeading'] = 'Pending Y-Lunch'; // Update heading
				}
			} elseif ($type == 'rejected') {
				$data['messBookings'] = Mess_Booking::where('status', 'Rejected')->get();
				$data['panelHeading'] = 'Rejected Y-Lunch'; // Update heading
			}

			$data['userType'] = 'employee';

			// Add counter to each booking (useful for display in a table)
			$counter = 1;
			foreach ($data['messBookings'] as $messBooking) {
				$messBooking->counter = $counter;
				$counter++;
			}

			return view('basic.mess-bookings', $data);
		} else {
			Session::flash('error', 'Your session has ended. Please login to continue.');
			return redirect('/');
		}
	}

	// individual mess booking details
	public function showMessBookingDetails($id)
	{
		$data['employee'] = Session::get('emp_details');
		if (isset($data['employee']->id)) {
			$data['layout'] = 'ers-layout';
			$data['path'] = Route::getFacadeRoot()->current()->uri();
			$data['employee_sf_data'] = Session::get('sf_details');
			if ($data['employee_sf_data']->line_manager_id == null) {
				Session::flash('error', 'You do not have a Line Manager assigned in. Please contact HR.');
				return redirect('dashboard');
			} elseif (!$data['employee']->entitlements->contains('entitlement_type', 'Y-Lunch')) {
				Session::flash('error', 'You are not entitled for Y-Lunch. Please contact HR.');
				return redirect('dashboard');
			}

			$data['departments'] = Department::whereHas('businessUnit.company', function ($query) use ($data) {
				$query->where('companies.id', '=', $data['employee']->department->businessUnit->company->id);
			})->get();
			$data['panelHeading'] = 'Mess Booking Details';
			$data['submitURL'] = 'mess-booking/submit';
			$data['deleteURL'] = 'mess-booking/delete';
			$data['approveURL'] = 'mess-booking/approve';
			$data['rejectURL'] = 'mess-booking/reject';
			$data['messBooking'] = Mess_Booking::find($id);
			$data['messBookingType'] = Mess_Booking_Type::find($data['messBooking']->mess_booking_type_id)->first()->name;
			if ($data['messBooking']->employee_id == $data['employee']->id) {
				$data['userType'] = 'employee';
			} else {
				$data['userType'] = 'approver';
			}

			return view('basic.mess-booking-details', $data);
		}
	}

	// mess bookings list for approver
	public function showApproverMessBookings()
	{
		$employee_id = Session::get('id');
		if (isset($employee_id)) {
			$data['path'] = Route::getFacadeRoot()->current()->uri();
			$data['employee'] = Employee::find($employee_id);
			$data['panelHeading'] = 'Mess Bookings';
			$data['forwardingURL'] = 'mess-booking/details/';
			$data['generateReport'] = 'mess-booking/report';
			$data['messBookings'] = Mess_Booking::where('approver_id', $employee_id)->get();
			$data['userType'] = 'approver';
			$counter = 1;
			foreach ($data['messBookings'] as $messBooking) {
				$messBooking->counter = $counter;
				$counter++;
			}
			$data['messBookings_count'] = $data['messBookings']->count();
			return view('basic.mess-bookings', $data);
		} else {
			Session::flash('error', 'Your session has ended. Please login to continue.');
			return redirect('/');
		}
	}

	// show mess booking request form
	public function showMessBookingForm()
	{
		$data['employee'] = Session::get('emp_details');
		if (isset($data['employee']->id)) {
			$data['path'] = Route::getFacadeRoot()->current()->uri();
			$data['employee_sf_data'] = Session::get('sf_details');
			if ($data['employee_sf_data']->line_manager_id == null) {
				Session::flash('error', 'You do not have a Line Manager assigned. Please contact HR.');
				return redirect('dashboard');
			} elseif (!$data['employee']->entitlements->contains('entitlement_type', 'Y-Lunch')) {
				Session::flash('error', 'You are not entitled for Y-Lunch. Please contact HR.');
				return redirect('dashboard');
			}
			$data['departments'] = Department::whereHas('businessUnit.company', function ($query) use ($data) {
				$query->where('companies.id', '=', $data['employee']->department->businessUnit->company->id);
			})->get();
			$data['panelHeading'] = 'New Mess Booking';
			$data['storingURL'] = 'mess-booking/create';
			$data['messBookingTypes'] = Mess_Booking_Type::all();
			return view('basic.add-mess-booking', $data);
		}
	}

	// insert mess booking request form data as draft
	public function createMessBooking(Request $request)
	{
		$data['path'] = Route::getFacadeRoot()->current()->uri();
		$data['employee'] = Session::get('emp_details');
		$data['employee_sf_data'] = Session::get('sf_details');
		$data['employee_sync'] = SAP_Sync::where('employee_number', $data['employee']->employee_number)->first();
		$BookingName = $request->input('booking_name');
		$BSPEmployeeHeadCount = $request->input('BSPEmployeeHeadCount', 0);
		$guestHeadCount = $request->input('GuestHeadCount', 0);
		if ($BSPEmployeeHeadCount == 0 && $guestHeadCount == 0) {
			Session::flash('error', 'Total Head Count cannot be zero');
			return redirect('mess-booking');
		}
		$BookingDateStart = $request->input('BookingDateStart');
		$BookingDateEnd = $request->input('BookingDateEnd');
		$BookingDateEnd = $request->input('BookingDateEnd');
		$BookingTime = $request->input('BookingTime');
		$FoodType = $request->input('FoodType');
		if ($FoodType == 0 || $FoodType == null) {
			Session::flash('error', 'Please select a Booking Type');
			return redirect('mess-booking');
		}
		DB::beginTransaction();
		$messBooking = new Mess_Booking();
		$messBooking->employee_id = $data['employee']->id;
		$messBooking->bsp_employee_count = $BSPEmployeeHeadCount;
		$messBooking->guest_count = $guestHeadCount;
		$messBooking->total_head_count = $BSPEmployeeHeadCount + $guestHeadCount;
		$messBooking->mess_booking_type_id = $FoodType;
		$messBooking->booking_start_date = $BookingDateStart;
		$messBooking->booking_end_date = $BookingDateEnd;
		$messBooking->booking_time = $BookingTime;
		$messBooking->booking_name = $BookingName;
		$messBooking->status = "Draft";
		$messBooking->save();
		$messBooking->approver_id = $data['employee_sync']->line_manager_id;
		DB::commit();
		Session::flash('success', 'Your claim is saved');
		return redirect('mess-booking/details' . '/' . $messBooking->id);
	}

	// update mess booking record from draft to submitted
	public function submitMessBooking($id)
	{
		$data['employee'] = Session::get('emp_details');
		if (isset($data['employee']->id)) {
			$data['employee_sf_data'] = Session::get('sf_details');
			if ($data['employee_sf_data']->line_manager_id == null) {
				Session::flash('error', 'You do not have a Line Manager assigned. Please contact HR.');
				return redirect('dashboard');
			}
			$messBooking = Mess_Booking::find($id);
			if ($messBooking->employee_id == $data['employee']->id && $messBooking->status == "Draft") {
				$data['path'] = Route::getFacadeRoot()->current()->uri();
				$data['panelHeading'] = 'Mess Booking';
				DB::beginTransaction();
				$messBooking->status = "Submitted";
				$messBooking->approver_id = $data['employee_sf_data']->line_manager_id;
				$messBooking->save();
				DB::commit();

				$mail = new MailController();
				$mail->send($data, $data, 'y-lunch', $data['panelHeading'] . ' Approved');
				Session::flash('success', 'Submited Successfuly.');
				return redirect('dashboard');
			} else {
				Session::flash('error', 'You do not have the access to submit');
				return redirect('dashboard');
			}
		} else {
			Session::flash('error', 'Your session has ended. Please login to continue.');
			return redirect('/');
		}
	}

	// delete mess booking record for employee only when status is draft
	public function deleteMessBooking($id)
	{
		$data['employee'] = Session::get('emp_details');
		if (isset($data['employee']->id)) {
			$messBooking = Mess_Booking::find($id);
			if ($messBooking->employee_id == $data['employee']->id && $messBooking->status == "Draft") {
				$messBooking->delete();
				Session::flash('success', 'Deleted Successfuly.');
				return redirect('dashboard');
			} else {
				Session::flash('error', 'You do not have the access to delete');
				return redirect('dashboard');
			}
		} else {
			Session::flash('error', 'Your session has ended. Please login to continue.');
			return redirect('/');
		}
	}

	// approve mess booking record for approver only when status is submitted
	public function approveMessBooking($id)
	{
		$data['employee'] = Session::get('emp_details');
		if (isset($data['employee']->id)) {
			$messBooking = Mess_Booking::find($id);
			if (isset($data['employee']->id) && $data['employee']->emplioyee_number == $data['employee']->employee_number){
				if ($messBooking->status == "Submitted") {
					$messBooking->status = "Approved";
					DB::beginTransaction();
					Session::flash('success', 'Approved Successfuly.');
					return redirect('dashboard');
				} else {
					Session::flash('error', 'You do not have the access to Approve');
					return redirect('dashboard');
				}
			} else {
				Session::flash('error', 'Your session has ended. Please login to continue.');
				return redirect('/');
			}
		}
	}

	// reject mess booking record for approver only when status is submitted
	public function rejectMessBooking(Request $request, $id)
	{
		$data['employee'] = Session::get('emp_details');
		$messBooking = Mess_Booking::find($id);
		if (isset($data['employee']->id) && $data['employee']->emplioyee_number == $data['employee']->employee_number){
			if ($messBooking->status == "Submitted") {
				$messBooking->status = "Rejected";
				$messBooking->remarks = 'Rejected by ' . Employee::find($employee_id)->employee_name . ': ' . $request->input('rejection_comments');
				$messBooking->save();
				DB::beginTransaction();
				Session::flash('success', 'Rejected Successfuly.');
				return redirect('dashboard');
			} else {
				Session::flash('error', 'You do not have the access to Approve');
				return redirect('dashboard');
			}
		} else {
			Session::flash('error', 'Your session has ended. Please login to continue.');
			return redirect('/');
		}
	}

	public function generateReport(Request $request)
	{
		$employee_id = Session::get('id');
		if (isset($employee_id)) {
			$data['date_range'] = $request->input('dateRange');
			$date_range_split = $array = explode("-", $data['date_range']);
			$from_date = $date_range_split[0];
			$to_date = $date_range_split[1];
			$data['employee'] = Employee::find($employee_id);
			$data['panelHeading'] = 'Mess Booking Report ' . $data['date_range'];
			$data['messBookings'] = Mess_Booking::whereBetween('created_at', [$from_date, $to_date])->get();
			$counter = 1;
			foreach ($data['messBookings'] as $mess_booking) {
				$mess_booking->counter = $counter;
				$mess_booking->employee_name = Employee::find($mess_booking->employee_id)->employee_name;
				$mess_booking->employee_number = Employee::find($mess_booking->employee_id)->employee_number;
				$counter++;
			}
			return view('basic.mess-bookings-report', $data);
		} else {
			Session::flash('error', 'Your session has ended. Please login to continue.');
			return redirect('/');
		}
	}
}
