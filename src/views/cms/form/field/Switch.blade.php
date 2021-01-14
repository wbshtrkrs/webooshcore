<label class="custom-input type-pills">
    <input type='hidden' value="0" name="{{ $model->getFormName($key, $listName, $listIndex, $language) }}"'>
    <input class="form-control" id="{{ $model->getFormName($key, $listName, $listIndex, $language) }}" type="checkbox" name="{{ $model->getFormName($key, $listName, $listIndex, $language) }}" value="1" {{ $model->isDisabled($key) }} label="{{ $model->label($key) }}" @if($model->getValue($key, $listItem, $language) == 1) checked @endif>
    <span class="checker"></span>
</label>
