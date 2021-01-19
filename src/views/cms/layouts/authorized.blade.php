<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>@yield('metaTitle') | {!! env('PROJECT_NAME') !!}</title>
    <meta name="description" content="@yield('metaDescription')">
    <meta name="keywords" content="@yield('metaKeywords')">

    <link rel="shortcut icon" href="{!! url('/') !!}/favicon.ico"/>

    @include('cms::layouts.part.css')

    @yield('cssCustom')

</head>
<body>

<div class="container-scroller">

    @yield('headerCustom')

    <!-- partial:navbar -->
    @include('cms::layouts.part.navbar')
    <!-- partial -->

    <div class="container-fluid page-body-wrapper">
        <!-- partial:partials/_settings-panel.html -->
        <div class="theme-setting-wrapper">
            <div id="theme-settings" class="settings-panel">
                <i class="settings-close mdi mdi-close"></i>
                <div class="d-flex align-items-center justify-content-between border-bottom">
                    <p class="settings-heading font-weight-bold border-top-0 mb-3 pl-3 pt-0 border-bottom-0 pb-0">Template Skins</p>
                </div>
                <div class="sidebar-bg-options selected" id="sidebar-light-theme">
                    <div class="img-ss rounded-circle bg-light border mr-3"></div>Light</div>
                <div class="sidebar-bg-options" id="sidebar-dark-theme">
                    <div class="img-ss rounded-circle bg-dark border mr-3"></div>Dark</div>
                <p class="settings-heading font-weight-bold mt-2">Header Skins</p>
                <div class="color-tiles mx-0 px-4">
                    <div class="tiles primary"></div>
                    <div class="tiles success"></div>
                    <div class="tiles warning"></div>
                    <div class="tiles danger"></div>
                    <div class="tiles pink"></div>
                    <div class="tiles info"></div>
                    <div class="tiles dark"></div>
                    <div class="tiles default"></div>
                </div>
            </div>
        </div>

        <div id="right-sidebar" class="settings-panel">
            <i class="settings-close mdi mdi-close"></i>
            <div class="d-flex align-items-center justify-content-between border-bottom">
                <p class="settings-heading font-weight-bold border-top-0 mb-3 pl-3 pt-0 border-bottom-0 pb-0">Friends</p>
            </div>
            <ul class="chat-list">
                <li class="list active">
                    <div class="profile">
                        <img src="{!! url('/') !!}/assets/webooshcore/images/faces/face1.jpg" alt="image">
                        <span class="online"></span>
                    </div>
                    <div class="info">
                        <p>Thomas Douglas</p>
                        <p>Available</p>
                    </div>
                    <small class="text-muted my-auto">19 min</small>
                </li>
                <li class="list">
                    <div class="profile">
                        <img src="{!! url('/') !!}/assets/webooshcore/images/faces/face2.jpg" alt="image">
                        <span class="offline"></span>
                    </div>
                    <div class="info">
                        <div class="wrapper d-flex">
                            <p>Catherine</p>
                        </div>
                        <p>Away</p>
                    </div>
                    <div class="badge badge-success badge-pill my-auto mx-2">4</div>
                    <small class="text-muted my-auto">23 min</small>
                </li>
                <li class="list">
                    <div class="profile">
                        <img src="{!! url('/') !!}/assets/webooshcore/images/faces/face3.jpg" alt="image">
                        <span class="online"></span>
                    </div>
                    <div class="info">
                        <p>Daniel Russell</p>
                        <p>Available</p>
                    </div>
                    <small class="text-muted my-auto">14 min</small>
                </li>
                <li class="list">
                    <div class="profile">
                        <img src="{!! url('/') !!}/assets/webooshcore/images/faces/face4.jpg" alt="image">
                        <span class="offline"></span>
                    </div>
                    <div class="info">
                        <p>James Richardson</p>
                        <p>Away</p>
                    </div>
                    <small class="text-muted my-auto">2 min</small>
                </li>
                <li class="list">
                    <div class="profile">
                        <img src="{!! url('/') !!}/assets/webooshcore/images/faces/face5.jpg" alt="image">
                        <span class="online"></span>
                    </div>
                    <div class="info">
                        <p>Madeline Kennedy</p>
                        <p>Available</p>
                    </div>
                    <small class="text-muted my-auto">5 min</small>
                </li>
                <li class="list">
                    <div class="profile">
                        <img src="{!! url('/') !!}/assets/webooshcore/images/faces/face6.jpg" alt="image">
                        <span class="online"></span>
                    </div>
                    <div class="info">
                        <p>Sarah Graves</p>
                        <p>Available</p>
                    </div>
                    <small class="text-muted my-auto">47 min</small>
                </li>
            </ul>
        </div>
        <!-- partial -->

        <!-- partial:sidebar -->
        @include('admin.webooshcore.part.sidebar')
        <!-- partial -->

        <div class="main-panel">

            <div class="content-wrapper">
                @yield('authorizeContent')
            </div>
            <!-- content-wrapper ends -->

            <!-- partial:footer -->
            @include('cms::layouts.part.footer')
            <!-- partial -->
        </div>
        <!-- main-panel ends -->
    </div>
    <!-- page-body-wrapper ends -->
</div>

@include('cms::layouts.part.modal')
@yield('modalCustom')

<script>
    var mainUrl = '{{ url('/') }}';
    var fullImgURL = mainUrl + '/assets/upload/full/';
    var mdImgURL = mainUrl + '/assets/upload/md/';
    var fileUrl = mainUrl + '/assets/upload/file/';
    var saveSessionUrl = '{!! (Route::has('admin.save.session')) ? route('admin.save.session') : '' !!}';
</script>

@include('cms::layouts.part.js')
@yield('jsCustom')

</body>
</html>
