<div class="range-wrapper">
    <div class="input-group date datepicker">
        <input type="text" class="form-control" id="{{ $model->getFormName($key, $listName, $listIndex, $language, 'From') }}" name="{{ $model->getFormName($key, $listName, $listIndex, $language, 'From') }}" value="{{ $model->getValue($key.'From', $listItem, $language) }}" {{ $model->isRequired($key, $isList) }} {{ $model->isDisabled($key) }} label="{{ $model->label($key) }}">
        <span class="input-group-addon input-group-append border-left">
		<span class="mdi mdi-calendar input-group-text"></span>
	  </span>
    </div>

    <span class="range-separator">To</span>

    <div class="input-group date datepicker">
        <input type="text" class="form-control" id="{{ $model->getFormName($key, $listName, $listIndex, $language, 'To') }}" name="{{ $model->getFormName($key, $listName, $listIndex, $language, 'To') }}" value="{{ $model->getValue($key.'To', $listItem, $language) }}" {{ $model->isRequired($key, $isList) }} {{ $model->isDisabled($key) }} label="{{ $model->label($key) }}">
        <span class="input-group-addon input-group-append border-left">
		<span class="mdi mdi-calendar input-group-text"></span>
	  </span>
    </div>
</div>
