<p align="center">
  <img width="277" height="136" src="https://raw.githubusercontent.com/Geedium/Docs/master/GWM/logo.png">
  <p align="center">
    <a href="https://www.php.net/"><img src="https://img.shields.io/badge/language-php-%23787cb5"/></a>&nbsp;
    <a href="https://github.com/Geedium/GWM/blob/master/LICENSE.md"><img alt="GitHub license" src="https://img.shields.io/github/license/Geedium/GWM"></a>
</p>

About Geedium's Website Manager
======
The main goal is to be as strict as possible, making user-friendly experience.

Routing Process
======
1. Client sends request ex. **/dashboard**, **/**, **/homepage**
2. Router finds compatible route in configuration file
3. Router attempts to inject route by using autoloader
4. Route depending on type creates widgets to display
5. Widgets interacts with controllers to get data
6. Controllers interacts with models to find data
7. Widgets are sent by route response with status code