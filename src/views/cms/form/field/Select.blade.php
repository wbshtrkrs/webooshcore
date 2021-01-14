<div class="select-wrapper">
        <select  class="form-control select-custom control-half" id="{{ $model->getFormName($key, $listName, $listIndex, $language) }}" name="{{ $model->getFormName($key, $listName, $listIndex, $language) }}" value="{{ $model->getValue($key, $listItem, $language) }}" {{ $model->isRequired($key, $isList) }} {{ $model->isDisabled($key) }} label="{{ $model->label($key) }}">
                @foreach($model->formSelectList($key) as $selectKey=>$selectLabel)
                        <option value="{{ $selectKey }}"
                                @if($selectKey == $model->getValue($key, $listItem, $language)) selected @endif>{{ $selectLabel }}</option>
                @endforeach
        </select>
</div>
