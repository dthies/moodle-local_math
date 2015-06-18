<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/*
 * @package    local_math
 * @subpackage asciimathml
 * @copyright  2014 onwards Daniel Thies
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

global $CFG;

require_once($CFG->libdir.'/filelib.php');

/**
 * Math local filter plugin class.
 * implements methods for formatting and generating images
 */
class math_asciimathml extends local_math_plugin {
    public $filter = 'asciimathml';
    public $imgformat = 'xml';
    public $type = 'math/asciimath';
    protected $a;

    public function __construct() {
        require_once('ASCIIMathPHP-2.0/ASCIIMathPHP-2.0.class.php');
        require('ASCIIMathPHP-2.0/ASCIIMathPHP-2.0.cfg.php');
        $this->a = new ASCIIMathPHP($symbol_arr);
    }

    /**
     * Convert contents of an element from a script to a MathML node
     * for the script cached the database.
     * @param DOMNode $span a DOM Node that contains the script
     */
    public function process($span) {
        $script = $span->nodeValue;
        $span->nodeValue = '';
        $mathjax = $span->ownerDocument->createElement('script');
        $mathjax->nodeValue = $script;
        $mathjax->setAttribute('type', $this->type);
        $this->append_image($span, $script);
    }

    /*
     * Append MathML for the script
     * @param string $span container to be parent
     * @param string $script script to be rendered
     * @return string math node append
     */
    public function append_image($span, $script=null) {
        if (!$script) {
            $script = $span->nodeValue;
            $span->nodeValue = '';
        }
        // We don't need the url, but this method stores the script in database.
        $this->get_image_url($script);

        $filename = md5($script) . '.xml';
        $xml = $this->get_image($filename);
        $dom = $this->load_document($xml);

        // Copy math node to the original document.
        $mathml = $span->ownerDocument->importNode($dom->getElementsByTagName('math')->item(0), true);
        return $span->appendChild($mathml);
    }

    /**
     * Render the script into an xml mathml file
     * @param string $script script to be rendered
     * @param string $filename md5 hash of the script
     * @return string math node
     */
    public function render($script, $filename) {

        $this->a->setExpr($script);
        $this->a->genMathML();

        $xml = $this->get_image_cache() . "/$filename.xml";

        file_put_contents($xml, $this->a->getMathML());

        return $xml;
    }

}


