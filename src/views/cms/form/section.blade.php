@php
    $isList = true;
    if (empty($listType)) {
        $listType = $model::FORM_TYPE;
        $isList = false;
    }

    $parentLanguage = getParentLanguage();
    $language = getLanguageSession();
@endphp

@foreach( $listType as $key => $value)
    @php
        $formType = $model->formType($key);
        if ($isList) $formType = $listType[$key];

        $allowedExtension = '';

        if( strpos( $formType , 'Image' ) !== false ){
            $imageCount = (substr($formType, 6) - 1);
            $formType = 'Image';
            $allowedExtension = 'image/*';
        }

        if( strpos( $formType , 'Rating' ) !== false ){
            $ratingCount = (substr($formType, 7));
            $formType = 'Rating';
        }

        if (!isset($listItem)) $listItem = '';
        if (!isset($listName)) $listName = '';
        if (!isset($listIndex)) $listIndex = '';

        $isDisabledFormLanguage = false;

        if ($parentLanguage != $language) {
            $formLangDisabled = $model::FORM_LANGUAGE_DISABLED;

            if (in_array($key, $formLangDisabled)) $isDisabledFormLanguage = true;
        }
    @endphp

    @if ($formType && $formType != 'ListSortable' && $model->isRemoved($key) && !$isDisabledFormLanguage)
        @include('cms::form.group')
    @endif

@endforeach
