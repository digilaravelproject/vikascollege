<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel')</title>
    <!-- Bootstrap 5 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <!-- Bootstrap Icons CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
        }

        /* Sidebar */
        #sidebar-wrapper {
            min-width: 250px;
            max-width: 250px;
            background-color: #343a40;
            color: #fff;
            transition: all 0.3s;
        }

        #sidebar-wrapper .sidebar-heading {
            font-size: 1.5rem;
        }

        #sidebar-wrapper .list-group-item {
            background-color: #343a40;
            color: #fff;
            border: none;
            transition: all 0.2s;
        }

        #sidebar-wrapper .list-group-item:hover {
            background-color: #495057;
            color: #fff;
        }

        #sidebar-wrapper .list-group-item.active {
            background-color: #007bff;
            border-color: #007bff;
        }

        #wrapper.toggled #sidebar-wrapper {
            margin-left: -250px;
        }

        /* Page Content */
        #page-content-wrapper {
            width: 100%;
            padding: 20px;
            transition: all 0.3s;
        }

        /* Navbar */
        .navbar {
            padding: 0.5rem 1rem;
        }

        /* Card Layout */
        .card {
            border-radius: 0.5rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }
    </style>
</head>

<body>
    <div class="d-flex" id="wrapper">

        <!-- Sidebar -->

        @include('layouts.admin.partials.sidebar')

        <!-- /#sidebar-wrapper -->

        <!-- Page Content -->
        <div id="page-content-wrapper">

            <nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom">
                <div class="container-fluid">
                    <button class="btn btn-primary" id="sidebarToggle"><i class="bi bi-list"></i></button>
                    <span class="navbar-brand ms-3">Admin Panel</span>
                </div>
            </nav>

            <div class="container-fluid mt-4">
                <h1 class="mb-4">@yield('page_title', 'Dashboard')</h1>
                <div class="row">
                    @yield('content')
                </div>
            </div>
        </div>
        <!-- /#page-content-wrapper -->

    </div>
    <!-- /#wrapper -->

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>
    <script>
        // Sidebar toggle
        const sidebarToggle = document.getElementById('sidebarToggle');
        const wrapper = document.getElementById('wrapper');
        sidebarToggle.addEventListener('click', () => {
            wrapper.classList.toggle('toggled');
        });
    </script>

</body>

</html>