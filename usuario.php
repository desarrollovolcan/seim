<?php include('partials/html.php'); ?>

<head>
    <?php $title = 'Usuario'; include('partials/title-meta.php'); ?>

    <?php include('partials/head-css.php'); ?>
</head>

<body>
    <!-- Begin page -->
    <div class="wrapper">

        <?php include('partials/menu.php'); ?>

        <!-- ============================================================== -->
        <!-- Start Main Content -->
        <!-- ============================================================== -->

        <div class="content-page">
            <div class="container-fluid">
                <?php $subtitle = 'Gestión'; $title = 'Usuario'; include('partials/page-title.php'); ?>

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex align-items-center justify-content-between">
                                <h5 class="card-title mb-0">Input Example</h5>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="text-muted small">Show Code</span>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="showCodeSwitch">
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6 col-xl-3">
                                        <label class="form-label">Basic Input</label>
                                        <input type="text" class="form-control">
                                    </div>
                                    <div class="col-md-6 col-xl-3">
                                        <label class="form-label">Input with Label</label>
                                        <input type="text" class="form-control" placeholder="Name">
                                    </div>
                                    <div class="col-md-6 col-xl-3">
                                        <label class="form-label">Input with Placeholder</label>
                                        <input type="text" class="form-control" placeholder="Placeholder">
                                    </div>
                                    <div class="col-md-6 col-xl-3">
                                        <label class="form-label">Input with Value</label>
                                        <input type="text" class="form-control" value="Input value">
                                    </div>

                                    <div class="col-md-6 col-xl-3">
                                        <label class="form-label">Readonly Plain Text Input</label>
                                        <input type="text" class="form-control" value="Readonly input" readonly>
                                    </div>
                                    <div class="col-md-6 col-xl-3">
                                        <label class="form-label">Readonly Input</label>
                                        <input type="text" class="form-control" value="Readonly input" readonly>
                                    </div>
                                    <div class="col-md-6 col-xl-3">
                                        <label class="form-label">Disabled Input</label>
                                        <input type="text" class="form-control" value="Disabled input" disabled>
                                    </div>
                                    <div class="col-md-6 col-xl-3">
                                        <label class="form-label">Input with Icon</label>
                                        <div class="app-search">
                                            <input type="text" class="form-control" placeholder="example@gmail.com">
                                            <i data-lucide="mail" class="app-search-icon text-muted"></i>
                                        </div>
                                    </div>

                                    <div class="col-md-6 col-xl-3">
                                        <label class="form-label">Input with Icon Right</label>
                                        <div class="app-search">
                                            <input type="text" class="form-control" placeholder="example@gmail.com">
                                            <i data-lucide="mail" class="app-search-icon text-muted"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-xl-3">
                                        <label class="form-label">Input Date</label>
                                        <input type="text" class="form-control" placeholder="dd/mm/aaaa">
                                    </div>
                                    <div class="col-md-6 col-xl-3">
                                        <label class="form-label">Input Time</label>
                                        <input type="text" class="form-control" placeholder="--:--">
                                    </div>
                                    <div class="col-md-6 col-xl-3">
                                        <label class="form-label">Input Password</label>
                                        <input type="password" class="form-control" value="password">
                                    </div>

                                    <div class="col-md-6 col-xl-3">
                                        <label class="form-label">Example Textarea</label>
                                        <textarea class="form-control" rows="3"></textarea>
                                    </div>
                                    <div class="col-md-6 col-xl-3">
                                        <label class="form-label">Form Text</label>
                                        <input type="text" class="form-control">
                                        <small class="text-muted">Must be 8-20 characters long.</small>
                                    </div>
                                    <div class="col-md-6 col-xl-3">
                                        <label class="form-label">Color Picker</label>
                                        <input type="color" class="form-control form-control-color" value="#1f2d5c">
                                    </div>
                                    <div class="col-md-6 col-xl-3">
                                        <label class="form-label">Input Border Style</label>
                                        <input type="text" class="form-control border-dashed" placeholder="Enter your name">
                                    </div>

                                    <div class="col-md-6 col-xl-3">
                                        <label class="form-label">Datalist example</label>
                                        <input class="form-control" list="datalistOptions" placeholder="Search your country...">
                                        <datalist id="datalistOptions">
                                            <option value="Argentina"></option>
                                            <option value="Chile"></option>
                                            <option value="Colombia"></option>
                                            <option value="México"></option>
                                            <option value="Perú"></option>
                                        </datalist>
                                    </div>
                                    <div class="col-md-6 col-xl-3">
                                        <label class="form-label">Rounded Input</label>
                                        <input type="text" class="form-control rounded-pill" placeholder="Enter your name">
                                    </div>
                                    <div class="col-md-6 col-xl-3">
                                        <label class="form-label">Floating Input</label>
                                        <div class="form-floating">
                                            <input type="text" class="form-control" id="floatingInput" placeholder="name">
                                            <label for="floatingInput">Floating Input</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- ============================================================== -->
        <!-- End Main Content -->
        <!-- ============================================================== -->

        <?php include('partials/footer.php'); ?>
    </div>
    <!-- End page -->

    <?php include('partials/footer-scripts.php'); ?>
</body>
</html>
