<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Voucher extends Model {

	public function employee(){
        return $this->belongsTo('App\Employee');
    }

    public function voucherItems(){
        return $this->hasMany('App\Voucher_Item');
    }

    public function department(){
	    return $this->belongsTo('App\Department');
    }

    public function costCenter($company_id){
	    return $this->belongsTo('App\Department', 'charge_to_cost_center','cost_center')->whereHas('businessUnit.company',function($query) use ($company_id){
	        $query->where('companies.id', '=', $company_id);
        })->first();
    }

    public function requiresSecondApproval(){
        return $this->voucherItems()->whereHas('category',function($query){
            $query->where('approval_steps', '=', 2);
        })->get();
    }

    public function voucherStatus(){
        return $this->belongsToMany('App\Employee', 'voucher_employee')->withPivot('approved', 'created_at', 'updated_at');
    }

    public function reportVoucherCategories(){
        return $this->belongsToMany('App\Category', 'voucher_items');
    }

    public function categories($id){
        return $this->join('voucher_items', 'voucher_items.voucher_id','=','vouchers.id')
            ->join('categories', 'categories.id','=', 'voucher_items.category_id')
            ->select('categories.category_name', 'categories.id', 'categories.view')
            ->where('vouchers.id', '=', $id)
            ->distinct()
            ->get();
    }

    public function categoryItems($id, $category){
        return $this->join('voucher_items', 'voucher_items.voucher_id','=','vouchers.id')
            ->join('categories', 'categories.id','=', 'voucher_items.category_id')
            ->select('voucher_items.*')
            ->where('vouchers.id', '=', $id)
            ->where('categories.id', '=', $category)
            ->get();
    }

    public function receiptNotProvided($id, $category){
        return $this->join('voucher_items', 'voucher_items.voucher_id','=','vouchers.id')
            ->join('categories', 'categories.id','=', 'voucher_items.category_id')
            ->where('vouchers.id', '=', $id)
            ->where('categories.id', '=', $category)
            ->where('voucher_items.receipt_copy', '=', 0)
            ->where('voucher_items.attachment', '=', 'No File')
            ->count('voucher_items.id');
    }

    public function totalAmount($id){
        return $this->join('voucher_items', 'voucher_items.voucher_id','=','vouchers.id')
            ->where('vouchers.id', '=', $id)
            ->sum('amount');
    }

    public function totalAmountApproved($id){
        return $this->join('voucher_items', 'voucher_items.voucher_id','=','vouchers.id')
            ->where('vouchers.id', '=', $id)
            ->sum('amount_paid');
    }

    public function totalAmountForex($id){
        return $this->join('voucher_items', 'voucher_items.voucher_id','=','vouchers.id')
            ->where('vouchers.id', '=', $id)
            ->sum('forex_amount');
    }

    public function advanceTotalAmount($id){
        return $this->join('voucher_items', 'voucher_items.voucher_id','=','vouchers.id')
            ->where('vouchers.id', '=', $id)
            ->sum('advance_amount');
    }
    
    public function totalApprovedAmount($id){
        return $this->join('voucher_items', 'voucher_items.voucher_id','=','vouchers.id')
            ->where('vouchers.id', '=', $id)
            ->sum('approved_amount');
    }

    public function categoryTotalAmount($id, $category){
        return $this->join('voucher_items', 'voucher_items.voucher_id','=','vouchers.id')
            ->join('categories', 'categories.id','=', 'voucher_items.category_id')
            ->where('vouchers.id', '=', $id)
            ->where('categories.id', '=', $category)
            ->sum('amount');
    }

    public function categoryTotalAmountForex($id, $category){
        return $this->join('voucher_items', 'voucher_items.voucher_id','=','vouchers.id')
            ->join('categories', 'categories.id','=', 'voucher_items.category_id')
            ->where('vouchers.id', '=', $id)
            ->where('categories.id', '=', $category)
            ->sum('forex_amount');
    }

    public function categoryAdvanceTotalAmount($id, $category){
        return $this->join('voucher_items', 'voucher_items.voucher_id','=','vouchers.id')
            ->join('categories', 'categories.id','=', 'voucher_items.category_id')
            ->where('vouchers.id', '=', $id)
            ->where('categories.id', '=', $category)
            ->sum('advance_amount');
    }

    public function currentApprover(){
        return $this->belongsToMany('App\Employee','voucher_employee')->wherePivot('approved', '=', NULL);
    }

    public function approvers(){
        return $this->belongsToMany('App\Employee','voucher_employee')->withPivot('comments');
    }

    public function processedVouchers(){
        return $this->hasMany('App\Processed_Voucher');
    }
}
