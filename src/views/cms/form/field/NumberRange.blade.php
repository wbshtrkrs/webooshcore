<div class="range-wrapper">
    <div class="input-group">
        <input class="form-control control-half autonumeric" value="{{ $model->getValue($key.'From', $listItem, $language) }}" type="text" {{ $model->isRequired($key) }} {{ $model->isDisabled($key) }} label="{{ $model->label($key) }}" placeholder="{{ $model->placeholder($key).' From' }}"/>
        <input class="autonumericvalue" id="{{ $model->getFormName($key, $listName, $listIndex, $language, 'From') }}" name="{{ $model->getFormName($key, $listName, $listIndex, $language, 'From') }}" value="{{ $model->getValue($key.'From', $listItem, $language) }}" type="hidden" {{ $model->isDisabled($key) }}/>
    </div>

    <span class="range-separator">To</span>

    <div class="input-group">
        <input class="form-control control-half autonumeric" value="{{ $model->getValue($key.'To', $listItem, $language) }}" type="text" {{ $model->isRequired($key) }} {{ $model->isDisabled($key) }} label="{{ $model->label($key) }}" placeholder="{{ $model->placeholder($key).' To' }}"/>
        <input class="autonumericvalue" id="{{ $model->getFormName($key, $listName, $listIndex, $language, 'To') }}" name="{{ $model->getFormName($key, $listName, $listIndex, $language, 'To') }}" value="{{ $model->getValue($key.'To', $listItem, $language) }}" type="hidden" {{ $model->isDisabled($key) }}/>
    </div>
</div>
