@extends('layouts.ers-layout')
@section('header')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/tables/datatable/datatables.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/plugins/animate/animate.css')}}">
@endsection
@section('body')
    @if(View()->exists($customView))
        @include($customView)
    @endif
@endsection
@section('footer')
    <script src="{{asset('app-assets/vendors/js/tables/datatable/datatables.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/js/scripts/tables/datatables/datatable-basic.js')}}" type="text/javascript"></script>
    <script>
        var vouchers = [];
        var processedVoucher = [];
        $('#updateStatus').on('click', function(){

            if(confirm('Are you sure you want to update the status ?')){
                $('.documents').each(function(){
                    var checkbox = $(this);
                    if(checkbox.prop('checked')){
                        if( vouchers.indexOf(checkbox.val()) === -1 ){
                            vouchers.push(checkbox.val());
                        }
                    }
                });
                if(vouchers.length == 0){
                    alert('Please select atleast 1 voucher to continue.');
                }
                else{
                    $.ajax({
                        url: "{!! URL::to('voucher/documents/received') !!}",
                        method: 'POST',
                        data:{
                            vouchers: vouchers,
                            _token: "{!! csrf_token() !!}"
                        },
                        success:function(response){
                            var res = JSON.parse(response);
                            if (res === 'success'){
                                var message = '<div class="alert bg-success alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">'+
                                    '<span class="alert-icon"><i class="la la-thumbs-o-up"></i></span>'+
                                    '<button type="button" class="close" data-dismiss="alert" aria-label="Close">'+
                                    '<span aria-hidden="true">&times;</span>'+
                                    '</button>'+
                                        '<strong>Yeah !</strong> Documents status updated successfuly.'+
                                    '</div>';
                                $('#jsMessage').append(message);
                            }
                        },
                        error:function(response){
                            console.log(response);
                        }
                    });
                    $('.documents').each(function(){
                        for(var i=0; i<vouchers.length; i++){
                            if(vouchers[i] == $(this).val()){
                                $(this).closest('tr').remove();
                            }
                        }
                    });
                }
            }
        });
        $('#select_clear').on('change', function(){
            if($(this).prop('checked')){
                $('.documents').each(function(){
                    $(this).prop('checked', true);
                });
            }
            else{
                $('.documents').each(function(){
                    $(this).prop('checked', false);
                    vouchers = [];
                });
            }
        });
    </script>
@endsection