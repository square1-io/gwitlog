Gwitlog
==============

Make developers eager to read your commit log by styling it like a social media timeline

Besed on the comic at http://www.commitstrip.com/en/page/4/.


### Installation
The package can be installed by adding the "conroyp/gwitlog": "dev-master" package to your project's `composer.json`.

```json
[
    "require": {
        "conroyp/gwitlog": "dev-master"
    }
]
```

### Usage

`git log --pretty=format:'%H -%d %s (%ad) <%an:%ae>'`

```php
<?php

require 'vendor/autoload.php';

use Conroyp\Gwitlog;

$gwitlog = new Renderer();
// Set up repo name and remote host (enables linking in output)
$gwitlog->setRepoName('Gwitlog');
$gwitlog->setRemoteHost('https://github.com/conroyp/gwitlog');
// Log file already generated
$gwitlog->setInputFile('git.log');

// Generte output and render to screen
$gwitlog->render();

```

@TODO: Output to file

@TODO: Input from pipe



### Roadmap

* Parsing regular log output, allowing multi-line commits to be visible over multiple lines
* Handling the output of `--graph` to better attach branch names to all commits on that branch