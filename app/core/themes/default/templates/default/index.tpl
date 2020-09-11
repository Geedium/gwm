<!DOCTYPE html>
<html lang="en">
    <head>
        <link rel="stylesheet" href="{{ assetPath }}vendor/css/fontawesome.min.css">
        <link rel="stylesheet" href="{{ assetPath }}vendor/css/Chart.min.css">
        <link rel="stylesheet" href="{{ assetPath }}css/dashboard.css">

        <style>
            section { 
                display: flex;
                justify-content: space-between;
                max-width: calc(100vw - 350px);
                flex-flow: row;
            }
            @media only screen and (max-width: 1024px) {
                section {
                    flex-wrap: wrap;
                }     
            }

            section > div {
                background: #272c33;
                box-shadow: 1px 1px 10px 5px #333c48;
                padding: 4px;
                color: white;
                width: -moz-available;
            }

            section > div > h1 {
                text-align: center;
                display: inline-block;
                position: relative;
            }
            
            .btn {
                padding: 5px;
                background: #868d97;
                border-radius: 3px;
                color: white;
            }

            .btn.green {
                color: #b3e4b3;
                background: #126f12;
            }

            .btn:hover {
                background: rgba(134, 141, 151, 0.75);
            }

            .btn.green:hover {
                background: #126f12d0;
            }
        </style>

        <script src="{{ assetPath }}vendor/js/Chart.min.js"></script>

        <title>Dashboard</title>
    </head>

    <body>
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

        <section>
            <div>
                <h1>Assets</h1>

                <div class="container">
                   <div style="width: 350px; height: 350px;">
                       <canvas id="myCanvas" width="400" height="400">
    
                       </canvas>
    
                       <a class="btn green" href="http://127.0.0.1/dashboard/build">Build Assets</a>
                       <a class="btn" href="/">Configure</a>
                   </div>
                </div>
            </div>

            <div>
                <h1>Clock</h1>
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
                            <button onclick="window.location='/dashboard/articles'">
                                <i class="fas fa-newspaper"></i>
                                <span>Articles</span>
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

        <script src="{{ assetPath }}js/dashboard.generated.js"></script>
    </body>
</html>