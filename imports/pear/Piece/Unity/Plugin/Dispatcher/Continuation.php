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
 * @version    SVN: $Id: Continuation.php 694 2007-01-12 02:13:31Z iteman $
 * @link       http://piece-framework.com/piece-unity/
 * @see        Piece_Flow_Continuation, Piece_Flow
 * @since      File available since Release 0.1.0
 */

require_once 'Piece/Unity/Plugin/Common.php';
require_once 'Piece/Flow/Continuation.php';
require_once 'Piece/Flow/Action/Factory.php';
require_once 'Piece/Unity/Context.php';
require_once 'Piece/Flow/Error.php';
require_once 'Piece/Unity/Error.php';

// {{{ GLOBALS

$GLOBALS['PIECE_UNITY_Continuation_FlowExecutionTicketKey'] = null;
$GLOBALS['PIECE_UNITY_Continuation_FlowNameKey'] = null;
$GLOBALS['PIECE_UNITY_Continuation_FlowName'] = null;

// }}}
// {{{ Piece_Unity_Plugin_Dispatcher_Continuation

/**
 * A dispatcher which dispatches requests to the continuation server based on
 * Piece_Flow.
 *
 * @package    Piece_Unity
 * @author     KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @copyright  2006-2007 KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    Release: 0.11.0
 * @link       http://piece-framework.com/piece-unity/
 * @see        Piece_Flow_Continuation, Piece_Flow
 * @since      Class available since Release 0.1.0
 */
class Piece_Unity_Plugin_Dispatcher_Continuation extends Piece_Unity_Plugin_Common
{

    // {{{ properties

    /**#@+
     * @access public
     */

    /**#@-*/

    /**#@+
     * @access private
     */

    var $_continuation;

    /**#@-*/

    /**#@+
     * @access public
     */

    // }}}
    // {{{ invoke()

    /**
     * Invokes the plugin specific code.
     *
     * Starts a new continuation or continues with the current continuation,
     * and returns a view string.
     *
     * @return string
     * @throws PIECE_UNITY_ERROR_INVALID_CONFIGURATION
     * @throws PIECE_UNITY_ERROR_INVOCATION_FAILED
     */
    function invoke()
    {
        $this->_prepareContinuation();
        if (Piece_Unity_Error::hasErrors('exception')) {
            return;
        }

        $this->_continuation->invoke($this->_context, $this->getConfiguration('bindActionsWithFlowExecution'));
        if (Piece_Flow_Error::hasErrors('exception')) {
            Piece_Unity_Error::push(PIECE_UNITY_ERROR_INVOCATION_FAILED,
                                    'Failed to invoke the plugin [ ' . __CLASS__ . ' ].',
                                    'exception',
                                    array('plugin' => __CLASS__),
                                    Piece_Flow_Error::pop()
                                    );
            return;
        }

        Piece_Unity_Error::pushCallback(create_function('$error', 'return ' . PEAR_ERRORSTACK_PUSHANDLOG . ';'));
        $view = $this->_continuation->getView();
        Piece_Unity_Error::popCallback();
        if (Piece_Flow_Error::hasErrors('exception')) {
            Piece_Unity_Error::push(PIECE_UNITY_ERROR_INVOCATION_FAILED,
                                    'Failed to invoke the plugin [ ' . __CLASS__ . ' ].',
                                    'exception',
                                    array('plugin' => __CLASS__),
                                    Piece_Flow_Error::pop()
                                    );
            return;
        }

        $viewElement = &$this->_context->getViewElement();
        $viewElement->setElement('__flowExecutionTicket', $this->_continuation->getCurrentFlowExecutionTicket());

        $session = &$this->_context->getSession();
        $session->setPreloadCallback('_Dispatcher_Continuation_ActionLoader', array(__CLASS__, 'loadAction'));
        $actionInstances = Piece_Flow_Action_Factory::getInstances();
        foreach (array_keys($actionInstances) as $actionClass) {
            $session->addPreloadClass('_Dispatcher_Continuation_ActionLoader',
                                      $actionClass,
                                      Piece_Unity_Plugin_Dispatcher_Continuation::getFlowName()
                                      );
        }

        return $view;
    }

    // }}}
    // {{{ getEventName()

    /**
     * Gets an event name.
     *
     * @return string
     * @static
     */
    function getEventName()
    {
        $context = &Piece_Unity_Context::singleton();
        return $context->getEventName();
    }

    // }}}
    // {{{ getFlowExecutionTicket()

    /**
     * Gets a flow execution ticket.
     *
     * @return string
     * @static
     */
    function getFlowExecutionTicket()
    {
        $context = &Piece_Unity_Context::singleton();
        $request = &$context->getRequest();
        return $request->hasParameter($GLOBALS['PIECE_UNITY_Continuation_FlowExecutionTicketKey']) ? $request->getParameter($GLOBALS['PIECE_UNITY_Continuation_FlowExecutionTicketKey']) : null;
    }

    // }}}
    // {{{ getFlowName()

    /**
     * Gets a flow name.
     *
     * @return string
     * @static
     */
    function getFlowName()
    {
        if (!is_null($GLOBALS['PIECE_UNITY_Continuation_FlowName'])) {
            return $GLOBALS['PIECE_UNITY_Continuation_FlowName'];
        }

        $context = &Piece_Unity_Context::singleton();
        $request = &$context->getRequest();
        return $request->hasParameter($GLOBALS['PIECE_UNITY_Continuation_FlowNameKey']) ? $request->getParameter($GLOBALS['PIECE_UNITY_Continuation_FlowNameKey']) : null;
    }

    // }}}
    // {{{ loadAction()

    /**
     * Loads an action for preventing that the action become an
     * incomplete class.
     *
     * @param string $class
     * @param string $flowName
     * @static
     */
    function loadAction($class, $flowName)
    {
        if ($flowName == Piece_Unity_Plugin_Dispatcher_Continuation::getFlowName()) {
            Piece_Flow_Action_Factory::load($class);
        }
    }

    // }}}
    // {{{ getContinuationSessionKey()

    /**
     * Gets the session key for a continuation object.
     *
     * @return string
     * @static
     */
    function getContinuationSessionKey()
    {
        return '_continuation';
    }

    // }}}
    // {{{ publish()

    /**
     * Publishes the Piece_Flow_Continuation object as a view element if it
     * exists.
     *
     * @since Method available since Release 0.9.0
     * @throws PIECE_UNITY_ERROR_INVALID_CONFIGURATION
     */
    function publish()
    {
        if (is_null($this->_continuation)) {
            $this->_prepareContinuation();
            if (Piece_Unity_Error::hasErrors('exception')) {
                return;
            }
        }

        $viewElement = &$this->_context->getViewElement();
        $viewElement->setElementByRef('__continuation', $this->_continuation);
    }

    /**#@-*/

    /**#@+
     * @access private
     */

    // }}}
    // {{{ _createContinuation()

    /**
     * Creates a new Piece_Flow_Continuation object and configure it.
     *
     * @return Piece_Flow_Continuation
     * @throws PIECE_UNITY_ERROR_INVALID_CONFIGURATION
     */
    function &_createContinuation()
    {
        $continuation = &new Piece_Flow_Continuation($this->getConfiguration('enableSingleFlowMode'));
        $continuation->setCacheDirectory($this->getConfiguration('cacheDirectory'));
        $continuation->setEventNameCallback(array(__CLASS__, 'getEventName'));
        $continuation->setFlowExecutionTicketCallback(array(__CLASS__, 'getFlowExecutionTicket'));
        $continuation->setFlowNameCallback(array(__CLASS__, 'getFlowName'));

        foreach ($this->getConfiguration('flowDefinitions') as $flowDefinition) {
            Piece_Unity_Error::pushCallback(create_function('$error', 'return ' . PEAR_ERRORSTACK_PUSHANDLOG . ';'));
            $continuation->addFlow($flowDefinition['name'],
                                   $flowDefinition['file'],
                                   $flowDefinition['isExclusive']
                                   );
            Piece_Unity_Error::popCallback();
            if (Piece_Flow_Error::hasErrors('exception')) {
                Piece_Unity_Error::push(PIECE_UNITY_ERROR_INVALID_CONFIGURATION,
                                        'Failed to configure the plugin [ ' . __CLASS__ . ' ].',
                                        'exception',
                                        array('plugin' => __CLASS__),
                                        Piece_Flow_Error::pop()
                                        );
                $return = null;
                return $return;
            }
        }

        return $continuation;
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
        $this->_addConfigurationPoint('actionDirectory');
        $this->_addConfigurationPoint('enableSingleFlowMode', false);
        $this->_addConfigurationPoint('cacheDirectory');
        $this->_addConfigurationPoint('flowDefinitions', array());
        $this->_addConfigurationPoint('flowExecutionTicketKey', '_flowExecutionTicket');
        $this->_addConfigurationPoint('flowNameKey', '_flow');
        $this->_addConfigurationPoint('flowName');
        $this->_addConfigurationPoint('bindActionsWithFlowExecution', true);

        $GLOBALS['PIECE_UNITY_Continuation_FlowExecutionTicketKey'] = $this->getConfiguration('flowExecutionTicketKey');
        $GLOBALS['PIECE_UNITY_Continuation_FlowNameKey'] = $this->getConfiguration('flowNameKey');
        $GLOBALS['PIECE_UNITY_Continuation_FlowName'] = $this->getConfiguration('flowName');
        Piece_Flow_Action_Factory::setActionDirectory($this->getConfiguration('actionDirectory'));

        $viewElement = &$this->_context->getViewElement();
        $viewElement->setElement('__flowExecutionTicketKey', $GLOBALS['PIECE_UNITY_Continuation_FlowExecutionTicketKey']);
        $viewElement->setElement('__flowNameKey', $GLOBALS['PIECE_UNITY_Continuation_FlowNameKey']);
    }

    // }}}
    // {{{ _prepareContinuation()

    /**
     * Sets the Piece_Flow_Continuation object to $_continuation property and
     * the context.
     *
     * @since Method available since Release 0.9.0
     * @throws PIECE_UNITY_ERROR_INVALID_CONFIGURATION
     */
    function _prepareContinuation()
    {
        $session = &$this->_context->getSession();
        $continuation = &$session->getAttribute(Piece_Unity_Plugin_Dispatcher_Continuation::getContinuationSessionKey());
        if (is_null($continuation)) {
            $continuation = &$this->_createContinuation();
            if (Piece_Unity_Error::hasErrors('exception')) {
                return;
            }

            $session->setAttributeByRef(Piece_Unity_Plugin_Dispatcher_Continuation::getContinuationSessionKey(), $continuation);
        }

        $this->_context->setContinuation($continuation);
        $this->_continuation = &$continuation;
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