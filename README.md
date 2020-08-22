<p align="center">
  <img width="277" height="136" src="https://raw.githubusercontent.com/Geedium/GWM/master/.github/96d6f2e7e1f705ab5e59c84a6dc009b2.png"><br/>
    <a href="https://www.php.net/"><img src="https://img.shields.io/badge/language-php-%23787cb5"/></a>&nbsp;
    <a href="https://github.com/Geedium/GWM/blob/master/LICENSE.md"><img alt="GitHub license" src="https://img.shields.io/github/license/Geedium/GWM"></a>
</p>

Nginx Quick Start
```properties
location / {
    try_files $uri /index.php$is_args$args;
}
```
