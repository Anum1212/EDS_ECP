@extends('layouts.ers-layout')
@section('header')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/icheck/icheck.css')}}">
@endsection
@section('body')
    <div class="content-body">
        <section id="form-control-repeater">
            <form action="{{URL::to('gl-account/add')}}" method="post">
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title" id="file-repeater">GL Account Information</h4>
                                <a class="heading-elements-toggle"><i class="la la-ellipsis-h font-medium-3"></i></a>
                                <div class="heading-elements">
                                    <ul class="list-inline mb-0">
                                        <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="card-content collapse show">
                                <div class="card-body">
                                    <div class="form row" id="bu-form">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <div class="form-group col-md-6 mb-2">
                                            <label>Category</label>
                                            <select class="select2 form-control category generateable" name="category" required>
                                                <option value="">Select</option>
                                                @foreach($categories as $category)
                                                    <option value="{{$category->id}}">{{$category->category_name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group col-md-6 mb-2">
                                            <label>Classification</label>
                                            <select class="select2 form-control classification generateable" name="classification" required>
                                                <option value="">Select</option>
                                                @foreach($classifications as $classification)
                                                    <option value="{{$classification->classification}}">{{$classification->classification}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group col-md-6 mb-2">
                                            <label>GL Account</label>
                                            <input type="text" class="form-control gl_account" name="gl_account" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer" id="bu-form-footer">
                                    <button class="btn btn-success btn-sm" type="submit"><i class="la la-paper-plane-o"></i> Submit </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        @if(view()->exists($customView))
                            @include($customView)
                        @endif
                    </div>
                </div>
            </form>
        </section>
    </div>
@endsection
@section('footer')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/js/scripts/forms/select/form-select2.js')}}" type="text/javascript"></script>
    <script>
        $('.generateable').on('change',function(){
            var category = $('.category').val();
            var classification = $('.classification').val();
            if(category != "" && classification != ""){
                getGLAccount(category, classification);
            }
        });
        function getGLAccount(category, classification){
            $.ajax({
                url: "{!! URL::to('check/gl-mapping') !!}",
                method: "POST",
                data:{
                    category:category,
                    classification:classification
                },
                success:function(response){
                    var res = JSON.parse(response);
                    if(res.gl_account){
                        $('.gl_account').val(res.gl_account);
                    }
                    else{
                        if(res == 'Not Found'){
                            $('.gl_account').val('');
                            $('.gl_account').attr('placeholder', 'Please Enter New GL Account Here');
                        }
                        else{
                            $('.gl_account').val('');
                            $('.gl_account').attr('placeholder', res);
                        }
                    }
                },
                failure:function(response){
                    console.log(response);
                }
            })
        }
    </script>
@endsection