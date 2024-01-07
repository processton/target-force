{!! Form::textField('subject', __('Email Subject'), optional($post->email)->subject) !!}
{!! Form::textField('from_name', __('From Name'), optional($post->email)->from_name) !!}
{!! Form::textField('from_email', __('From Email'), optional($post->email)->from_email) !!}
{!! Form::selectField('template_id', __('Templates'), $templates) !!}
