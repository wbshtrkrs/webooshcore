<div class="text-editor-wrapper">
    <textarea class="text-editor" id="{{ $model->getFormName($key, $listName, $listIndex, $language) }}" name="{{ $model->getFormName($key, $listName, $listIndex, $language) }}">{{ $model->getValue($key, $listItem, $language) }}</textarea>
</div>
