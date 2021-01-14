<div class="range-wrapper">
    <div class="input-group date timepicker" id="timepicker-{{ $model->getFormName($key, $listName, $listIndex, $language, 'From') }}" data-target-input="nearest">
        <div class="input-group" data-target="#timepicker-{{ $model->getFormName($key, $listName, $listIndex, $language , 'From') }}" data-toggle="datetimepicker">
            <input type="text" class="form-control datetimepicker-input" data-target="#timepicker-{{ $model->getFormName($key, $listName, $listIndex, $language, 'From') }}" id="{{ $model->getFormName($key, $listName, $listIndex, $language, 'From') }}" name="{{ $model->getFormName($key, $listName, $listIndex, $language, 'From') }}" value="{{ $model->getValue($key.'From', $listItem, $language) }}" {{ $model->isRequired($key, $isList) }} {{ $model->isDisabled($key) }} label="{{ $model->label($key) }}"/>
            <div class="input-group-addon input-group-append">
                <i class="mdi mdi-clock input-group-text"></i>
            </div>
        </div>
    </div>

    <span class="range-separator">To</span>

    <div class="input-group date timepicker" id="timepicker-{{ $model->getFormName($key, $listName, $listIndex, $language, 'To') }}" data-target-input="nearest">
        <div class="input-group" data-target="#timepicker-{{ $model->getFormName($key, $listName, $listIndex, $language, 'To') }}" data-toggle="datetimepicker">
            <input type="text" class="form-control datetimepicker-input" data-target="#timepicker-{{ $model->getFormName($key, $listName, $listIndex, $language, 'To') }}" id="{{ $model->getFormName($key, $listName, $listIndex, $language, 'To') }}" name="{{ $model->getFormName($key, $listName, $listIndex, $language, 'To') }}" value="{{ $model->getValue($key.'To', $listItem, $language) }}" {{ $model->isRequired($key, $isList) }} {{ $model->isDisabled($key) }} label="{{ $model->label($key) }}"/>
            <div class="input-group-addon input-group-append">
                <i class="mdi mdi-clock input-group-text"></i>
            </div>
        </div>
    </div>
</div>
