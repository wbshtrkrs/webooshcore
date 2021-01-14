<div class="card">
    <div class="card-body">
        <div class="card-header">
            <h4 class="card-title">{!! @$title !!}</h4>
        </div>

        @if($model::USE_META_SET)
            @include('cms::form.meta')
        @endif

        @if(count(getNonListDetailsSection($model)) > 0)
            @include('cms::form.section')
        @endif

        @foreach($model::FORM_LIST as $listName=>$listType)
            <div class="panel-section-wrapper">

                <div class="panel-header">
                    <h3 class="header-title">{{ keyToLabel($listName) }}</h3>
                    <button type="button" class="btn btn-primary btn-add-row">Add Row <i class="fa fa-plus"></i></button>
                </div>

                <div class="panel-list-wrapper">
                    @if(!empty($model->getValue($listName, '', $language)))
                        @foreach($model->getValue($listName, '', $language) as $listIndex=>$listItem)
                            <div class="panel panel-default panel-section">
                                <div class="panel-heading">
                                    <div class="panel-title">
                                        <h4>{{ $listIndex+1 }}</h4>
                                    </div>
                                    <i class="fa fa-times remove-panel"></i>
                                </div>
                                <div class="panel-body">
                                    @include('cms::form.section')
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>

                @php
                    $listIndex = -1;
                    $listItem = [];
                @endphp

                <div style="display: none" class="panel-section-template">
                    <div class="panel panel-default panel-section">
                        <div class="panel-heading">
                            <div class="panel-title">
                                <h4></h4>
                            </div>
                            <i class="fa fa-times remove-panel"></i>
                        </div>
                        <div class="panel-body">
                            @include('cms::form.section')
                        </div>
                    </div>
                </div>

            </div>
        @endforeach

        @include('cms::form.button')

    </div>
</div>
