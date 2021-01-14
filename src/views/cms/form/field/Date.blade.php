<div class="input-group date datepicker">
	<input type="text" class="form-control" id="{{ $model->getFormName($key, $listName, $listIndex, $language) }}" name="{{ $model->getFormName($key, $listName, $listIndex, $language) }}" value="{{ $model->getValue($key, $listItem, $language) }}" {{ $model->isRequired($key, $isList) }} {{ $model->isDisabled($key) }} label="{{ $model->label($key) }}">
	<span class="input-group-addon input-group-append border-left">
		<span class="mdi mdi-calendar input-group-text"></span>
	  </span>
</div>