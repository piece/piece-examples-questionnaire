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
 * @version    SVN: $Id: Flexy.php 701 2007-01-20 18:42:27Z iteman $
 * @link       http://pear.php.net/package/HTML_Template_Flexy/
 * @link       http://piece-framework.com/piece-unity/
 * @see        HTML_Template_Flexy
 * @since      File available since Release 0.2.0
 */

require_once 'HTML/Template/Flexy.php';
require_once 'HTML/Template/Flexy/Element.php';
require_once 'PEAR.php';
require_once 'Piece/Unity/Plugin/Renderer/HTML.php';
require_once 'Piece/Unity/Error.php';

// {{{ Piece_Unity_Plugin_Renderer_Flexy

/**
 * A renderer which is based on HTML_Template_Flexy template engine.
 *
 * @package    Piece_Unity
 * @author     KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @copyright  2006-2007 KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    Release: 0.11.0
 * @link       http://pear.php.net/package/HTML_Template_Flexy/
 * @link       http://piece-framework.com/piece-unity/
 * @see        HTML_Template_Flexy
 * @since      Class available since Release 0.2.0
 */
class Piece_Unity_Plugin_Renderer_Flexy extends Piece_Unity_Plugin_Renderer_HTML
{

    // {{{ properties

    /**#@+
     * @access public
     */

    /**#@-*/

    /**#@+
     * @access private
     */

    var $_configurationOptions = array('templateDir' => null,
                                       'compileDir'  => null,
                                       'debug'       => 0
                                       );

    /**#@-*/

    /**#@+
     * @access public
     */

    /**#@-*/

    /**#@+
     * @access private
     */

    // }}}
    // {{{ _createFormElements()

    /**
     * Creates form elements which are passed to
     * HTML_Template_Flexy::outputObject() method from the view elements.
     *
     * @param array $elements
     * @return array
     */
    function _createFormElements($elements)
    {
        $formElements = array();
        $formElementValueKey      = $this->getConfiguration('formElementValueKey');
        $formElementOptionsKey    = $this->getConfiguration('formElementOptionsKey');
        $formElementAttributesKey = $this->getConfiguration('formElementAttributesKey');
        foreach ($elements as $name => $type) {
            $formElements[$name] = &new HTML_Template_Flexy_Element();
            if (!is_array($type)) {
                continue;
            }

            if (array_key_exists($formElementValueKey, $type)) {
                $formElements[$name]->setValue($type[$formElementValueKey]);
            }

            if (array_key_exists($formElementOptionsKey, $type)
                && is_array($type[$formElementOptionsKey])
                ) {
                $formElements[$name]->setOptions($type[$formElementOptionsKey]);
            }

            if (array_key_exists($formElementAttributesKey, $type)
                && is_array($type[$formElementAttributesKey])
                ) {
                $formElements[$name]->setAttributes($type[$formElementAttributesKey]);
            }
        }

        return $formElements;
    }

    // }}}
    // {{{ _getOptions()

    /**
     * Gets an array which contains configuration options for
     * a HTML_Template_Flexy object.
     *
     * @return array
     */
    function _getOptions()
    {
        $options = array('fatalError'      => HTML_TEMPLATE_FLEXY_ERROR_RETURN,
                         'privates'        => true,
                         'globals'         => true,
                         'globalfunctions' => true
                         );

        foreach (array_keys($this->_configurationOptions) as $point) {
            $$point = $this->getConfiguration($point);
            if (!is_null($$point)) {
                $options[$point] = $$point;
            }
        }

        return $options;
    }

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
        $this->_addConfigurationPoint('templateExtension', '.html');
        $this->_addConfigurationPoint('formElementsKey', '_elements');
        $this->_addConfigurationPoint('formElementValueKey', '_value');
        $this->_addConfigurationPoint('formElementOptionsKey', '_options');
        $this->_addConfigurationPoint('formElementAttributesKey', '_attributes');
        foreach ($this->_configurationOptions as $point => $default) {
            $this->_addConfigurationPoint($point, $default);
        }
    }

    // }}}
    // {{{ _doRender()

    /**
     * Renders a HTML.
     *
     * @param boolean $isLayout
     * @throws PIECE_UNITY_ERROR_INVOCATION_FAILED
     */
    function _doRender($isLayout)
    {
        $options = $this->_getOptions();
        if (!$isLayout) {
            $view = $this->_context->getView();
        } else {
            $options['templateDir'] = $this->getConfiguration('layoutDirectory');
            $options['compileDir'] = $this->getConfiguration('layoutCompileDirectory');
            $view = $this->getConfiguration('layoutView');
        }

        $flexy = &new HTML_Template_Flexy($options);
        $file = str_replace('_', '/', str_replace('.', '', $view)) . $this->getConfiguration('templateExtension');
        $resultOfCompile = $flexy->compile($file);
        if (PEAR::isError($resultOfCompile)) {
            if ($flexy->currentTemplate === false) {
                Piece_Unity_Error::pushCallback(create_function('$error', 'return ' . PEAR_ERRORSTACK_PUSHANDLOG . ';'));
                Piece_Unity_Error::pushPEARError($resultOfCompile,
                                                 PIECE_UNITY_ERROR_NOT_FOUND,
                                                 "The HTML template file [ $file ] not found.",
                                                 'warning',
                                                 array('plugin' => __CLASS__)
                                                 );
                Piece_Unity_Error::popCallback();
                return;
            }

            Piece_Unity_Error::pushPEARError($resultOfCompile,
                                             PIECE_UNITY_ERROR_INVOCATION_FAILED,
                                             'Failed to invoke the plugin [ ' . __CLASS__ . ' ].',
                                             'exception',
                                             array('plugin' => __CLASS__)
                                             );
            return;
        }

        $viewElement = &$this->_context->getViewElement();
        $viewElements = $viewElement->getElements();

        $formElements = array();
        $formElementsKey = $this->getConfiguration('formElementsKey');
        if (array_key_exists($formElementsKey, $viewElements)) {
            $formElements = $this->_createFormElements($viewElements[$formElementsKey]);
            unset($viewElements[$formElementsKey]);
        }

        $controller = (object)$viewElements;
        $resultOfOutputObject = $flexy->outputObject($controller, $formElements);
        if (PEAR::isError($resultOfOutputObject)) {
            Piece_Unity_Error::pushPEARError($resultOfOutputObject,
                                             PIECE_UNITY_ERROR_INVOCATION_FAILED,
                                             'Failed to invoke the plugin [ ' . __CLASS__ . ' ].',
                                             'exception',
                                             array('plugin' => __CLASS__)
                                             );
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
        $config->setConfiguration('Renderer_Flexy', 'templateDir', $this->getConfiguration('fallbackDirectory'));
        $config->setConfiguration('Renderer_Flexy', 'compileDir', $this->getConfiguration('fallbackCompileDirectory'));
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