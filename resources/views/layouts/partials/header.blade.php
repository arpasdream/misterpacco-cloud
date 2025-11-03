<!-- [ Header ] start -->
<header class="navbar pcoded-header navbar-expand-lg navbar-light headerpos-fixed bg-gradient-primary">

    <div class="m-header">
        <a class="mobile-menu" id="mobile-collapse1" href="#!"><span></span></a>
        <a href="/" class="b-brand d-flex align-items-center">
            <img src="/assets/images/logo.png" alt="Logo" class="logo-main" style="height:40px;">
        </a>
    </div>

    <div class="collapse navbar-collapse">
        <ul class="navbar-nav ms-auto">
            <li>
                <div class="dropdown drp-user">
                    <a href="#" class="dropdown-toggle d-flex align-items-center" data-bs-toggle="dropdown">
                        <img src="/assets/images/user/9439678.jpg" class="img-radius me-2" style="height:35px;" alt="User">
                        <span class="h6 mb-0 text-dark">{{ Auth::user()->nome }}</span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end profile-notification">
                        <a href="{{ route('logout.perform') }}" class="dropdown-item">
                            <i class="feather icon-power text-danger"></i> Esci
                        </a>
                    </div>
                </div>
            </li>
        </ul>
    </div>
</header>
<!-- [ Header ] end -->
