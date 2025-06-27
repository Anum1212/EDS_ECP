{{--*/ $businessUnits = \App\Business_Unit::all(); /*--}}
<div class="content-body">
    <section id="form-control-repeater">
        <form action="{{URL::to('department/add')}}" method="post">
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title" id="file-repeater">Department Information</h4>
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
                                    <div class="form-group col-md-4 mb-2">
                                        <label>Select Business Unit</label>
                                        <select class="select2 form-control" name="business_unit" required>
                                            @foreach($businessUnits as $businessUnit)
                                                <option value="{{$businessUnit->id}}">{{$businessUnit->bu_name.' / '.$businessUnit->company->company_name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-md-8 mb-2">
                                        <label>Department Name</label>
                                        <input type="text" class="form-control" value="{{Input::old('department_name')}}" name="department_name" required>
                                    </div>
                                </div>
                                <div class="form row" id="bu-form">
                                    <div class="form-group col-md-4 mb-2">
                                        <label>Cost Center</label>
                                        <input type="text" class="form-control" value="{{Input::old('cost_center')}}" name="cost_center" maxlength="5" required>
                                    </div>
                                    <div class="form-group col-md-4 mb-2">
                                        <label>Select Classification</label>
                                        <select class="select2 form-control" name="classification" required>
                                            @foreach($classifications as $classification)
                                                <option value="{{$classification->classification}}">{{$classification->classification}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer" id="bu-form-footer">
                                <button class="btn btn-success btn-sm" type="submit"><i class="la la-paper-plane-o"></i> Submit </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </section>
</div>
