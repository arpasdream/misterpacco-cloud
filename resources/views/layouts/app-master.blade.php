<!DOCTYPE html>
<html lang="it">

<head>

    <title>MisterPacco | Amministrazione</title>
    <!-- HTML5 Shim and Respond.js IE11 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 11]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    <!-- Meta -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="description" content="" />
    <meta name="keywords" content="">
    <meta name="author" content="Regil Lab Srl" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <!-- Favicon icon -->
    <link rel="icon" href="/assets/images/favicon.svg" type="image/x-icon">
    <!-- fontawesome icon -->
    <link rel="stylesheet" href="/assets/fonts/fontawesome/css/fontawesome-all.min.css">
    <!-- animation css -->
    <link rel="stylesheet" href="/assets/plugins/animation/css/animate.min.css">
    <!-- notification css -->
    <link rel="stylesheet" href="/assets/plugins/notification/css/notification.min.css">
    <!-- select2 css -->
    <link rel="stylesheet" href="/assets/plugins/select2/css/select2.min.css">
    <!-- vendor css -->
    <link rel="stylesheet" href="/assets/css/style.css">
    <!-- ckeditor css -->
    <link rel="stylesheet" href="https://cdn.ckeditor.com/ckeditor5/42.0.2/ckeditor5.css" />
    <link rel="stylesheet" href="https://cdn.ckeditor.com/ckeditor5-premium-features/42.0.2/ckeditor5-premium-features.css" />

</head>

<body class="">
<!-- [ Pre-loader ] start -->
<div class="loader-bg">
    <div class="loader-track">
        <div class="loader-fill"></div>
    </div>
</div>
<!-- [ Pre-loader ] End -->

@include('layouts.partials.sidebar')

@include('layouts.partials.header')

<!-- [ Main Content ] start -->
<div class="pcoded-main-container">
	<div class="pcoded-wrapper">
		<div class="pcoded-content">
			<div class="pcoded-inner-content">
				<div class="main-body">
					<div class="page-wrapper">
                        @include('layouts.partials.title')

						@yield('content')
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- [ Main Content ] end -->

<!-- Warning Section start -->
<!-- Older IE warning message -->
<!--[if lt IE 11]>
<div class="ie-warning">
    <h1>Warning!!</h1>
    <p>You are using an outdated version of Internet Explorer, please upgrade
        <br/>to any of the following web browsers to access this website.
    </p>
    <div class="iew-container">
        <ul class="iew-download">
            <li>
                <a href="http://www.google.com/chrome/">
                    <img src="/assets/images/browser/chrome.png" alt="Chrome">
                    <div>Chrome</div>
                </a>
            </li>
            <li>
                <a href="https://www.mozilla.org/en-US/firefox/new/">
                    <img src="/assets/images/browser/firefox.png" alt="Firefox">
                    <div>Firefox</div>
                </a>
            </li>
            <li>
                <a href="http://www.opera.com">
                    <img src="/assets/images/browser/opera.png" alt="Opera">
                    <div>Opera</div>
                </a>
            </li>
            <li>
                <a href="https://www.apple.com/safari/">
                    <img src="/assets/images/browser/safari.png" alt="Safari">
                    <div>Safari</div>
                </a>
            </li>
            <li>
                <a href="http://windows.microsoft.com/en-us/internet-explorer/download-ie">
                    <img src="/assets/images/browser/ie.png" alt="">
                    <div>IE (11 & above)</div>
                </a>
            </li>
        </ul>
    </div>
    <p>Sorry for the inconvenience!</p>
</div>
<![endif]-->
<!-- Warning Section Ends -->

<!-- Required Js -->
<script src="/assets/js/vendor-all.min.js"></script>
<script src="/assets/plugins/bootstrap/js/popper.min.js"></script>
<script src="/assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="/assets/js/pcoded.min.js"></script>
<script src="/assets/js/jquery.validate.min.js"></script>
<script src="/assets/js/additional-methods.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.20.0/localization/messages_it.min.js" integrity="sha512-yYJuLaPqHqlqoDSyLrJKgtrgeS44ElQNYQ+KPeCZ3A1I91/wvNIw4LRenDCOd1Nk/sEyzM3lMeKXGnfMyAWtUA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- ckeditor js -->
<script type="importmap">
    {
        "imports": {
        "ckeditor5": "https://cdn.ckeditor.com/ckeditor5/42.0.2/ckeditor5.js",
        "ckeditor5/": "https://cdn.ckeditor.com/ckeditor5/42.0.2/",
        "ckeditor5-premium-features": "https://cdn.ckeditor.com/ckeditor5-premium-features/42.0.2/ckeditor5-premium-features.js",
        "ckeditor5-premium-features/": "https://cdn.ckeditor.com/ckeditor5-premium-features/42.0.2/"
        }
    }
</script>
<script type="module">
    import {
        ClassicEditor,
        Essentials,
        Paragraph,
        Bold,
        Italic,
        Font,
        Heading,
        Strikethrough,
        Link,
        BlockQuote,
        List,
        CKFinder,
        CKFinderUploadAdapter,
        ImageUpload,
        ImageResizeEditing,
        ImageResizeHandles,
        Image,
        ImageCaption,
        ImageResize,
        ImageStyle,
        ImageToolbar,
        LinkImage

    } from 'ckeditor5';

    ClassicEditor
        .create( document.querySelector( '#editor' ), {
            plugins: [ Essentials, Paragraph, Bold, Italic, Font, Heading, Strikethrough, Link, BlockQuote, List, CKFinder, CKFinderUploadAdapter, ImageUpload, ImageResizeEditing, ImageResizeHandles, Image, ImageToolbar, ImageCaption, ImageStyle, ImageResize, LinkImage ],
            toolbar: {
                items: [
                    'undo', 'redo',
                    '|',
                    'heading',
                    '|',
                    'fontfamily', 'fontsize', 'fontColor', 'fontBackgroundColor',
                    '|',
                    'bold', 'italic', 'strikethrough',
                    '|',
                    'link', 'blockQuote', 'bulletedList', 'numberedList',
                    '|',
                    'uploadImage'

                ],
                shouldNotGroupWhenFull: false
            },
            ckfinder: {
                uploadUrl: '/image-upload?_token=' + $('meta[name="csrf-token"]').attr('content'),
            },
            image: {
                styles: [
                    'alignLeft', 'alignCenter', 'alignRight'
                ],
                resizeUnit: 'px',
                resizeOptions: [
                    {
                        name: 'resizeImage:original',
                        label: 'Originale',
                        value: null
                    },
                    {
                        name: 'resizeImage:custom',
                        value: 'custom',
                        icon: 'custom'
                    },
                    {
                        name: 'resizeImage:1200',
                        label: '1200px',
                        value: '1200'
                    },
                    {
                        name: 'resizeImage:1600',
                        label: '1600px',
                        value: '1600'
                    }
                ],
                toolbar: [
                    'imageStyle:alignLeft', 'imageStyle:alignCenter', 'imageStyle:alignRight',
                    '|',
                    'imageResize',
                    'resizeImage:custom',
                ]
            }
        } )
        .then( editor => {
            window.editor = editor;
        } )
        .catch( error => {
            console.error( error );
        } );
</script>

<!-- am chart js -->
<script src="/assets/plugins/chart-am4/js/core.js"></script>
<script src="/assets/plugins/chart-am4/js/charts.js"></script>
<script src="/assets/plugins/chart-am4/js/animated.js"></script>
<script src="/assets/plugins/chart-am4/js/maps.js"></script>
<script src="/assets/plugins/chart-am4/js/worldLow.js"></script>
<script src="/assets/plugins/chart-am4/js/continentsLow.js"></script>
<script src="/assets/plugins/select2/js/select2.full.min.js"></script>

<!-- dashboard-custom js -->
<script src="/assets/js/pages/dashboard-analytics.js"></script>
<script src="/assets/js/custom.js"></script>

</body>
</html>
