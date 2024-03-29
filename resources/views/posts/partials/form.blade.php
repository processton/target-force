<x-targetforce.text-field name="name" :label="__('Post Name')" :value="$post->name ?? old('name')" />
<x-targetforce.text-field name="subject" :label="__('Email Subject')" :value="$post->subject ?? old('subject')" />
<x-targetforce.text-field name="from_name" :label="__('From Name')" :value="$post->from_name ?? old('from_name')" />
<x-targetforce.text-field name="from_email" :label="__('From Email')" type="email" :value="$post->from_email ?? old('from_email')" />

<x-targetforce.select-field name="template_id" :label="__('Template')" :options="$templates" :value="$post->template_id ?? old('template_id')" />

<x-targetforce.select-field name="email_service_id" :label="__('Email Service')" :options="$emailServices->pluck('formatted_name', 'id')" :value="$post->email_service_id ?? old('email_service_id')" />

<x-targetforce.checkbox-field name="is_open_tracking" :label="__('Track Opens')" value="1" :checked="$post->is_open_tracking ?? true" />
<x-targetforce.checkbox-field name="is_click_tracking" :label="__('Track Clicks')" value="1" :checked="$post->is_click_tracking ?? true" />

<x-targetforce.textarea-field name="content" :label="__('Content')">{{ $post->content ?? old('content') }}</x-targetforce.textarea-field>

<div class="form-group row">
    <div class="offset-sm-3 col-sm-9">
        <a href="{{ route('targetforce.posts.index') }}" class="btn btn-light">{{ __('Cancel') }}</a>
        <button type="submit" class="btn btn-primary">{{ __('Save and continue') }}</button>
    </div>
</div>

@include('targetforce::layouts.partials.summernote')

@push('js')
    <script>

        $(function () {
            const smtp = {{
                $emailServices->filter(function ($service) {
                    return $service->type_id === \Targetforce\Base\Models\EmailServiceType::SMTP;
                })
                ->pluck('id')
            }};

            let service_id = $('select[name="email_service_id"]').val();

            toggleTracking(smtp.includes(parseInt(service_id, 10)));

            $('select[name="email_service_id"]').on('change', function () {
              toggleTracking(smtp.includes(parseInt(this.value, 10)));
            });
        });

        function toggleTracking(disable) {
            let $open = $('input[name="is_open_tracking"]');
            let $click = $('input[name="is_click_tracking"]');

            if (disable) {
                $open.attr('disabled', 'disabled');
                $click.attr('disabled', 'disabled');
            } else {
                $open.removeAttr('disabled');
                $click.removeAttr('disabled');
            }
        }

    </script>
@endpush
