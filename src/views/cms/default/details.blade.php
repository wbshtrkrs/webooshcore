@extends('cms::layouts.authorized')

@section('headerCustom')
    @php
        $id = 0;

        $title = $model::getTitleDetails();

        $formUrl = $model->getUrlDetails();
        $cancelUrl = $model->getUrlIndex();

        $buttonSection = true;

        $language = '';
    @endphp
@endsection

@section('metaTitle', @$title)

@section('authorizeContent')

    <form id="form" action="{!! $formUrl !!}" class="create-project form-horizontal" method="POST"  enctype="multipart/form-data">
        @include('cms::form.main')
    </form>

@endsection