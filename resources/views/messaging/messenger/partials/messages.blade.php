<div class="alert-message clearfix">
    <a class="{{$message->user->id == Auth::id() ? 'msj-emisor' : 'msj-receptor'}}" href="#">
        <img src="{{$message->user->id == Auth::id() ?  asset('img/avatar.png'): asset('img/avatar.png')}}"
             alt="{{ $message->user->name }}" class="img-circle">
    </a>
    <div class="msj-content">
        <!-- <h5 class="media-heading  {{$message->user->id == Auth::id() ? 'pull-left' : 'pull-right'}}"> -->
        <h5 class="msj-heading">
            <strong>{{ $message->user->name }}</strong>
        </h5>
        <p class="msj-sub-info">
            {{Lang::get('items.messenger.posted')}} {{Carbon\Carbon::parse($message->created_at->diffForHumans())}}</small>
        </p>        
        <p class="msj-text">{{ $message->body }}</p>
    </div>
</div>