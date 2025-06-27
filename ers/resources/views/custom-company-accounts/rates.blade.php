{{--*/ $rates = \App\Rate::with('rateGrade.company')->whereHas('rateGrade.company', function($query) use ($employee){$query->where('companies.id', '=', $employee->department->businessUnit->company->id);})->get(); /*--}}
<div class="content-body">
    <section id="configuration">
        <div class="row">
            <div class="col-12">
                <div class="card" style="overflow: auto;">
                    <div class="card-header">
                        <h4 class="card-title">Rates</h4>
                        <a class="heading-elements-toggle"><i class="la la-ellipsis-v font-medium-3"></i></a>
                        <div class="heading-elements">
                            <ul class="list-inline mb-0">
                                <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="card-content collapse show">
                        <div class="card-body card-dashboard">
                            <table id="project-task-list" class="table table-striped table-bordered row-grouping">
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Grade</th>
                                    <th>Name</th>
                                    <th>Amount</th>
                                    <th>From</th>
                                    <th>To</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($rates as $rate)
                                    <tr>
                                        <td>{{$rate->id}}</td>
                                        <td>{{$rate->rateGrade->primary_name.' / '.$rate->rateGrade->company->company_name}}</td>
                                        <td>{{$rate->rate_name}}</td>
                                        <td>{{$rate->currency.' '.$rate->amount}}</td>
                                        <td>{{date('d M, Y', strtotime($rate->rate_from))}}</td>
                                        <td>{{isset($rate->rate_to)?date('d M, Y', strtotime($rate->rate_to)):"-"}}</td>
                                    </tr>
                                @endforeach
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>