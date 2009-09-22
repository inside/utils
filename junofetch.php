#!/usr/bin/env php
<?php

define('BASE_URL', 'http://www.junodownload.com/');
define('DESTINATION_DIR', '/tmp/');
define('NUMBER_OF_SONGS_FROM_ARTIST_TO_PLAY', 1);
define('JUNOS_URL', 'http://www.junodownload.com/search/' .
                    '?as=1&q=%s&s_search_precision=all' .
                    '&s_search_type=artist&s_search_music=1' .
                    '&s_music_product_type=all&s_genre_id=0000' .
                    '&s_search_merchandise=0&s_show_out_of_stock=0' .
                    '&s_show_digital=0&s_media_type=download' .
                    '&order=date_down');

function get_file($file)
{
    if (($content = file_get_contents($file)) === false)
    {
        echo "Couldn't retrieve the file $file\n";
        exit(1);
    }

    return $content;
}

function get_stdin($q)
{
    echo $q;
    $stdin = fgets(STDIN);
    return substr($stdin, 0, strlen($stdin) - 1);
}

# Main

if ($argc > 1)
{
    $file = $argv[1];
}
else
{
    $file = get_stdin('Enter the juno mailing list link or html file: ');
}

$content = get_file($file);

if (preg_match('`href="([^"]+\.m3u)"`', $content, $matches) === 0)
{
    echo "Couldn't find m3u link\n";
    exit(1);
}

$m3uUrl    = BASE_URL . $matches[1];
$content   = get_file($m3uUrl);
$content   = explode("\n", $content);
$artist    = '';
$trackList = '';
$i         = 1;

foreach ($content as $k => $v)
{
    if (preg_match('`- ([^-]+$)`', $v, $matches) === 0)
    {
        continue;
    }

    $prevArtist = $artist;
    $artist = $matches[1];

    if ($artist == $prevArtist)
    {
        if ($i == NUMBER_OF_SONGS_FROM_ARTIST_TO_PLAY)
        {
            continue;
        }

        $i++;
    }
    else
    {
        $i = 1;
    }

    // Downloads files and tag them
    $remoteFile = $content[$k + 1];
    $localFile  = DESTINATION_DIR . basename($remoteFile);
    $junosUrl   = sprintf(JUNOS_URL, urlencode('"' . $artist . '"'));

    if (file_exists($localFile) === false)
    {
        system(escapeshellcmd('wget ' . $remoteFile . ' -O ' . $localFile));
        system(escapeshellcmd('eyeD3 --comment=:Url:' . $junosUrl . ' ' . $localFile));
    }

    $trackList .= $localFile . ' ';
}

echo 'Downloading of tracks finished.' . "\n";
$stdin = get_stdin('Do you want to start playing them? [y/n]');

if (strtolower($stdin) == 'y')
{
    $trackList = substr($trackList, 0, strlen($trackList) - 1);
    system(escapeshellcmd('mpg123 --long-tag -C ' . $trackList));
}
