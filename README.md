
QUIQQER Continuous Integration
========

Dokumentationsgenerierung und Continuous Integration für dein PHP Projekt.


Packetname:

    quiqqer/quiqqer-ci


Features
--------

- Documentationsgenerierung (phpdox)
- Continuous Integration
- PHPUnit Intekration
- Copy/Paste Detector
- Static code analysis (PHPDepend)
- Code Metriken


Installation
------------

Bevor Sie das Paket installieren müssen folgende Voraussetzungen gegeben sein:

Voraussetzungen:

- php5-xsl 
- php5-xdebug 
- graphviz 
- php5-intl
- system() shell_exec() muss erlaubt sein
- git

Installation via APT (Ubuntu/Debian):

```bash
sudo apt-get install php5-xsl php5-xdebug graphviz php5-intl libpcre3-dev php-pear
```

Der Paketname ist: quiqqer/quiqqer-ci

*Hint*
If Xdebug is not available on the CLI, though, it currently needs to be installed via PECL. :-/

```bash
sudo apt-get install php5-dev
sudo pecl install xdebug

sudo ln -s /etc/php5/mods-available/xdebug.ini /etc/php5/apache2/conf.d/20-xdebug.ini
sudo ln -s /etc/php5/mods-available/xdebug.ini /etc/php5/cli/conf.d/20-xdebug.ini
```

Mitwirken
----------

- Issue Tracker: https://dev.quiqqer.com/quiqqer/quiqqer-ci/issues
- Source Code: https://dev.quiqqer.com/quiqqer/quiqqer-ci/


Support
-------

Falls Sie ein Fehler gefunden haben oder Verbesserungen wünschen,
Dann können Sie gerne an support@pcsg.de eine E-Mail schreiben.


License
-------


Entwickler
--------
