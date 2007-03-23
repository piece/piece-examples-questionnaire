<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */

/**
 * PHP versions 4 and 5
 *
 * Copyright (c) 2006-2007 KUBO Atsuhiro <iteman@users.sourceforge.net>,
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 *     * Redistributions of source code must retain the above copyright
 *       notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @package    Piece_Unity
 * @author     KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @copyright  2006-2007 KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    SVN: $Id: PHP.php 701 2007-01-20 18:42:27Z iteman $
 * @link       http://piece-framework.com/piece-unity/
 * @since      File available since Release 0.1.0
 */

require_once 'Piece/Unity/Plugin/Renderer/HTML.php';
require_once 'Piece/Unity/Error.php';

// {{{ Piece_Unity_Plugin_Renderer_PHP

/**
 * A renderer which uses PHP itself as a template engine.
 *
 * @package    Piece_Unity
 * @author     KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @copyright  2006-2007 KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    Release: 0.11.0
 * @link       http://piece-framework.com/piece-unity/
 * @since      Class available since Release 0.1.0
 */
class Piece_Unity_Plugin_Renderer_PHP extends Piece_Unity_Plugin_Renderer_HTML
{

    // {{{ properties

    /**#@+
     * @access public
     */

    /**#@-*/

    /**#@+
     * @access private
     */

    /**#@-*/

    /**#@+
     * @access public
     */

    /**#@-*/

    /**#@+
     * @access private
     */

    // }}}
    // {{{ _initialize()

    /**
     * Defines and initializes extension points and configuration points.
     *
     * @since Method available since Release 0.6.0
     */
    function _initialize()
    {
        parent::_initialize();
        $this->_addConfigurationPoint('templateDirectory');
        $this->_addConfigurationPoint('templateExtension', '.php');
    }

    // }}}
    // {{{ _doRender()

    /**
     * Renders a HTML.
     *
     * @param boolean $isLayout
     */
    function _doRender($isLayout)
    {
        if (!$isLayout) {
            $templateDirectory = $this->getConfiguration('templateDirectory');
            $view = $this->_context->getView();
        } else {
            $templateDirectory = $this->getConfiguration('layoutDirectory');
            $view = $this->getConfiguration('layoutView');
        }

        if (is_null($templateDirectory)) {
            return;
        }

        $file = "$templateDirectory/" . str_replace('_', '/', str_replace('.', '', $view)) . $this->getConfiguration('templateExtension');

        if (!file_exists($file)) {
            Piece_Unity_Error::pushCallback(create_function('$error', 'return ' . PEAR_ERRORSTACK_PUSHANDLOG . ';'));
            Piece_Unity_Error::push(PIECE_UNITY_ERROR_NOT_FOUND,
                                   "The HTML template file [ $file ] not found.",
                                    'warning',
                                    array('plugin' => __CLASS__)
                                   );
            Piece_Unity_Error::popCallback();
            return;
        }

        if (!is_readable($file)) {
            Piece_Unity_Error::pushCallback(create_function('$error', 'return ' . PEAR_ERRORSTACK_PUSHANDLOG . ';'));
            Piece_Unity_Error::push(PIECE_UNITY_ERROR_NOT_READABLE,
                                   "The HTML template file [ $file ] was not readable.",
                                    'warning',
                                    array('plugin' => __CLASS__)
                                   );
            Piece_Unity_Error::popCallback();
            return;
        }

        $viewElement = &$this->_context->getViewElement();
        extract($viewElement->getElements(), EXTR_OVERWRITE | EXTR_REFS);

        if (!include_once $file) {
            Piece_Unity_Error::pushCallback(create_function('$error', 'return ' . PEAR_ERRORSTACK_PUSHANDLOG . ';'));
            Piece_Unity_Error::push(PIECE_UNITY_ERROR_NOT_FOUND,
                                    'The HTML template file [ $file ] not found or was not readable.',
                                    'warning',
                                    array('plugin' => __CLASS__)
                                    );
            Piece_Unity_Error::popCallback();
        }
    }

    // }}}
    // {{{ _prepareFallback()

    /**
     * Prepares another view as a fallback.
     */
    function _prepareFallback()
    {
        $config = &$this->_context->getConfiguration();
        $config->setConfiguration('Renderer_PHP', 'templateDirectory', $this->getConfiguration('fallbackDirectory'));
    }
 
    /**#@-*/

    // }}}
}

// }}}

/*
 * Local Variables:
 * mode: php
 * coding: iso-8859-1
 * tab-width: 4
 * c-basic-offset: 4
 * c-hanging-comment-ender-p: nil
 * indent-tabs-mode: nil
 * End:
 */
?>