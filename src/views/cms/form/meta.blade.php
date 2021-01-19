@php
    $language = getLanguageSession();
@endphp

<div class="form-group">
    <label class="label-required">
        {{ $model->label('metaTitle', 'META') }}
        @if(!empty($model->labelHelp('metaTitle', 'META')))<span class="label-help">({{ $model->labelHelp('metaTitle', 'META') }})</span>@endif
    </label>
    <input type="text" class="form-control" name="{{ $model->getFormName('metaTitle', '', 0, $language) }}" value="{{ $model->getValue('metaTitle', '', $language) }}" placeholder="{{ $model->label('metaTitle', 'META') }}" required>
</div>

<div class="form-group">
    <label class="label-required">
        {{ $model->label('metaDescription', 'META') }}
        @if(!empty($model->labelHelp('metaTitle', 'META')))<span class="label-help">({{ $model->labelHelp('metaTitle', 'META') }})</span>@endif
    </label>
    <textarea class="form-control" cols="30" rows="5" name="{{ $model->getFormName('metaDescription', '', 0, $language) }}" placeholder="{{ $model->label('metaDescription', 'META') }}" required>{{ $model->getValue('metaDescription', '', $language) }}</textarea>
</div>

<div class="form-group">
    <label class="label-required">
        {{ $model->label('metaKeywords', 'META') }}
        @if(!empty($model->labelHelp('metaTitle', 'META')))<span class="label-help">({{ $model->labelHelp('metaTitle', 'META') }})</span>@endif
    </label>
    <input type="text" class="form-control" name="{{ $model->getFormName('metaKeywords', '', 0, $language) }}" value="{{ $model->getValue('metaKeywords', '', $language) }}" placeholder="{{ $model->label('metaKeywords', 'META') }}" required>
</div>
