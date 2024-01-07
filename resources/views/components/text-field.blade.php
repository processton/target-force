<x-targetforce.field-wrapper :name="$name" :label="$label">
    <input type="{{ $type }}" name="{{ $name }}" value="{{ $value }}" {{ $attributes->merge(['id' => 'id-field-' .  str_replace('[]', '', $name), 'class' => 'form-control']) }}>
</x-targetforce.field-wrapper>
