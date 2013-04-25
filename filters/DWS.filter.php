<?php

/**
 * Filter the code to make it compatible with DWS Coding Standards
 *
 * PHP version 5
 *
 * LICENSE: This source file is subject to version 3.0 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_0.txt.  If you did not receive a copy of
 * the PHP License and are unable to obtain it through the web, please
 * send a note to license@php.net so we can mail you a copy immediately.
 * @category   PHP
 * @package PHP_Beautifier
 * @subpackage Filter
 * @author Jim Wigginton <terrafrost@php.net>
 * @copyright  2008 Jim Wigginton
 * @link     http://pear.php.net/package/PHP_Beautifier
 * @license    http://www.gnu.org/licenses/lgpl.html  LGPL
 * @version    CVS: $Id:$
 */

/**
 * Require PEAR_Config
 */
require_once ('PEAR/Config.php');

/**
 * Filter the code to make it compatible with DWS Coding Standards
 *
 * Among other differences from the PEAR Coding Standards, the phpBB coding standards use BSD style indenting.
 *
 * @category   PHP
 * @package PHP_Beautifier
 * @subpackage Filter
 * @author Jim Wigginton <terrafrost@php.net>
 * @copyright  2008 Jim Wigginton
 * @link     http://pear.php.net/package/PHP_Beautifier
 * @license    http://www.gnu.org/licenses/lgpl.html  LGPL
 * @version    Release: 0.0.1
 */
class PHP_Beautifier_Filter_DWS extends PHP_Beautifier_Filter
{
    protected $_sDescription = 'Filter the code to make it compatible with DWS Coding Standards';
    private $_iNestedStringIndex = 0;
    public function __construct(PHP_Beautifier $oBeaut, $aSettings = array())
    {
        parent::__construct($oBeaut, $aSettings);
        $oBeaut->setIndentChar(' ');
        $oBeaut->setIndentNumber(4);
        $oBeaut->setNewLine("\n");
        array_push($oBeaut->aModesAvailable, 'variable_variable');
    }

    function t_open_brace($sTag)
    {
        if ($this->oBeaut->openBraceDontProcess() || $this->oBeaut->isPreviousTokenContent('$')) {
            if ($this->oBeaut->isPreviousTokenContent('$')) {
                $this->oBeaut->setMode('variable_variable');
            }

            $this->oBeaut->add($sTag);
            if ($this->oBeaut->getMode('string_index') && !$this->oBeaut->getMode('double_quote')) {
                $this->_iNestedStringIndex++;
            }
        } else {
            if ($this->oBeaut->getControlSeq() == T_CLASS || $this->oBeaut->getControlSeq() == T_FUNCTION) {
                $this->oBeaut->removeWhiteSpace();
                $this->oBeaut->addNewLineIndent();
                $this->oBeaut->add($sTag);
            } elseif ($this->oBeaut->removeWhiteSpace()) {
                $this->oBeaut->add(' ' . $sTag);
            } else {
                $this->oBeaut->add($sTag);
            }

            $this->oBeaut->incIndent();
            if ($this->oBeaut->getControlSeq() == T_SWITCH) {
                $this->oBeaut->incIndent();
            }

            $this->oBeaut->addNewLineIndent();
        }
    }

    function t_close_brace($sTag)
    {
        if (!$this->oBeaut->getMode('double_quote') && $this->_iNestedStringIndex > 0) {
            $this->_iNestedStringIndex--;
            $this->oBeaut->setMode('string_index');
        }

        if ($this->oBeaut->getMode('string_index') || $this->oBeaut->getMode('double_quote') || $this->oBeaut->getMode('variable_variable')) {
            if ($this->oBeaut->getMode('variable_variable')) {
                $this->oBeaut->unsetMode('variable_variable');
            }

            $this->oBeaut->add($sTag);
        } else {
            if ($this->oBeaut->getControlSeq() == T_SWITCH) {
                $this->oBeaut->decIndent();
            }

            $this->oBeaut->removeWhitespace();
            $this->oBeaut->decIndent();
            $this->oBeaut->addNewLineIndent();
            $this->oBeaut->add($sTag);
            if ($this->oBeaut->isNextTokenContent('else')
                || $this->oBeaut->isNextTokenContent('elseif')
                || $this->oBeaut->isNextTokenContent('catch')) {
                $this->oBeaut->add(' ');
            } else {
                if (!$this->oBeaut->isNextTokenContent('}') && !$this->oBeaut->isNextTokenContent(null)) {
                    $this->oBeaut->addNewLine();
                }

                $this->oBeaut->addNewLineIndent();
            }
        }
    }

    function t_doc_comment($sTag)
    {
        if ($this->oBeaut->getPreviousTokenContent() != '}') {
            $this->oBeaut->removeWhiteSpace();
            $this->oBeaut->addNewLine();
            $this->oBeaut->addNewLineIndent();
        }

        $this->oBeaut->add($sTag);
        $this->oBeaut->addNewLineIndent();
    }

    function t_break($sTag)
    {
        $this->oBeaut->add($sTag);
        if ($this->oBeaut->isNextTokenConstant(T_LNUMBER)) {
            $this->oBeaut->add(' ');
        }
    }

    function t_semi_colon($sTag)
    {
        $this->oBeaut->removeWhitespace();
        $this->oBeaut->add($sTag);
        if ($this->oBeaut->getControlParenthesis() != T_FOR) {
            $this->oBeaut->addNewLineIndent();
        } else {
            $this->oBeaut->add(' ');
        }
    }

    function t_assigment_pre($sTag)
    {
        $this->oBeaut->removeWhiteSpace();
        $this->oBeaut->add(' ' . $sTag . ' ');
    }

    function t_parenthesis_close($sTag)
    {
        if (!$this->oBeaut->isPreviousTokenConstant(T_COMMENT) and !$this->oBeaut->isPreviousTokenConstant(T_END_HEREDOC)) {
            $this->oBeaut->removeWhitespace();
        }

        $this->oBeaut->add($sTag);
        if (!$this->oBeaut->isNextTokenContent(';') && !$this->oBeaut->isNextTokenContent(']') && !$this->oBeaut->isNextTokenContent('}')) {
            $this->oBeaut->add(' ');
        }
    }

    function t_catch($sTag)
    {
        $this->oBeaut->add($sTag . ' ');
    }

    function t_comment($sTag)
    {
        if ($this->oBeaut->removeWhitespace()) {
            if (preg_match("/\r|\n/", $this->oBeaut->getPreviousWhitespace())) {
                $this->oBeaut->addNewLineIndent();
            } else {
                $this->oBeaut->add(' ');
            }
        }

        if (substr($sTag, 0, 2) == '/*') {
            $this->comment_large($sTag);
        } else {
            $this->comment_short($sTag);
        }
    }

    function comment_short($sTag)
    {
        if ($this->oBeaut->isPreviousTokenContent('}')) {
            $this->oBeaut->removeWhiteSpace();
            $this->oBeaut->addNewLine();
            $this->oBeaut->addNewLineIndent();
        }

        $this->oBeaut->add(trim($sTag));
        if (!$this->oBeaut->isNextTokenContent('}') && !$this->oBeaut->isNextTokenConstant(T_DOC_COMMENT)) {
            $this->oBeaut->addNewLineIndent();
        }
    }

    function comment_large($sTag)
    {
        if ($sTag == '/*{{{*/' or $sTag == '/*}}}*/') {
            // folding markers
            $this->oBeaut->add(' ' . $sTag);
            $this->oBeaut->addNewLineIndent();
        } else {
            $aLines = explode("\n", $sTag);
            $allWithAsterisk = true;
            for ($x = 1; $x < (count($aLines) - 1); $x++) {
                if (substr(trim($aLines[$x]), 0, 1) != '*') {
                    $allWithAsterisk = false;
                }
            }

            foreach ($aLines as $sLinea) {
                if (substr(trim($sLinea), 0, 2) == '/*') {
                    $this->oBeaut->add(trim($sLinea));
                } elseif (substr(trim($sLinea), 0, 2) == '*/') {
                    $this->oBeaut->add(trim($sLinea));
                } elseif ($allWithAsterisk) {
                    $this->oBeaut->add(' ' . trim($sLinea));
                } else {
                    if (trim(substr($sLinea, 0, $this->oBeaut->getIndent())) == '') {
                        $this->oBeaut->add(substr($sLinea, $this->oBeaut->getIndent()));
                    } else {
                        $this->oBeaut->add(trim($sLinea));
                    }
                }

                $this->oBeaut->addNewLineIndent();
            }
        }
    }

    function preProcess()
    {
        $this->_iNestedStringIndex = 0;
    }
}
