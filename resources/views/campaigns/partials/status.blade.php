@if($post->draft)
    <span class="badge badge-light">{{ $post->status->name }}</span>
@elseif($post->queued)
    <span class="badge badge-warning">{{ $post->status->name }}</span>
@elseif($post->sending)
    <span class="badge badge-warning">{{ $post->status->name }}</span>
@elseif($post->sent)
    <span class="badge badge-success">{{ $post->status->name }}</span>
@elseif($post->cancelled)
    <span class="badge badge-danger">{{ $post->status->name }}</span>
@endif
