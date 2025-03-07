# COMP3512 LAMP Stack Template

This repo is a modified version of the [SprintCube docker-compose-lamp repo](https://github.com/sprintcube/docker-compose-lamp).

The original README for that repo is a useful thing and can be found in this repo as [README.original.md](README.original.md).

## LAMP Details

This particular LAMP stack consists of:
- Ubuntu Linux 20.04.6 LTS (Focal Fossa)
- Apache 2.4.59
- MySQL Server 8.4.0 / Client 8.0.37
- PHP 8.3

## VS Code Extension Details

The following extensions are installed the first time the Codespace is started:

- [Code Spell Checker](https://marketplace.visualstudio.com/items?itemName=streetsidesoftware.code-spell-checker)

    _No excuses for misspellings now. You can add words to the `.cspell.json` file to have a custom dictionary for the repo._

- [Database Client](https://marketplace.visualstudio.com/items?itemName=cweijan.vscode-database-client2)

    _Allows you to do simple DB tasks in a light GUI without the need for an external tool._

- [Live Server](https://marketplace.visualstudio.com/items?itemName=ritwickdey.LiveServer)

    _Provides instant feedback as you save your work, for both PHP and JS development. If you want it to work properly with PHP, you'll need the [browser extension](https://chromewebstore.google.com/detail/live-server-web-extension/fiegdmejfepffgpnejdinekhfieaogmj) as well._ 

- [PHP Debug](https://marketplace.visualstudio.com/items?itemName=xdebug.php-debug)

    _Works with the installed Xdebug module to allow reasonably simple debugging of PHP code._

- [PHP Intelephense](https://marketplace.visualstudio.com/items?itemName=bmewburn.vscode-intelephense-client)

    _A PHP code completer, linter, and formatter. Kinda._

- [Prettier](https://marketplace.visualstudio.com/items?itemName=esbenp.prettier-vscode)

    _Opinionated code formatter for JS._

If you're using a desktop version of VS Code, you may see other extensions as well - it depends on whether you're synchronizing you're settings or not.

## Using This Beastie

### initial Codespace start
The **very first** time your Codespace starts up, you need to enter `./start.sh` (the dot slash are necessary). This runs a script that does the gruntwork necessary to get everything up and running so that you can start coding. The whole process takes about 100 seconds to complete. Subsequent spinups will be significantly faster.

### where to put your work

The document root for this particular LAMP stack is `www/html/public`, which is mapped to the `www/public` folder you see. With simple applications, you can dump all your files there. With more complex applications (like The Project and later labs), *publicly* accessible files should go in `www/public`, but you will put many other files one level "up", directly in `www`.

### database connection

Use the included Database Client extension to create a connection to needed databases.
Use **root** as both the username and password for your connection. 

### various tips

- If both ports (80 and 3306) aren't showing, open up a terminal and run these two commands in this order:
    1. **docker compose down**
    2. **docker compose up -d**

- If things are acting wonky, push all work that you don't want to lose, then try the following things - in order from least drastic to most drastic - stopping when things aren't wonky anymore:
    1. `Ctrl+Shift+P` > Reload Window
    2. `Ctrl+Shift+P` > Rebuild Container > Rebuild button (**Not the `Full Rebuild`!**)
    4. Delete the Codespace, then create a new one. (**Don't forget to push beforehand!**)


## File Details

If you're interested, here's what the different files in this repo are for. If a file/directory isn't mentioned here, it's because I don't think it's important...or don't know what it's for. 😏

- `.devcontainer/devcontainer.json`
    
    Used primarily to install [extensions](#vs-code-extension-details).

- `.vscode/launch.json`
    
    Used when debugging a PHP page; forces use of Xdebug for debugging with the proper settings.

- `.vscode/settings.json`
    
    Sets a variety of Workspace settings. I'm opinionated, so if you want to tinker with this stuff, go right ahead!

- `bin/`
    
    Contains all the Dockerfiles for the PHP+Apache and mySQL/mariaDB containers. If you want to make tweaks with the installations of those services, pop open the files in the directory corresponding to the version you want to tweak and go to town.

- `config/php/php.ini`
    
    Need to have the Xdebug 3 settings here to use Xdebug properly.

- `logs/apache2/*.log`

    The log files for Apache are stored here. They're quite useful sometimes, especially for debugging (esp. **error.log**), so if you're having trouble understanding why a page isn't working, this can be a great place to check.

- `.cscpelljson`

    OK words for CSpell. Opinionated.

- `.eslintrc.json`

    Settings file for eslint. Opinionated.

- `.prettierrc`

    Settings file for Prettier. Opinionated.

- `docker-compose.yml`

    Stitches together php/apache and mysql containers. Modified from the original one provided by the [SprintCube docker-compose-lamp repo](https://github.com/sprintcube/docker-compose-lamp) - stripped out things we don't need for the course.

- `initial.env`

    Fills in all the variables for the `docker-compose.yml` files. Copied to `.env` as part of the `start.sh` script. Not meant to be put into version control. 

- `initial.htaccess`

    Tells Apache to behave in useful ways, without needing to go digging around in Apache configuration files - which aren't accessible in this Codespace anyway. Copied to `www/.htaccess` as part of the `start.sh` script. Not meant to be put into version control.

- `package.json`

    Just here to get Prettier and eslint working correctly.