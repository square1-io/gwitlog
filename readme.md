Gwitlog
==============

Make developers eager to read your commit log by styling it like a social media timeline.

Based on this [commitstrip comic](http://www.commitstrip.com/en/2014/08/07/our-cto-has-discovered-an-incredible-way-of-making-developers-read-his-commit-messages-you-wont-even-believe-how-he-did-it/), this tool allows you to take a commit log from a git repo and style it to look like a social media timeline.

### Installation
The package can be installed by adding the "conroyp/gwitlog": "dev-master" package to your project's `composer.json`.

```json
[
    "require": {
        "square1/gwitlog": "0.1.*"
    }
]
```

### Usage

#### Generating the log file

The required format of the git log is generated by running the below command on your repository:

`git log --pretty=format:'%H -%d %s (%ad) <%an:%ae>'`

#### Outputting the timeline to screen

```php
<?php
/**
 * A simple script to read a git log from a file and render the timeline to screen
 */
require 'vendor/autoload.php';

use \Square1\Gwitlog\Renderer as Renderer;

$gwitlog = new Renderer();
// Provide repo name
$gwitlog->setRepoName('Gwitlog');
// Provide remote repo base (Optional, but it allows us link commits back to the web GUI)
// Currently support bitbucket and github urls
$gwitlog->setRemoteHost('https://github.com/square1-io/gwitlog');

// Log file generated based on git log command:
// git log --pretty=format:'%H -%d %s (%ad) <%an:%ae>' > git.log
$gwitlog->setInputFile('git.log');

// Generate output and render to screen
$gwitlog->render();

```

#### Outputting the timeline to file

```php
<?php
/**
 * A simple script to read a git log from a file and render the timeline to a file
 */
require 'vendor/autoload.php';

use \Square1\Gwitlog\Renderer as Renderer;

$gwitlog = new Renderer();
// Provide repo name
$gwitlog->setRepoName('Gwitlog');
// Provide remote repo base (Optional, but it allows us link commits back to the web GUI)
// Currently support bitbucket and github urls
$gwitlog->setRemoteHost('https://github.com/square1-io/gwitlog');

// Log file generated based on git log command:
// git log --pretty=format:'%H -%d %s (%ad) <%an:%ae>' > git.log
$gwitlog->setInputFile('git.log');

// Write to file timeline.html
$gwitlog->outputToFile('timeline.html');

```

#### Reading from stdin rather than a flat file

`Gwitlog` also supports reading from an input stream, allowing the `git log` command to be piped through the script without writing the intermediary `git.log` file.

```php
<?php
/**
 * A simple script to read a git log from stdin and render the timeline to screen
 */
require 'vendor/autoload.php';

use \Square1\Gwitlog\Renderer as Renderer;

$gwitlog = new Renderer();
// Provide repo name
$gwitlog->setRepoName('Gwitlog');
// Provide remote repo base (Optional, but it allows us link commits back to the web GUI)
// Currently support bitbucket and github urls
$gwitlog->setRemoteHost('https://github.com/square1-io/gwitlog');

// Read from input stream
$input = fopen('php://stdin', 'r');
$gwitlog->setInputStream($input);

// Generate output and render to screen
$gwitlog->render();

```

Saving the above as `gwitlog.php` allows us to run:

`git log --pretty=format:'%H -%d %s (%ad) <%an:%ae>' | gwitlog.php > timeline.html`

That leaves a file (`timeline.html`) containing the formatted timeline. This allows things like a post-build hook in a local testing environment to generate a new timeline for the team to review after each successful merge.


#### Customising the output

It's possible to customise the output of the result. We use the [Blade](https://github.com/PhiloNL/Laravel-Blade) templating language, most commonly found in Laravel projects.

The `Renderer` can be given the location of a directory containing customised views. It will expect to find three views here - `header.blade.php`, `gwit.blade.php` and `footer.blade.php`. The call to pass on this directory can be made at any time before the `outputToFile` or `render` functions are called.

```
<?php
/**
 * A simple script to read a git log from stdin and render the timeline to screen, using
 * customised views
 */
require 'vendor/autoload.php';

use \Square1\Gwitlog\Renderer as Renderer;

$gwitlog = new Renderer();
// Provide repo name
$gwitlog->setRepoName('Gwitlog');
// Provide remote repo base (Optional, but it allows us link commits back to the web GUI)
// Currently support bitbucket and github urls
$gwitlog->setRemoteHost('https://github.com/square1-io/gwitlog');

// Use our custom templates
$gwitlog->setViewDirectory(__DIR__ . '/../views/gwitlog');

// Read from input stream
$input = fopen('php://stdin', 'r');
$gwitlog->setInputStream($input);

// Generate output and render to screen
$gwitlog->render();

```

By default, these views shall be cached to `/tmp`. If you have permission issues with your deployment or just wish for all of your project views to be cached in one place, call `setCacheDirectory($path)`. This will update the cache directory used by the renderer.


#### Over-riding default size limits

A maximum of 20,000 commits will be handled by default. If you want to try processing more than this, calling `setMaxEntries($limit)` will increase the processing limit.


### Tests

`phpunit`


### Roadmap

* Parsing regular log output, allowing multi-line commits to be visible over multiple lines
* Handling the output of `--graph` to better attach branch names to all commits on that branch
* Accept url of public project and download git log from there automatically
