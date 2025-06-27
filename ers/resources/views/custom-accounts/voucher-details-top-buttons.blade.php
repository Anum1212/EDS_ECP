<a href="{{URL::to($forwardingURL.'/'.'documents/received'.'/'.$voucher->id)}}"><button type="button" class="btn btn-sm btn-success"><i class="la la-check-circle"></i> Documents Received</button></a>
<a class="reject" data-toggle="modal" data-target="#reject{{$voucher->id}}"><button type="button" class="btn btn-sm btn-danger"><i class="la la-times-circle"></i> Documents Objection</button></a>
<div class="modal animated shake text-left" id="reject{{$voucher->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel10" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger white">
                <h4 class="modal-title white" id="myModalLabel10">Reject</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="post" action="{{URL::to('documents/objection'.'/'.$voucher->id)}}">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <div class="modal-body">
                    <h5>Are you sure you want to raise document objection on this voucher ?</h5>
                    <hr>
                    <label>Rejection Comments</label>
                    <textarea rows="5" class="form-control" name="rejection_comments" id="rejection_comments" maxlength="500" required>{{Input::old('rejection_comments')}}</textarea>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-outline-danger">Reject!</button>
                    <button type="button" class="btn grey btn-outline-secondary" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>