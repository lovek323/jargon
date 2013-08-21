<?php

class Parser
{
    private $baseFilename = '';
    private $texFilename  = '';
    private $mdFilename   = '';
    private $htmlFilename = '';
    private $contents     = '';
    private $output       = '';

    public function __construct($entry)
    {
        $this->baseFilename = 'entries/'.$entry[0].'/'.$entry;
        $this->texFilename  = $this->baseFilename.'.tex';
        $this->mdFilename   = $this->baseFilename.'.md';
        $this->htmlFilename = $this->baseFilename.'.html';
        $this->contents     = file_get_contents($this->texFilename);
    }

    public function parse()
    {
        $output    = $this->contents;
        $links     = array();
        $footnotes = array();
        $changes   = array();

        /*** HTML SPECIAL CHARACTERS ***/
        $output = str_replace(
            array('<',    '>',    '``',      '\'\'',    '`',       '\''),
            array('&lt;', '&gt;', '&#8220;', '&#8221;', '&lsquo;', '&rsquo;'),
            $output
        );
        $output = preg_replace('/\\\\mainentry\\{(.*?)\\}/s', "**\\1**", $output);

        /*** CITATIONS ***/
        $output = preg_replace_callback(
            '/\\\\cite(?:entry|appendix)\\{(?<text>.*?)\\}/s',
            function ($matches) use (&$links) {
                $text       = $matches['text'];
                $id         = getId($text);
                $links[$id] = $text;

                return "[$text][$id]";
            },
            $output
        );

        /*** CHANGES ***/
        $output = preg_replace_callback(
            '/\\\\Changes\\{(?<text>.*?)\\}/s',
            function ($matches) use (&$changes) {
                $id        = count($changes);
                $changes[] = $matches['text'];
                $output    = ' <span class="changes" data-id="'.$id.'">Changes'.
                    '</span>';

                return $output;
            },
            $output
        );

        /*** FOOTNOTES ***/
        $output = preg_replace_callback(
            '/\\\\footnote\\{(?<text>.*?)\\}/s',
            function ($matches) use (&$footnotes) {
                $footnotes[] = $matches['text'];
                $output      = '<sup>'.count($footnotes).'</sup>';

                return $output;
            },
            $output
        );
        if (count($footnotes)) {
            $output .= "\n";
        }
        foreach ($footnotes as $index => $text) {
            $output .= ($index+1).'. '.$text."\n";
        }

        /*** CITED ENTRY LINKS ***/
        foreach ($links as $text) {
            $linkId  = getId($text);
            $output .= "[$linkId]: /$linkId.html\n";
        }

        /*** CHANGES TEXT ***/
        if (count($changes)) {
            $output .= "\n";
        }
        foreach ($changes as $index => $text) {
            $output .= '<div class="changes-text" data-id="'.$index.'">'.$text.
                '</div>'."\n";
        }

        if (file_exists('footer.md')) {
            $output .= "\n".file_get_contents('footer.md');
        };

        file_put_contents($this->mdFilename, $output);

        exec(
            'perl Markdown.pl '.escapeshellarg($this->mdFilename).' > '.
            escapeshellarg($this->htmlFilename)
        );

        return $output;
    }
}

$entry  = $argv[1];
$parser = new Parser($entry);
$output = $parser->parse();

print "$output\n";

function getId($entry)
{
    return strtolower(str_replace(
        array(' ', "\n"),
        array('-', '-'),
        $entry
    ));
}

