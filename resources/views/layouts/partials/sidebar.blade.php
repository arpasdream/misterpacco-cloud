<!-- [ navigation menu ] start -->
<nav class="pcoded-navbar menupos-fixed menu-dark menu-item-icon-style6 ">
    <div class="navbar-wrapper ">
        <div class="navbar-brand header-logo">
            <a href="/" class="b-brand">
                <img src="/assets/images/logo.png" alt="Logo" class="logo-main">
            </a>
            <a class="mobile-menu" id="mobile-collapse" href="#!"><span></span></a>
        </div>
        <div class="navbar-content scroll-div" id="layout-sidenav">
            <ul class="nav pcoded-inner-navbar sidenav-inner">

                <li class="nav-item"><a href="/" class="nav-link"><span class="pcoded-micon"><i class="feather icon-home"></i></span><span class="pcoded-mtext">Dashboard</span></a></li>

                <!--<li data-username="mailing-list" class="nav-item pcoded-hasmenu pcoded-trigger active">
                    <a href="#!" class="nav-link">
                        <span class="pcoded-micon"><i class="feather icon-list"></i></span>
                        <span class="pcoded-mtext">Mailing list</span>
                    </a>
                    <ul class="pcoded-submenu">
                        <li><a href="">Mailing list</a></li>
                        <li><a href="">Crea nuova</a></li>
                    </ul>
                </li>

                <li data-username="newsletter" class="nav-item pcoded-hasmenu pcoded-trigger active">
                    <a href="#!" class="nav-link">
                        <span class="pcoded-micon"><i class="feather icon-mail"></i></span>
                        <span class="pcoded-mtext">Newsletter</span>
                    </a>
                    <ul class="pcoded-submenu">
                        <li><a href="">Newsletters</a></li>
                        <li><a href="">Crea nuova</a></li>
                    </ul>
                </li>

                <li data-username="invio-newsletter" class="nav-item pcoded-hasmenu pcoded-trigger active">
                    <a href="#!" class="nav-link">
                        <span class="pcoded-micon"><i class="feather icon-share"></i></span>
                        <span class="pcoded-mtext">Invio Newsletter</span>
                    </a>
                    <ul class="pcoded-submenu">
                        <li><a href="">Newsletters inviate</a></li>
                        <li><a href="">Invia newsletter</a></li>
                    </ul>
                </li>-->

                <li data-username="spedizioni" class="nav-item pcoded-hasmenu">
                    <a href="#!" class="nav-link">
                        <span class="pcoded-micon"><i class="feather icon-package"></i></span>
                        <span class="pcoded-mtext">Spedizioni</span>
                    </a>
                    <ul class="pcoded-submenu">
                        <li><a href="{{ route('lista-spedizioni') }}">Lista spedizioni</a></li>
                    </ul>
                </li>

                <li data-username="clienti" class="nav-item pcoded-hasmenu">
                    <a href="#!" class="nav-link">
                        <span class="pcoded-micon"><i class="feather icon-users"></i></span>
                        <span class="pcoded-mtext">Clienti</span>
                    </a>
                    <ul class="pcoded-submenu">
                        <li><a href="{{ route('lista-clienti') }}">Lista clienti</a></li>
                        <li><a href="{{ route('inserisci-cliente') }}">Crea nuovo</a></li>
                    </ul>
                </li>

                <li data-username="utenti" class="nav-item pcoded-hasmenu">
                    <a href="#!" class="nav-link">
                        <span class="pcoded-micon"><i class="feather icon-user-check"></i></span>
                        <span class="pcoded-mtext">Utenti</span>
                    </a>
                    <ul class="pcoded-submenu">
                        <li><a href="{{ route('lista-utenti') }}">Utenti</a></li>
                        <li><a href="{{ route('inserisci-utente') }}">Crea nuovo</a></li>
                    </ul>
                </li>

            </ul>

        </div>

    </div>
</nav>
<!-- [ navigation menu ] end -->
