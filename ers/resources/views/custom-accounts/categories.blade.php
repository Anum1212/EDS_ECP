{{--*/ $categories = \App\Category::all(); /*--}}
<div class="content-body">
    <section id="configuration">
        <div class="row">
            <div class="col-12">
                <div class="card" style="overflow: auto;">
                    <div class="card-header">
                        <h4 class="card-title">Categories</h4>
                        <a class="heading-elements-toggle"><i class="la la-ellipsis-v font-medium-3"></i></a>
                        <div class="heading-elements">
                            <ul class="list-inline mb-0">
                                <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="card-content collapse show">
                        <div class="card-body card-dashboard">
                            <table class="table table-striped table-bordered zero-configuration">
                                <thead>
                                <tr>
                                    <th>Company</th>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Approval Steps</th>
                                    <th>Protocol</th>
                                    <th>Description</th>
                                    <th>View</th>
                                    <th>GL Account</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($categories as $category)
                                    <tr>
                                        <td>{{$category->company->company_name}}</td>
                                        <td><a href="{{URL::to('category/update'.'/'.$category->id)}}">{{$category->id}}</a></td>
                                        <td>{{$category->category_name}}</td>
                                        <td>{{$category->approval_steps}}</td>
                                        <td>{{$category->protocol}}</td>
                                        <td>{{$category->description}}</td>
                                        <td>{{$category->view}}</td>
                                        <td>
                                            @foreach($category->glMappings as $glMapping)
                                                <p>{{$glMapping->gl_account .' - '.$glMapping->classification}}</p>
                                            @endforeach
                                        </td>
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