<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    public function sapRecord(){
        return $this->hasOne('App\SAP_Sync', 'employee_number', 'employee_number');
    }    

    public function employeeGrade(){
        return $this->belongsTo('App\Grade', 'grade_id');
    }
    
    public function entitlements(){
        return $this->hasMany('App\Entitlements', 'employee_number', 'employee_number');
    }

    /**
     * All the relationships that belongs to Voucher are defined below.
     *
     */

    public function vouchers()
    {
        return $this->hasMany('App\Voucher')->where('is_travel_order', '=', NULL);
    }

    public function approvedVouchers(){
        return $this->hasMany('App\Voucher')->where('is_travel_order', '=', NULL)->whereIn(
            'status', [
                'Approved - Documents in transit',
                'Processed',
                'In Process',
                'Posted'
            ]);
    }

    public function unapprovedVouchers(){
        return $this->hasMany('App\Voucher')->where('is_travel_order', '=', NULL)->whereNotIn(
            'status', [
                'Approved - Documents in transit',
                'Processed',
                'In Process',
                'Posted'
            ]);
    }

    // Y-Lunch Section
    public function totalYLunch(){
        return $this->hasMany('App\Mess_Booking')->whereIn(
            'status', [
                'Draft',
                'Submitted',
                'Approved',
                'Rejected'
            ]);
    }
    public function approvedYLunch(){
        return $this->hasMany('App\Mess_Booking')->where('status', 'Approved');
    }
    public function unapprovedYLunch(){
        return $this->hasMany('App\Mess_Booking')->where('status', 'Submitted');
    }
        public function rejectedYLunch(){
        return $this->hasMany('App\Mess_Booking')->where('status', "'Rejected");
    }

        public function approvedYLunchForApprover(){
        return $this->hasMany('App\Mess_Booking')->where('status', 'Approved');
    }
    public function unapprovedYLunchForApprover(){
        return $this->hasMany('App\Mess_Booking')->where('status', 'Submitted');
    }
        public function rejectedYLunchForApprover(){
        return $this->hasMany('App\Mess_Booking')->where('status', "'Rejected");
    }

    public function department()
    {
        return $this->belongsTo('App\Department');
    }

    public function bankAccounts()
    {
        return $this->belongsToMany('App\Bank', 'Employee_Bank')->withPivot('account_number', 'default', 'account_title')->wherePivot('default', 1);
    }

    public function vouchersRequireApproval()
    {
        return $this->belongsToMany('App\Voucher', 'voucher_employee')->where('is_travel_order', '=', NULL)->wherePivot('approved', NULL);
    }

    public function vouchersApproved()
    {
        return $this->belongsToMany('App\Voucher', 'voucher_employee')->where('is_travel_order', '=', NULL)->wherePivot('approved', '=', 1);
    }

    public function vouchersDeclined()
    {
        return $this->belongsToMany('App\Voucher', 'voucher_employee')->where('is_travel_order', '=', NULL)->wherePivot('approved', '=', 0);
    }

    public function divisionsApprover(){
        return $this->belongsToMany('App\Business_Unit', 'business_unit_employee', 'employee_id', 'business_unit_id');
    }

    public function vehicles(){
        return $this->hasMany('App\Vehicle');
    }

    public function consumedLitres(){
        return $this->hasManyThrough('App\Voucher_Item', 'App\Voucher')->selectRaw('SUM(voucher_items.litres) as consumedLitres')->whereNotNull('voucher_items.litres');
    }

    /**
     * All the relationships that belongs to Travel Order are defined below.
     *
     */

    public function travelOrders(){
        return $this->hasMany('App\Voucher')->where('is_travel_order', '=', 1);
    }

    public function approvedUnusedTravelOrders(){
        return $this->hasMany('App\Voucher')->where('is_travel_order', '=', 1)->whereIn(
            'status', [
                'Posted'
            ]);
    }

    public function approvedTravelOrders(){
        return $this->hasMany('App\Voucher')->where('is_travel_order', '=', 1)->whereIn(
            'status', [
                'Approved - Documents in transit',
                'Processed',
                'In Process',
                'Posted',
                'Adjusted'
            ]);
    }

    public function unapprovedTravelOrders(){
        return $this->hasMany('App\Voucher')->where('is_travel_order', '=', 1)->whereNotIn(
            'status', [
                'Approved - Documents in transit',
                'Processed',
                'In Process',
                'Posted',
                'Adjusted'
            ]);
    }

    public function travelOrdersRequireApproval()
    {
        return $this->belongsToMany('App\Voucher', 'voucher_employee')->where('is_travel_order', '=', 1)->wherePivot('approved', NULL);
    }

    public function travelOrdersApproved()
    {
        return $this->belongsToMany('App\Voucher', 'voucher_employee')->where('is_travel_order', '=', 1)->wherePivot('approved', '=', 1);
    }

    public function travelOrdersDeclined()
    {
        return $this->belongsToMany('App\Voucher', 'voucher_employee')->where('is_travel_order', '=', 1)->wherePivot('approved', '=', 0);
    }
}
