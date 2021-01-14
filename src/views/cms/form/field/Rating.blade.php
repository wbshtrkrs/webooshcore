<div class="br-wrapper br-theme-bars-1to10">
    <select class="rating-picker" name="{{ $model->getFormName($key, $listName, $listIndex, $language) }}" autocomplete="off" style="display: none;" {{ $model->isRequired($key, $isList) }} {{ $model->isDisabled($key) }} label="{{ $model->label($key) }}">
        @for($i = 1 ; $i <= $ratingCount ; $i++)
            <option value="{!! $i !!}" @if($model->getValue($key, $listItem, $language) == $i) selected @endif>{!! $i !!}</option>
        @endfor
    </select>
</div>