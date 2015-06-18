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
 * @subpackage displaytex2svg
 * @copyright  2014 onwards Daniel Thies
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

global $CFG;

require_once($CFG->libdir.'/filelib.php');

/**
 * TeX filtering class.
 */
class math_displaytex2svg extends local_math_plugin {
    public $filter = 'displaytex2svg';
    public $imgformat = 'svg';
    public $type = 'math/tex; mode=display';

    public function process($span) {
        $script = $span->nodeValue;
        $class = $span->getAttribute('class');
        $mathjax = $span->ownerDocument->createElement('script');
        $mathjax->nodeValue = htmlentities($script);
        $mathjax->setAttribute('type', 'math/tex; mode=display');
        $preview = $this->append_image($span, $script);
        $displaydiv = $span->parentNode->insertBefore($span->ownerDocument->createElement('div'), $span);
        $displaydiv->setAttribute('style', 'text-align: center');
        $span = $displaydiv->appendChild($span);
        $preview->setAttribute('class', 'MathJax_Preview');
        $span->appendChild($mathjax);

    }

    public function render($script, $filename) {
        $log = null;
        $this->get_temp();

        $pathlatex = get_config('filter_tex', 'pathlatex');
        if (!file_exists($pathlatex)) {
            return;
        }
        $pathlatex = escapeshellarg($pathlatex);

        $doc = $this->construct_latex_document($script);
        // Construct some file paths.
        $tex = $this->get_temp() . "/$filename.tex";
        $tex = $this->get_image_cache() . "/$filename.tex";
        $dvi = $this->get_temp() . "/$filename.dvi";
        $ps = $this->get_temp() . "/$filename.ps";
        $convertformat = 'svg';
        $img = $this->get_image_cache() . "/$filename.{$convertformat}";

        // Turn the latex doc into a .tex file in the temp area.
        $fh = fopen( $tex, 'w' );
        fputs( $fh, $doc );
        fclose( $fh );
        // Run latex on document.
        $command = "{$pathlatex} --interaction=nonstopmode --halt-on-error -output-directory=" . $this->get_temp() . " $tex";
        chdir( $this->temp_dir );
        $this->execute($command, $log);
        // Run dvips (.dvi to .ps).
        $pathdvips = escapeshellarg(get_config('filter_tex', 'pathdvips'));
        $scale = escapeshellarg($this->get_config('scale'));
        $command = "{$pathdvips} -x $scale -E $dvi -o $ps";
        if ($this->execute($command, $log )) {
            return false;
        }

        // Run dvisvgm on document (.ps to .svg).
        $pathdvisvgm = escapeshellarg(get_config('filter_math', 'pathdvisvgm'));
        $command = "{$pathdvisvgm} -E $ps -o $img";
        if ($this->execute($command, $log )) {
            return false;
        }

        return $img;
    }

    /**
     * Turn the bit of TeX into a valid latex document
     * @param string $forumula the TeX formula
     * @param int $fontsize the font size
     * @return string the latex document
     */
    public function construct_latex_document( $formula, $fontsize=12 ) {
        global $CFG;

        // $formula = filter_tex_sanitize_formula($formula);

        $doc = "\\documentclass[{$fontsize}pt]{article}\n";
        $doc .= get_config('filter_tex', 'latexpreamble');
        $doc .= "\\pagestyle{empty}\n";
        $doc .= "\\begin{document}\n";
        if (preg_match("/^[[:space:]]*\\\\begin\\{(gather|align|alignat|multline).?\\}/i", $formula)) {
            $doc .= "$formula\n";
        } else {
            $doc .= "\\[ {$formula} \\]\n";
        }
        $doc .= "\\end{document}\n";
        return $doc;
    }

}


