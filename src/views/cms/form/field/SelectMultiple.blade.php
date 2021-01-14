<div class="select-wrapper">
    <select  class="form-control select-custom control-half" multiple id="{{ $model->getFormName($key, $listName, $listIndex, $language) }}" name="{{ $model->getFormName($key, $listName, $listIndex, $language) }}" {{ $model->isRequired($key, $isList) }} {{ $model->isDisabled($key) }} label="{{ $model->label($key) }}">
        @foreach($model->formSelectList($key) as $selectKey=>$selectLabel)
            <option value="{{ $selectKey }}"
                    @if( in_array($selectKey, $model->getValue($key, $listItem, $language))) selected @endif>{{ $selectLabel }}</option>
        @endforeach
    </select>
</div>
