<div class="input-group date timepicker" id="timepicker-{{ $model->getFormName($key, $listName, $listIndex, $language) }}" data-target-input="nearest">
	<div class="input-group" data-target="#timepicker-{{ $model->getFormName($key, $listName, $listIndex, $language) }}" data-toggle="datetimepicker">
		<input type="text" class="form-control datetimepicker-input" data-target="#timepicker-{{ $model->getFormName($key, $listName, $listIndex, $language) }}" id="{{ $model->getFormName($key, $listName, $listIndex, $language) }}" name="{{ $model->getFormName($key, $listName, $listIndex, $language) }}" value="{{ $model->getValue($key, $listItem, $language) }}" {{ $model->isRequired($key, $isList) }} {{ $model->isDisabled($key) }} label="{{ $model->label($key) }}"/>
		<div class="input-group-addon input-group-append">
			<i class="mdi mdi-clock input-group-text"></i>
		</div>
	</div>
</div>