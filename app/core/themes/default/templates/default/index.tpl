<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" href="vendor/css/fontawesome.min.css">
        <link rel="stylesheet" href="vendor/css/Chart.min.css">
        <link rel="stylesheet" href="css/dashboard.css">

        <script src="vendor/js/Chart.min.js"></script>

        <title>Dashboard</title>
    </head>

    <body>
        <section>
            <div class="navbar">
                <ul>
                    <li>
                        <button>
                            <i class="fas fa-home"></i>
                            <span>Home</span>
                        </button>
                    </li>
                </ul>
            </div>

            <div class="container">
               <div style="width: 350px; height: 350px;">
                   <canvas id="myCanvas" width="400" height="400">

                   </canvas>

                   <button><a href="/dashboard/build">Build Assets</a></button>
               </div>
            </div>
        </section>

        <aside>
            <div class="hAside">
                GWM
            </div>

            <nav>
                <ul>
                    <li>
                        <div>
                            <button onclick="window.location='/dashboard'">
                                <i class="fas fa-project-diagram"></i>
                                <span>Dashboard</span>
                            </button>
                        </div>
                    </li>
                    <li>
                        <div>
                            <button onclick="window.location='/dashboard/analytics'">
                                <i class="fas fa-chart-line"></i>
                                <span>Analytics</span>
                            </button>
                        </div>
                    </li>
                    <li>
                        <div>
                            <button onclick="window.location='/dashboard/users'">
                                <i class="fas fa-user"></i>
                                <span>Users</span>
                            </button>
                        </div>
                    </li>
                    <li>
                        <div>
                            <button onclick="window.location='/dashboard/media'">
                                <i class="fas fa-photo-video"></i>
                                <span>Media</span>
                            </button>
                        </div>
                    </li>
                    <li>
                        <div>
                            <button onclick="window.location='/dashboard/settings'">
                                <i class="fas fa-cogs"></i>
                                <span>Settings</span>
                            </button>
                        </div>
                    </li>
                </ul>
            </nav>
        </aside>

        <script src="js/dashboard.generated.js"></script>
    </body>
</html>