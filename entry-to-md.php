<?php

$entry        = $argv[1];
$baseFilename = 'entries/'.$entry[0].'/'.$entry;
$texFilename  = $baseFilename.'.tex';
$mdFilename   = $baseFilename.'.md';
$contents     = file_get_contents($texFilename);
$output       = $contents;
$citedEntries = array();
$footnotes    = array();

$output = str_replace(
    array('<',    '>',    '``',      '\'\'',    '`',       '\''),
    array('&lt;', '&gt;', '&#8220;', '&#8221;', '&lsquo;', '&rsquo;'),
    $output
);
$output = preg_replace('/\\\\mainentry\\{(.*?)\\}/s', "**\\1**", $output);

$output = preg_replace_callback(
    '/\\\\citeentry\\{(?<entry>.*?)\\}/s',
    function ($matches) {
        global $citedEntries;

        $entry   = $matches['entry'];
        $entryId = getEntryId($entry);

        $citedEntries[$entryId] = $entry;

        return "[$entry][$entryId]";
    },
    $output
);

$output = preg_replace_callback(
    '/\\\\footnote\\{(?<text>.*?)\\}/s',
    function ($matches) {
        global $footnotes;

        $footnotes[] = $matches['text'];
        $output      = '<sup>'.count($footnotes).'</sup>';

        return $output;
    },
    $output
);

$output .= "\n";

foreach ($footnotes as $index => $text) {
    $output .= ($index+1).'. '.$text."\n";
}

$output .= "\n\n";

foreach ($citedEntries as $entry) {
    $entryId = getEntryId($entry);
    $output .= "[$entryId]: /$entryId.html\n";
}

print "$output\n";

file_put_contents($mdFilename, $output);

function getEntryId($entry)
{
    return strtolower(str_replace(
        array(' '),
        array('-'),
        $entry
    ));
}

