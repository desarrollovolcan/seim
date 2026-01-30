<?php include('partials/html.php'); ?>

<head>
    <?php $title = "CRM Reports"; include('partials/title-meta.php'); ?>

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
                
                <?php $subtitle = "CRM"; $title = "Reports & Insights"; include('partials/page-title.php'); ?>

                <div class="row">
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
                                    <div>
                                        <h4 class="card-title mb-1">Commercial Performance Overview</h4>
                                        <p class="text-muted mb-0">Monitor revenue, pipeline velocity, and service quality in one view.</p>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-primary">Download Report</button>
                                        <button class="btn btn-outline-primary">Share Snapshot</button>
                                    </div>
                                </div>
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <div class="border rounded-3 p-3 h-100">
                                            <p class="text-muted mb-1">Monthly revenue</p>
                                            <h3 class="mb-0">$128.4k</h3>
                                            <span class="badge text-bg-success">+14%</span>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="border rounded-3 p-3 h-100">
                                            <p class="text-muted mb-1">Open pipeline</p>
                                            <h3 class="mb-0">$392k</h3>
                                            <span class="badge text-bg-info">38 deals</span>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="border rounded-3 p-3 h-100">
                                            <p class="text-muted mb-1">Service SLA</p>
                                            <h3 class="mb-0">94%</h3>
                                            <span class="badge text-bg-warning">3 at risk</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-4">
                                    <h5 class="mb-3">Key Activities</h5>
                                    <div class="table-responsive">
                                        <table class="table align-middle mb-0">
                                            <thead>
                                                <tr>
                                                    <th>Account</th>
                                                    <th>Opportunity</th>
                                                    <th>Owner</th>
                                                    <th>Status</th>
                                                    <th>Next step</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>Andrés Bakery</td>
                                                    <td>Website rebuild</td>
                                                    <td>Camila Díaz</td>
                                                    <td><span class="badge text-bg-primary">Proposal</span></td>
                                                    <td>Review scope</td>
                                                </tr>
                                                <tr>
                                                    <td>Nova Logistics</td>
                                                    <td>ERP support</td>
                                                    <td>Diego Pérez</td>
                                                    <td><span class="badge text-bg-success">Won</span></td>
                                                    <td>Kickoff meeting</td>
                                                </tr>
                                                <tr>
                                                    <td>Cloudline</td>
                                                    <td>Marketing automation</td>
                                                    <td>Andrea López</td>
                                                    <td><span class="badge text-bg-warning">Negotiation</span></td>
                                                    <td>Pricing update</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title mb-3">Insight Filters</h5>
                                <form>
                                    <div class="mb-3">
                                        <label class="form-label" for="report-range">Date range</label>
                                        <select class="form-select" id="report-range">
                                            <option selected>Last 30 days</option>
                                            <option>Quarter to date</option>
                                            <option>Year to date</option>
                                            <option>Custom</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label" for="report-team">Team</label>
                                        <select class="form-select" id="report-team">
                                            <option selected>All teams</option>
                                            <option>Sales</option>
                                            <option>Delivery</option>
                                            <option>Support</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label" for="report-region">Region</label>
                                        <select class="form-select" id="report-region">
                                            <option selected>All regions</option>
                                            <option>North America</option>
                                            <option>Latam</option>
                                            <option>Europe</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label" for="report-owner">Account owner</label>
                                        <input type="text" class="form-control" id="report-owner" placeholder="Search owner">
                                    </div>
                                    <div class="d-flex flex-wrap gap-2">
                                        <button type="submit" class="btn btn-secondary">Apply Filters</button>
                                        <button type="button" class="btn btn-outline-secondary">Reset</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title mb-3">Quick Links</h5>
                                <div class="d-grid gap-2">
                                    <a href="crm-pipeline.php" class="btn btn-outline-primary">Pipeline Board</a>
                                    <a href="crm-activities.php" class="btn btn-outline-info">Activity Log</a>
                                    <a href="tickets-list.php" class="btn btn-outline-warning">Service Desk</a>
                                    <a href="invoices.php" class="btn btn-outline-success">Billing Center</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <!-- container -->

            <?php include('partials/footer.php'); ?>

        </div>

        <!-- ============================================================== -->
        <!-- End of Main Content -->
        <!-- ============================================================== -->

    </div>
    <!-- END wrapper -->

    <?php include('partials/customizer.php'); ?>

    <?php include('partials/footer-scripts.php'); ?>

</body>

</html>
