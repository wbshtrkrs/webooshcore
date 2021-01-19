@extends('cms::layouts.authorized')

@section('metaTitle', 'Informational Pages')

@section('authorizeContent')
    <div class="card">
        <div class="card-body">
            <div class="card-header">
                <h4 class="card-title">Informational Pages</h4>
            </div>
            <div class="row">
                <div class="col-12">
                    <table class="table table-hover datatable">
                        <thead>
                        <tr>
                            @foreach($typeEntity::INDEX_FIELD as $field)
                                <th>{{ keyToLabel($field) }}</th>
                            @endforeach
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($list as $item)
                            <tr>
                                @foreach($typeEntity::INDEX_FIELD as $field)
                                    <td>{{ $item->getValue($field, '', '') }}</td>
                                @endforeach
                                <td>
                                    <a href="{!! route('admin.page.details', ['type' => get_class_short($item)]) !!}" class="btn btn-outline-primary">View</a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
