<div class="image-selector-wrapper">
    @if(empty($model->isDisabled($key)))
        <input type="file" class="btn-upload-file" @if(empty(@$image)) id="{{ $model->getFormName($key.$imageKey, $listName, $listIndex, $language) }}" name="{{ $model->getFormName($key.$imageKey, $listName, $listIndex, $language) }}" @endif label="{{ $model->label($key) }}" accept=".jpg,.jpeg,.png" {!! $model->getImageLimit($key) !!}/>
    @endif
    @if(!empty(@$image))
        <input type="hidden" id="{{ $model->getFormName($key.$imageKey, $listName, $listIndex, $language) }}" name="{{ $model->getFormName($key.$imageKey, $listName, $listIndex, $language) }}" value="{{ @$image }}"/>
    @endif
    <button class="btn-upload" type="button">
        <img class="image" @if(!empty(@$image)) src="{{ getImageUrl(@$image) }}" @endif/>
        <div class="upload-section {!! (!empty(@$image)) ? 'hidden' : '' !!}">
            <i class="fa fa-upload"></i>
            Upload
        </div>
    </button>
    @if(empty($model->isDisabled($key)))
        <i @if(empty(@$image)) style="display: none" @endif class="fa fa-trash-o image-delete-icon"></i>
    @endif
</div>

