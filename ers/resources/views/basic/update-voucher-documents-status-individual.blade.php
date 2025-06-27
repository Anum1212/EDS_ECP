@extends('layouts.ers-layout')
@section('header')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <style>
        .content {
            background-color: #414042; /* Grey background */
        }
    </style>
@endsection
@section('body')
    {{-- {{$vouchers}} --}}
    @if(View()->exists($customView))
        @include($customView)
    @endif
@endsection
@section('footer')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/js/scripts/forms/select/form-select2.js')}}" type="text/javascript"></script>
    <script>
        $('.voucher').on('change',function(e) {
            var voucher_id = $('.voucher').val();
            console.log(voucher_id);
            $.ajax({
                url: "{!! URL::to('get/voucher/details') !!}",
                method: "POST",
                data:{
                    voucher_id: voucher_id
                },
                success:function(response){
                    if(response == 0){
                        $('#voucher-details').html('');
                        $('#error').modal('show');
                    }
                    else{
                        $('#voucher-details').html('');
                        $('#voucher-details').append(response);
                    }
                },
                failure:function(response){

                }
            })
        });
    </script>
@endsection