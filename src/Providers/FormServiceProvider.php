<?php

namespace Targetforce\Base\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Targetforce\Base\View\Components\CheckboxField;
use Targetforce\Base\View\Components\FieldWrapper;
use Targetforce\Base\View\Components\FileField;
use Targetforce\Base\View\Components\Label;
use Targetforce\Base\View\Components\SelectField;
use Targetforce\Base\View\Components\SubmitButton;
use Targetforce\Base\View\Components\TextareaField;
use Targetforce\Base\View\Components\TextField;

class FormServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        Blade::component(TextField::class, 'targetforce.text-field');
        Blade::component(TextareaField::class, 'targetforce.textarea-field');
        Blade::component(FileField::class, 'targetforce.file-field');
        Blade::component(SelectField::class, 'targetforce.select-field');
        Blade::component(CheckboxField::class, 'targetforce.checkbox-field');
        Blade::component(Label::class, 'targetforce.label');
        Blade::component(SubmitButton::class, 'targetforce.submit-button');
        Blade::component(FieldWrapper::class, 'targetforce.field-wrapper');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
    }
}
