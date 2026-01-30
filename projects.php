<?php include('partials/html.php'); ?>

<head>
    <?php $title = "Projects & Services CRM"; include('partials/title-meta.php'); ?>

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
                
                <?php $subtitle = "CRM"; $title = "Projects & Services Hub"; include('partials/page-title.php'); ?>

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
                                    <div>
                                        <h4 class="card-title mb-1">Commercial CRM Workspace</h4>
                                        <p class="text-muted mb-0">Manage opportunities, projects, services, and billing in one connected workflow.</p>
                                    </div>
                                    <div class="d-flex flex-wrap gap-2">
                                        <a href="crm-deals.php" class="btn btn-primary">New Deal</a>
                                        <a href="ticket-create.php" class="btn btn-info">New Service Ticket</a>
                                        <a href="invoice-create.php" class="btn btn-success">New Invoice</a>
                                    </div>
                                </div>
                                <div class="row g-3">
                                    <div class="col-md-6 col-xl-4">
                                        <div class="card h-100 border">
                                            <div class="card-body">
                                                <h5 class="mb-2">Pipeline & Opportunities</h5>
                                                <p class="text-muted">Track deals from lead to closure and keep the team aligned.</p>
                                                <div class="d-flex flex-wrap gap-2">
                                                    <a href="crm-pipeline.php" class="btn btn-sm btn-primary">View Pipeline</a>
                                                    <a href="crm-opportunities.php" class="btn btn-sm btn-outline-primary">Opportunities</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-xl-4">
                                        <div class="card h-100 border">
                                            <div class="card-body">
                                                <h5 class="mb-2">Leads & Contacts</h5>
                                                <p class="text-muted">Consolidate lead sources and nurture key accounts.</p>
                                                <div class="d-flex flex-wrap gap-2">
                                                    <a href="crm-leads.php" class="btn btn-sm btn-secondary">Leads</a>
                                                    <a href="crm-contacts.php" class="btn btn-sm btn-outline-secondary">Contacts</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-xl-4">
                                        <div class="card h-100 border">
                                            <div class="card-body">
                                                <h5 class="mb-2">Projects & Delivery</h5>
                                                <p class="text-muted">Coordinate timelines, milestones, and team execution.</p>
                                                <div class="d-flex flex-wrap gap-2">
                                                    <a href="pages-timeline.php" class="btn btn-sm btn-info">Project Timeline</a>
                                                    <a href="calendar.php" class="btn btn-sm btn-outline-info">Schedule</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-xl-4">
                                        <div class="card h-100 border">
                                            <div class="card-body">
                                                <h5 class="mb-2">Services & Tickets</h5>
                                                <p class="text-muted">Provide responsive service management with SLA visibility.</p>
                                                <div class="d-flex flex-wrap gap-2">
                                                    <a href="tickets-list.php" class="btn btn-sm btn-warning">Service Desk</a>
                                                    <a href="ticket-create.php" class="btn btn-sm btn-outline-warning">Open Ticket</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-xl-4">
                                        <div class="card h-100 border">
                                            <div class="card-body">
                                                <h5 class="mb-2">Quotes & Billing</h5>
                                                <p class="text-muted">Generate proposals, estimates, and invoices seamlessly.</p>
                                                <div class="d-flex flex-wrap gap-2">
                                                    <a href="crm-proposals.php" class="btn btn-sm btn-success">Proposals</a>
                                                    <a href="invoices.php" class="btn btn-sm btn-outline-success">Invoices</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-xl-4">
                                        <div class="card h-100 border">
                                            <div class="card-body">
                                                <h5 class="mb-2">Campaigns & Activities</h5>
                                                <p class="text-muted">Plan outreach, track engagements, and log activities.</p>
                                                <div class="d-flex flex-wrap gap-2">
                                                    <a href="crm-campaign.php" class="btn btn-sm btn-danger">Campaigns</a>
                                                    <a href="crm-activities.php" class="btn btn-sm btn-outline-danger">Activities</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title mb-3">Project Intake</h5>
                                <p class="text-muted">Capture new project requests and align scope with the CRM pipeline.</p>
                                <form>
                                    <div class="mb-3">
                                        <label class="form-label" for="project-name">Project name</label>
                                        <input type="text" class="form-control" id="project-name" placeholder="Example: Website redesign Q4">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label" for="project-client">Client</label>
                                        <input type="text" class="form-control" id="project-client" placeholder="Client or account">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label" for="project-stage">Pipeline stage</label>
                                        <select class="form-select" id="project-stage">
                                            <option selected>Select stage</option>
                                            <option>Discovery</option>
                                            <option>Proposal Sent</option>
                                            <option>In Negotiation</option>
                                            <option>Won</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label" for="project-desc">Scope summary</label>
                                        <textarea class="form-control" id="project-desc" placeholder="Define deliverables, timeline, and key stakeholders"></textarea>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label" for="project-start">Start date</label>
                                                <input type="date" class="form-control" id="project-start">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label" for="project-end">Target delivery</label>
                                                <input type="date" class="form-control" id="project-end">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex flex-wrap gap-2">
                                        <button type="submit" class="btn btn-primary">Create Project</button>
                                        <a href="crm-deals.php" class="btn btn-outline-primary">Link to Deal</a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title mb-3">Service Request</h5>
                                <p class="text-muted">Standardize service intake for existing customers.</p>
                                <form>
                                    <div class="mb-3">
                                        <label class="form-label" for="service-request">Service type</label>
                                        <select class="form-select" id="service-request">
                                            <option selected>Select service</option>
                                            <option>Implementation</option>
                                            <option>Maintenance</option>
                                            <option>Consulting</option>
                                            <option>Emergency Support</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label" for="service-priority">Priority</label>
                                        <select class="form-select" id="service-priority">
                                            <option selected>Normal</option>
                                            <option>High</option>
                                            <option>Urgent</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label" for="service-owner">Account owner</label>
                                        <input type="text" class="form-control" id="service-owner" placeholder="Owner or team">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label" for="service-notes">Request summary</label>
                                        <textarea class="form-control" id="service-notes" placeholder="Describe the service required"></textarea>
                                    </div>
                                    <div class="d-flex flex-wrap gap-2">
                                        <button type="submit" class="btn btn-warning">Submit Request</button>
                                        <a href="tickets-list.php" class="btn btn-outline-warning">View Tickets</a>
                                    </div>
                                </form>
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
