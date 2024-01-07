<div {{ $attributes->merge(['class' => 'form-group row form-group-' . $name . ' ' . $wrapperClass  . ' '. $errorClass($name)]) }}>
    <x-targetforce.label :name="$name">{{ $label }}</x-targetforce.label>
    <div class="col-sm-9">
        {{ $slot }}
    </div>
</div>