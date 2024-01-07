<div class="row">
    @foreach($templates as $template)
        <div class="col-lg-4 col-md-6 col-sm-12">
            <div class="card">
                <div class="card-header">
                    <div class="float-left">
                        {{ $template->name }}
                    </div>
                    <div class="float-right">
                        @if ( ! $template->is_in_use)
                            <form action="{{ route('targetforce.templates.destroy', $template->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <a href="{{ route('targetforce.templates.edit', $template->id) }}"
                                   class="btn btn-xs btn-light">{{ __('Edit') }}</a>
                                <button type="submit" class="btn btn-xs btn-light">{{ __('Delete') }}</button>
                            </form>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    @include('targetforce::templates.partials.griditem')
                </div>
            </div>
        </div>
    @endforeach
</div>

{{ $templates->links() }}
