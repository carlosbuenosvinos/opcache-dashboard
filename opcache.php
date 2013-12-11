<?php
if (isset($_GET['invalidate'])) {
    opcache_invalidate($_GET['invalidate'], true);
    header('Location: ' . $_SERVER['PHP_SELF'].'#scripts');
}

if (isset($_GET['reset'])) {
    opcache_reset();
    header('Location: ' . $_SERVER['PHP_SELF'].'#scripts');
}

/**
 * Fetch configuration and status information from OpCache
 */
$config = opcache_get_configuration();
$status = opcache_get_status();

/**
 * Turn bytes into a human readable format
 * @param $bytes
 */
function size_for_humans($bytes)
{
    if ($bytes > 1048576) {
        return sprintf("%.2f&nbsp;MB", $bytes/1048576);
    } elseif ($bytes > 1024) {
        return sprintf("%.2f&nbsp;kB", $bytes/1024);
    } else {
        return sprintf("%d&nbsp;bytes", $bytes);
    }
}

function getOffsetWhereStringsAreEqual($a, $b)
{
    $i = 0;
    while (strlen($a) && strlen($b) && strlen($a) > $i && $a{$i} === $b{$i}) {
        $i++;
    }

    return $i;
}

function getSuggestionMessage($property, $value)
{
    switch ($property) {
        case 'opcache_enabled':
            return $value ? '' : '<span class="glyphicon glyphicon-search"></span> You should enabled opcache';
            break;
        case 'cache_full':
            return $value ? '<span class="glyphicon glyphicon-search"></span> You should increase opcache.memory_consumption' : '';
            break;
        case 'opcache.validate_timestamps':
            return $value ? '<span class="glyphicon glyphicon-search"></span> If you are in a production environment you should disabled it' : '';
            break;
    }

    return '';
}

function getStringFromPropertyAndValue($property, $value)
{
    if ($value === false) {
        return 'false';
    }

    if ($value === true) {
        return 'true';
    }

    switch ($property) {
        case 'used_memory':
        case 'free_memory':
        case 'wasted_memory':
        case 'opcache.memory_consumption':
            return size_for_humans($value);
            break;
        case 'current_wasted_percentage':
        case 'opcache_hit_rate':
            return number_format($value, 2).'%';
            break;
        case 'blacklist_miss_ratio':
            return number_format($value, 2);
            break;
    }

    return $value;
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>OPcache Dashboard - Carlos Buenosvinos (@buenosvinos)</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="http://www.php.net/favicon.ico">
    <link href="//netdna.bootstrapcdn.com/bootstrap/3.0.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { padding-top: 70px; }
        h2 {
            padding-top: 100px;
            margin-top: -100px;
            display: inline-block; /* required for webkit browsers */
        }
    </style>

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="//oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="//oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
</head>
<body data-spy="scroll" data-target="#navbar-opcache">
<a href="https://github.com/carlosbuenosvinos/opcache-dashboard"><img style="position: absolute; top: 50px; right: 0; border: 0;" src="https://s3.amazonaws.com/github/ribbons/forkme_right_gray_6d6d6d.png" alt="Fork me on GitHub"></a>
<nav id="navbar-opcache" class="navbar navbar-default navbar-fixed-top" role="navigation">
    <div class="navbar-header">
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </button>
        <a class="navbar-brand" href="#">Zend OPcache <?= $config['version']['version']?></a>
    </div>

    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
        <ul class="nav navbar-nav">
            <li><a href="#hits">Hits</a></li>
            <li><a href="#memory">Memory</a></li>
            <li><a href="#keys">Keys</a></li>
            <li><a href="#status">Status</a></li>
            <li><a href="#configuration">Configuration</a></li>
            <li><a href="#scripts">Scripts</a></li>
        </ul>
    </div>
</nav>

<div class="container">
    <div class="jumbotron">
        <h1>OPcache Dashboard</h1>
        <h2>by Carlos Buenosvinos (<a href="https://twitter.com/buenosvinos">@buenosvinos</a>)</h2>
        <p>PHP: <?= phpversion() ?> and OPcache: <?= $config['version']['version'] ?></p>
    </div>

    <?php
    $stats = $status['opcache_statistics'];
    $hitRate = round($stats['opcache_hit_rate'], 2);
    ?>
    <h2 id="hits">Hits: <?= $hitRate ?>%</h2>
    <div class="progress progress-striped">
        <div class="progress-bar progress-bar-success" style="width: <?= $hitRate ?>%">
            <span class="sr-only">Hits</span>
        </div>
        <div class="progress-bar progress-bar-danger" style="width: <?= (100 - $hitRate) ?>%">
            <span class="sr-only">Misses</span>
        </div>
    </div>

    <?php
    $mem = $status['memory_usage'];
    $totalMemory = $config['directives']['opcache.memory_consumption'];
    $usedMemory = $mem['used_memory'];
    $freeMemory = $mem['free_memory'];
    $wastedMemory = $mem['wasted_memory'];
    ?>

    <h2 id="memory">Memory: <?= size_for_humans($wastedMemory + $usedMemory) ?> of <?= size_for_humans($totalMemory) ?></h2>
    <div class="progress progress-striped">
        <div class="progress-bar progress-bar-danger" style="width: <?= round(($wastedMemory / $totalMemory) * 100, 0) ?>%">
            <span class="sr-only">Wasted memory</span>
        </div>
        <div class="progress-bar progress-bar-warning" style="width: <?= round(($usedMemory / $totalMemory) * 100, 0) ?>%">
            <span class="sr-only">Used memory</span>
        </div>
        <div class="progress-bar progress-bar-success" style="width: <?= round(($freeMemory / $totalMemory) * 100, 0) ?>%">
            <span class="sr-only">Free memory</span>
        </div>
    </div>

    <?php
    $totalKeys = $stats['max_cached_keys'];
    $usedKeys = $stats['num_cached_keys'];
    $freeKeys = $totalKeys - $usedKeys;
    ?>
    <h2 id="keys">Keys: <?= $usedKeys ?> of <?= $totalKeys ?></h2>
    <div class="progress progress-striped">
        <div class="progress-bar progress-bar-warning" style="width: <?= round(($usedKeys / $totalKeys) * 100, 0) ?>%">
            <span class="sr-only">Used keys</span>
        </div>
        <div class="progress-bar progress-bar-success" style="width: <?= round(($freeKeys / $totalKeys) * 100, 0) ?>%">
            <span class="sr-only">Free keys</span>
        </div>
    </div>

    <h2 id="status">Status</h2>
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <?php
            foreach ($status as $key => $value) {
                if ($key == 'scripts') {
                    continue;
                }

                if (is_array($value)) {
                    foreach ($value as $k => $v) {
                        $v = getStringFromPropertyAndValue($k, $v);
                        $m = getSuggestionMessage($k, $v);
                        ?><tr class="<?= $m ? 'danger' : '' ?>"><th align="left"><?= $k ?></th><td align="right"><?= $v ?></td><td><?= $m ?></td></tr><?php
                    }
                    continue;
                }

                $mess = getSuggestionMessage($key, $value);
                $value = getStringFromPropertyAndValue($key, $value);
                ?><tr class="<?= $mess ? 'danger' : '' ?>"><th align="left"><?= $key ?></th><td align="right"><?= $value ?></td><td><?= $mess ?></td></tr><?php
            }
            ?>
        </table>
    </div>

    <h2 id="configuration">Configuration</h2>
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <?php foreach ($config['directives'] as $key => $value) {
                $mess = getSuggestionMessage($key, $value);
                ?>
                <tr class="<?= $mess ? 'danger' : '' ?>" >
                    <th align="left"><?= $key ?></th>
                    <td align="right"><?= getStringFromPropertyAndValue($key, $value) ?></td>
                    <td align="left"><?= $mess ?></td>
                </tr>
            <?php } ?>
        </table>
    </div>

    <h2 id="scripts">Scripts (<?= count($status["scripts"]) ?>) <a type="button" class="btn btn-success" href="?reset">Reset all</a></h2>
    <table class="table table-striped">
        <tr>
            <th>Options</th>
            <th>Hits</th>
            <th>Memory</th>
            <th>Path</th>
        </tr>
        <?php
        uasort($status['scripts'], function ($a, $b) { return $a['hits'] < $b ['hits']; });

        $offset = null;
        $previousKey = null;
        foreach ($status['scripts'] as $key => $data) {
            $offset = min(
                getOffsetWhereStringsAreEqual(
                    (null === $previousKey) ? $key : $previousKey,
                    $key
                ),
                (null === $offset) ? strlen($key) : $offset
            );
            $previousKey = $key;
        }

        foreach ($status['scripts'] as $key => $data) {
            ?>
            <tr>
                <td><a href="?invalidate=<?= $data['full_path'] ?>">Invalidate</a></td>
                <td><?= $data['hits'] ?></td>
                <td><?= size_for_humans($data['memory_consumption']) ?></td>
                <td><?= substr($data['full_path'], $offset - 1) ?></td>
            </tr>
        <?php } ?>
    </table>
</div>

<script src="//code.jquery.com/jquery.js"></script>
<script src="//netdna.bootstrapcdn.com/bootstrap/3.0.2/js/bootstrap.min.js"></script>
</body>
</html>
