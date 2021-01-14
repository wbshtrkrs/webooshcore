<div class="input-group amount-wrapper">
	<div class="input-group-prepend">
		<span class="input-group-text">{{ $model::AMOUNT_CURRENCY }}</span>
	</div>
	<input class="form-control autonumeric" value="{{ $model->getValue($key, $listItem, $language) }}" type="text" {{ $model->isRequired($key) }} {{ $model->isDisabled($key) }} label="{{ $model->label($key) }}" placeholder="{{ $model->placeholder($key) }}"/>
	<input class="autonumericvalue" id="{{ $model->getFormName($key, $listName, $listIndex, $language) }}" name="{{ $model->getFormName($key, $listName, $listIndex, $language) }}" value="{{ $model->getValue($key, $listItem, $language) }}" type="hidden" {{ $model->isDisabled($key) }}/>
</div>
