<div class="image-section {!! ($imageCount == -1) ? 'image-multiple-wrapper' : '' !!}">

    @if(!empty($model->getValue($key, $listItem, $language)))
        @foreach($model->getValue($key, $listItem, $language) as $imageKey=>$image)
            @include('cms::form.field.ImagePart')
        @endforeach
    @endif

    @if($imageCount == -1)
        @php
            $imageKey = countImage($model->getValue($key, $listItem, $language));
            $image = null;
        @endphp
        @if(empty($model->isDisabled($key)))
            @include('cms::form.field.ImagePart')
        @endif
    @elseif( $imageCount >= countImage($model->getValue($key, $listItem, $language)) )
        @foreach( range(countImage($model->getValue($key, $listItem, $language)), $imageCount)  as $imageKey)
            @include('cms::form.field.ImagePart')
        @endforeach
    @endif

</div>