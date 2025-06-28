<?php

namespace App\Http\Controllers;

use App\SAP_Sync;
use App\Employee;
use App\Department;
use App\Mess_Booking;
use App\Mess_Booking_Type;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;

class MessBookingController extends Controller
{
	// mess bookings list for employee
	public function showMessBookings($userType, $listType = null)
	{
		$data['employee'] = Session::get('emp_details');

		if (isset($data['employee']->id)) {
			$data['path'] = Route::getFacadeRoot()->current()->uri();

			// Handle if employee is not found
			if (!$data['employee']) {
				Session::flash('error', 'Employee record not found for your session.');
				return redirect('/');
			}
			$data['employee_sf_data'] = Session::get('sf_details');
			$data['panelHeading'] = 'Y-Lunch';
			$data['forwardingURL'] = 'mess-booking/details/';

			if ($listType == 'approved') {
				$data['panelHeading'] = 'Approved Y-Lunch'; // Update heading
			} elseif ($listType == 'unapproved') {
				$data['panelHeading'] = 'UnApproved Y-Lunch'; // Update heading
			} elseif ($listType == 'pending') {
				$data['panelHeading'] = 'Pending Y-Lunch'; // Update heading
			} elseif ($listType == 'rejected') {
				$data['panelHeading'] = 'Rejected Y-Lunch'; // Update heading
			}
			if ($userType == 'employee') {
				$data['userType'] = 'employee';
				if ($listType == 'approved') {
					$data['messBookings'] = $data['employee']->approvedYLunch()->get();
				} elseif ($listType == 'unapproved') {
					$data['messBookings'] = $data['employee']->unapprovedYLunch()->get();
				} elseif ($listType == 'rejected') {
					$data['messBookings'] = $data['employee']->rejectedYLunch()->get();
				} else {
					$data['messBookings'] = $data['employee']->totalYLunch()->get();
				}
			} elseif ($userType == 'approver') {
				$data['userType'] = 'approver';
				if ($listType == 'approved') {
					$data['messBookings'] =  $data['employee']->approvedYLunchForApprover()->get();
				} elseif ($listType == 'pending') {
					$data['messBookings'] =  $data['employee']->unapprovedYLunchForApprover()->with(['employee'])->get();
				} elseif ($listType == 'rejected') {
					$data['messBookings'] =  $data['employee']->rejectedYLunchForApprover()->get();
				} else {
					$data['messBookings'] =  $data['employee']->totalYLunchForApprover()->get();
				}
				$data['generateReport'] = 'mess-booking/report'; // Enable report generation for approvers
			} else {
				Session::flash('error', 'Invalid user type.');
				return redirect('/');
			}

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
	public function showMessBookingDetails($id, $userType = null)
	{
		$data['employee'] = Session::get('emp_details');
		if (isset($data['employee']->id)) {
			$data['logged_in_employee_sf_data'] = Session::get('sf_details');

			$data['layout'] = 'ers-layout';
			$data['path'] = Route::getFacadeRoot()->current()->uri();
			$data['panelHeading'] = 'Y-Lunch Details';

			$data['messBooking'] = Mess_Booking::find($id);
			$data['messBookingType'] = Mess_Booking_Type::find($data['messBooking']->mess_booking_type_id)->first()->name;

			$data['userType'] = $userType;
			$data['initiator_employee_details'] = Employee::where('id', $data['messBooking']->employee_id)->first();
			$data['initiator_employee_sf_details'] = SAP_Sync::where('employee_number', $data['initiator_employee_details']->employee_number)->first();
			$data['departments'] = Department::whereHas('businessUnit.company', function ($query) use ($data) {
				$query->where('companies.id', '=', $data['initiator_employee_details']->department->businessUnit->company->id);
			})->get();

			if (!$data['initiator_employee_details']->entitlements->contains('entitlement_type', 'Y-Lunch')) {
				Session::flash('error', 'You are not entitled for Y-Lunch. Please contact HR.');
				return redirect('dashboard');
			}
			if ($userType == 'employee') {
				$data['submitURL'] = 'mess-booking/submit';
				$data['deleteURL'] = 'mess-booking/delete';
				$data['userType'] = 'employee';
			} elseif ($userType == 'approver') {
				$data['approveURL'] = 'mess-booking/approve';
				$data['rejectURL'] = 'mess-booking/reject';
				$data['userType'] = 'approver';
			}
			return view('basic.mess-booking-details', $data);
		}
	}


	// show mess booking request form
	public function showMessBookingForm()
	{
		$data['employee'] = Session::get('emp_details');
		if (isset($data['employee']->id)) {
			$data['photo'] = Session::get('photo');
			$data['photo_mimetype'] = Session::get('photo_mimetype');
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
			$data['panelHeading'] = 'New Y-Lunch';
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
		$messBooking->approver_emp_number = $data['employee_sync']->line_manager_id;
		DB::commit();
		Session::flash('success', 'Your claim is saved');
		return redirect('mess-booking/details' . '/' . $messBooking->id . '/employee');
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
				$data['panelHeading'] = 'Y-Lunch';
				DB::beginTransaction();
				$messBooking->status = "Submitted";
				$messBooking->approver_emp_number = $data['employee_sf_data']->line_manager_id;
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
			return redirect('showMessBookings/employee');
		}
	}

	// approve mess booking record for approver only when status is submitted
	public function approveMessBooking($id)
	{
		$employee = Session::get('emp_details');

		if (!isset($employee->id)) {
			Session::flash('error', 'Your session has ended. Please login to continue.');
			return redirect('/');
		}

		$messBooking = Mess_Booking::find($id);
		if (!$messBooking) {
			Session::flash('error', 'Y-Lunch not found.');
			return redirect('dashboard');
		}

		DB::beginTransaction();

		try {
			if ($messBooking->status == "Submitted") {
				$messBooking->status = "Approved";
				$messBooking->save();

				DB::commit();
				Session::flash('success', 'Approved Successfully.');
				return redirect('dashboard');
			} else {
				DB::rollBack();
				Session::flash('error', 'This booking cannot be approved as its status is not "Submitted".');
				return redirect('dashboard');
			}
		} catch (Exception $e) {
			DB::rollBack();
			Session::flash('error', 'An error occurred while approving the booking. Please try again.');
			return redirect('dashboard');
		}
	}
	public function rejectMessBooking(Request $request, $id)
	{
		$employee = Session::get('emp_details');
		$messBooking = Mess_Booking::find($id);

		if (!isset($employee->id)) {
			Session::flash('error', 'Your session has ended. Please login to continue.');
			return redirect('/');
		}

		if (!$messBooking) {
			Session::flash('error', 'Y-Lunch not found.');
			return redirect('dashboard');
		}

		DB::beginTransaction();

		try {
			if ($messBooking->status == "Submitted") {
				$messBooking->status = "Rejected";
				$messBooking->remarks = 'Rejected by ' . $employee->employee_name . ': ' . $request->input('rejection_comments');
				$messBooking->save();

				DB::commit();
				Session::flash('success', 'Rejected Successfully.');
				return redirect('dashboard');
			} else {
				DB::rollBack();
				Session::flash('error', 'This booking cannot be rejected as its status is not "Submitted".');
				return redirect('dashboard');
			}
		} catch (\Exception $e) {
			DB::rollBack();
			Session::flash('error', 'An error occurred while processing your request. Please try again.');
			return redirect('dashboard');
		}
	}

	public function generateReport(Request $request)
	{
		$employee = Session::get('emp_details');

		if (!isset($employee->id)) {
			Session::flash('error', 'Your session has ended. Please login to continue.');
			return redirect('/');
		}

		$data['date_range'] = $request->input('dateRange');
		$status_filter = $request->input('status_filter', 'all');

		// Handle date range parsing
		if (!$data['date_range']) {
			Session::flash('error', 'Please select a date range.');
			return redirect()->back();
		}

		$date_range_split = explode(" - ", $data['date_range']); // Note: space around dash
		if (count($date_range_split) < 2) {
			$date_range_split = explode("-", $data['date_range']); // Fallback
		}

		$from_date = trim($date_range_split[0]);
		$to_date = trim($date_range_split[1]);

		$from_date = date('Y-m-d', strtotime($from_date));
		$to_date = date('Y-m-d', strtotime($to_date));

		// Choose method based on status filter
		switch ($status_filter) {
			case 'approved':
				$messBookingsQuery = $employee->approvedYLunchForApprover();
				break;
			case 'pending':
				$messBookingsQuery = $employee->unapprovedYLunchForApprover();
				break;
			case 'rejected':
				$messBookingsQuery = $employee->rejectedYLunchForApprover();
				break;
			default:
				$messBookingsQuery = $employee->totalYLunchForApprover();
				break;
		}

		// Apply date filter and get results
		$messBookings = $messBookingsQuery
			->whereBetween('mess_bookings.created_at', [$from_date . ' 00:00:00', $to_date . ' 23:59:59'])
			->with(['employee'])
			->get();

		// Debug: Check if we have any bookings
		if ($messBookings->isEmpty()) {
			Session::flash('error', 'No bookings found for the selected date range and status filter.');
			return redirect()->back();
		}

		// Generate CSV
		$filename = "mess_bookings_report_" . $status_filter . "_" . date('Y-m-d_H-i-s') . ".csv";

		$headers = [
			'Content-Type' => 'text/csv',
			'Content-Disposition' => 'attachment; filename="' . $filename . '"',
		];

		$callback = function () use ($messBookings) {
			$file = fopen('php://output', 'w');

			// CSV Headers
			fputcsv($file, [
				'S.No',
				'Booking Date',
				'Employee Name',
				'Employee Number',
				'Booking Name',
				'BSP Employee Count',
				'Guest Count',
				'Total Head Count',
				'Booking Start Date',
				'Booking End Date',
				'Booking Time',
				'Status',
				'Remarks'
			]);

			// CSV Data
			$counter = 1;
			foreach ($messBookings as $booking) {
				fputcsv($file, [
					$counter,
					$booking->created_at ? date('M d, Y', strtotime($booking->created_at)) : 'N/A',
					$booking->employee ? $booking->employee->employee_name : 'N/A',
					$booking->employee ? $booking->employee->employee_number : 'N/A',
					$booking->booking_name ? $booking->booking_name : 'N/A',
					$booking->bsp_employee_count ? $booking->bsp_employee_count : 0,
					$booking->guest_count ? $booking->guest_count : 0,
					$booking->total_head_count ? $booking->total_head_count : 0,
					$booking->booking_start_date ? date('M d, Y', strtotime($booking->booking_start_date)) : 'N/A',
					$booking->booking_end_date ? date('M d, Y', strtotime($booking->booking_end_date)) : 'N/A',
					$booking->booking_time ? $booking->booking_time : 'N/A',
					$booking->status ? $booking->status : 'N/A',
					$booking->remarks ? $booking->remarks : ''
				]);
				$counter++;
			}

			fclose($file);
		};

		return response()->stream($callback, 200, $headers);
	}
}
