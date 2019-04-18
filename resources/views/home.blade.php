@extends('layouts.app')

@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Dashboard</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    You are logged in!
                </div>
            </div>
        </div>
    </div>
    <div id="user">
        
    </div>
</div>

<script type="text/javascript">
        var pusher = new Pusher('b8a769bb4ba59781929d', {
                  cluster: 'ap1',
                  encrypted: true,
                });
 
        
        var channel = pusher.subscribe('hieutt-channel');
         

        channel.bind('send.user.create', function(data){
            console.log(data.user.email);
            $("#user").append(`<div><label class="label label-success">`+data.user.email+`</label></div>`);
        });


        // window.Echo = new Echo({
        //     broadcaster: 'pusher',
        //     key: 'b8a769bb4ba59781929d',
        //     cluster: 'ap1',
        //     encrypted: true
        // });
        // Echo.channel('my-name-channel')
        // .listen('.send.user.create', (e) => {
        //     console.log(e);
        // });
     
</script>
@endsection
