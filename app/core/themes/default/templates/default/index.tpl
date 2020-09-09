<!DOCTYPE html>
<html>
    <head>
        <style>
            *, ::after, ::before {
               box-sizing: border-box;
               font-kerning: normal;
               text-rendering: optimizeLegibility;
            }

            html, body {
                padding: 0;
                margin: 0;
                height: 100vh;
                overflow-x: hidden;
                font-size: 1em;

                font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
            }
            
            body {
                background: #575a60;
            }

            aside {
                width: 350px;
                position: absolute;
                left: 0;
                bottom: 0;
                top: 0;
                background: #272c33;
            }
            section {
                position: absolute;
                display: block;
                left: 350px;
                right: 0;
                bottom: 0;
                top: 0;
                padding: 2px;
            }
            .container {
                background: #a4a4a4;
                position: relative;
                display: block;
            }

            aside > nav {
                margin-top: 15px;
            }
            
            aside > nav > ul {
                list-style-type: none;
                color: white;
                padding: 0;
            }

            aside > nav > ul > li {
                display: block;
                line-height: 40px;
            }

            aside > nav > ul > li > div {
                width: 350px;
                display: block;
            }

            button {
                border: none;
            }

            @font-face {
                font-family: 'Font Awesome 5 Free';
                font-style: normal;
                font-weight: 400;
                font-display: block;
                src: url("vendor/fonts/fa-regular-400/fa-regular-400.eot");
                src: url("vendor/fonts/fa-regular-400/fa-regular-400.eot?#iefix") format("embedded-opentype"),
                     url("vendor/fonts/fa-regular-400/fa-regular-400.woff2") format("woff2"),
                     url("vendor/fonts/fa-regular-400/fa-regular-400.woff") format("woff"),
                     url("vendor/fonts/fa-regular-400/fa-regular-400.ttf") format("truetype"),
                     url("vendor/fonts/fa-regular-400/fa-regular-400.svg#fontawesome") format("svg"); 
            }
            
            @font-face {
                font-family: 'Font Awesome 5 Free';
                font-style: normal;
                font-weight: 900;
                font-display: block;
                src: url("vendor/fonts/fa-solid-900/fa-solid-900.eot");
                src: url("vendor/fonts/fa-solid-900/fa-solid-900.eot?#iefix") format("embedded-opentype"),
                     url("vendor/fonts/fa-solid-900/fa-solid-900.woff2") format("woff2"),
                     url("vendor/fonts/fa-solid-900/fa-solid-900.woff") format("woff"),
                     url("vendor/fonts/fa-solid-900/fa-solid-900.ttf") format("truetype"),
                     url("vendor/fonts/fa-solid-900/fa-solid-900.svg#fontawesome") format("svg"); 
            }

            .far {
                font-family: 'Font Awesome 5 Free';
                font-weight: 400;
            }

            .fa,
            .fas {
                font-family: 'Font Awesome 5 Free';
                font-weight: 900;
            }

            html, body, h1, h2, h3, h4, h5, h6 { padding: 0; margin: 0; }
            button:focus { outline: none; }
            article, aside,
            details, figcaption, 
            figure, footer, 
            header, hgroup,
            main, menu,
            nav, section,
            summary { display: block; }
            button { border: none; background: transparent; }
            button:hover { cursor: pointer; }
            button:focus { outline: none; }
            div { user-select: none; }
            div::selection { background:transparent; }
            li { list-style-type: none; }

            aside > nav > ul > li > div > button {
                width: inherit;
                display: block;
                font-size: 1.25em;
                border: none;
                color: white;
                padding: 4px;
            }

            aside > nav > ul > li > div > button > i {
                position: relative;
                left: 30px;
                width: 35px;
                margin-right: 15px;
                float: left;
            }

            aside > nav > ul > li > div > button > span {
                position: relative;
                float: left;
                left: 65px;
            }

            aside > nav > ul > li > div > button:hover {
                background: #373e48;
                color: #798597;
            }
            
            a, a:hover, a:visited, a:link {
                color: white;
                text-decoration: none;
            }

            .hAside {
                position: relative;
                top: 15px;
                left: 15px;
                width: 320px;
                height: 150px;
                font-size: larger;
                color: white;
            }

            .navbar {
                position: absolute;
                left: 0;
                right: 0;
                top: 0;
                background: #a3a3a3;
                height: 45px;
            }
            
        </style>

        <link rel="stylesheet" href="vendor/css/fontawesome.min.css">
        <link rel="stylesheet" href="vendor/css/Chart.min.css">

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

                   <button>Build Assets</button>
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

        <script>
        var c = document.getElementById("myCanvas");
        var ctx = c.getContext("2d");

        let data = {
            datasets: [{
                data: [10, 20, 30],

                backgroundColor: [
                    'rgba(255, 99, 132, 0.2)',
                    'rgba(54, 162, 235, 0.2)',
                    'rgba(255, 206, 86, 0.2)'
                ],
            }],

            // These labels appear in the legend and in the tooltips when hovering different arcs
            labels: [
                'Red',
                'Yellow',
                'Blue'
            ],
        };

        var myPieChart = new Chart(ctx, {
            type: 'doughnut',
            data: data
        });
        </script>
    </body>
</html>