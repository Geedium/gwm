<!--suppress HtmlDeprecatedAttribute -->
<p align="center">
  <img alt="GWM Logo" width="277" height="136" src="https://raw.githubusercontent.com/Geedium/GWM/master/.github/images/96d6f2e7e1f705ab5e59c84a6dc009b2.png"><br/>
    <a href="https://www.php.net/"><img alt="PHP" src="https://img.shields.io/badge/language-php-%23787cb5"/></a>&nbsp;
    <a href="https://github.com/Geedium/GWM/blob/master/LICENSE.md"><img alt="GitHub license" src="https://img.shields.io/github/license/Geedium/GWM"></a>
    <img alt="GitHub workflow" src="https://img.shields.io/github/workflow/status/Geedium/GWM/PHP%20Composer">
</p>

Quick Start
---

**Command-line interface**

```shell script
php tools/gwm diag - Validates GWM integrity.
php tools/gwm dev - Shortcut to run built-in web server.
php tools/gwm nr - Creates new unique resource.
php tools/gwm sass - Attempts to convert sass to css.
```

**Configurations**

<details>
<summary>Nginx</summary>
<pre lang="language-nginx">
location / {
    try_files $uri /index.php$is_args$args;
}
</pre>
</details>

<details>
<summary>Apache</summary>
<pre lang="ini">
RewriteCond %{HTTP_HOST} !^www.domain.tld$
RewriteRule ^(.*)$ http://www.domain.tld/$1 [R=301,L]
</pre>
</details>