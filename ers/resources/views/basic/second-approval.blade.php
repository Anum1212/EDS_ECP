@extends('layouts.ers-layout')
@section('header')
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/selects/select2.min.css')}}">
@endsection
@section('body')
    <div class="content-header row">
        <div class="content-header-left col-md-6 col-12 mb-2">
            <h3 class="content-header-title">Second Approval</h3>
            <p>Please select the approver from the list</p>
        </div>
    </div>
    <div class="content-body">
        <section id="form-control-repeater">
            <form action="{{URL::to($forwardingURL.'/approve'.'/'.$approval->id)}}" method="post">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title" id="file-repeater">Approvers' Information</h4>
                                <a class="heading-elements-toggle"><i class="la la-ellipsis-h font-medium-3"></i></a>
                                <div class="heading-elements">
                                    <ul class="list-inline mb-0">
                                        <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="card-content collapse show">
                                <div class="card-body">
                                    <div class="form-row">
                                        <div class="form-group col-md-12 mb-2">
                                            <select class="select2 form-control" name="approver" required>
                                                <option value="">Select</option>
                                                @foreach($approvers as $approver)
                                                    <option value="{{$approver->id}}">{{$approver->employee_name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer" id="company-form-footer">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <button class="btn btn-success white btn-sm" type="submit" ><i class="la la-paper-plane-o"></i> Submit </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </section>
    </div>
@endsection
@section('footer')
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('app-assets/js/scripts/forms/select/form-select2.js')}}" type="text/javascript"></script>
@endsection