<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-12">
                <div class="page-header-title">
                    <h5 class="flex-sm-fill h3 mt-4">
                        @if(Route::current()->getName()=='home')
                            Benvenuto, <a class="font-w600">{{ Auth::user()->nome }}</a>
                        @else
                            {{ ucwords(str_replace('-', ' ', Route::current()->getName())) }}
                        @endif
                    </h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/"><i class="feather icon-home"></i></a></li>
                    <li class="breadcrumb-item"><a>{{ Route::current()->getName() }}</a></li>
                </ul>
            </div>
        </div>
    </div>
</div>
<!-- [ breadcrumb ] end -->
