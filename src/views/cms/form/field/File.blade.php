<div class="row no-gutters">
    <div class="col-md-6">
        @if(@$model->getValue($key, $listItem, $language))
            <div class="input-group">
                <input type="text" class="form-control" disabled value="{!! $model->getValue($key, $listItem, $language) !!}">
                <input type="hidden" class="form-control" name="{{ $model->getFormName($key, $listName, $listIndex, $language) }}" value="{!! $model->getValue($key, $listItem, $language) !!}">

                @if(!$model->isDisabled($key))
                    <span class="input-group-addon file-change">Delete</span>
                @endif

                <a target="_blank" class="input-group-addon open-file" href="{!! GetFileURL($model->getValue($key, $listItem, $language)) !!}"><i class="fa fa-search-plus"></i></a>
            </div>
            <div class="input-group hide">
                <input type="file" class="form-control" accept="application/pdf" name="{{ $model->getFormName($key, $listName, $listIndex, $language) }}">
            </div>
        @else
            <input type="file" class="form-control" accept="application/pdf" name="{{ $model->getFormName($key, $listName, $listIndex, $language) }}">
        @endif
    </div>
</div>
