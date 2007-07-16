<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */

/**
 * PHP versions 4 and 5
 *
 * Copyright (c) 2006-2007 Piece Project, All rights reserved.
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
 * @package    Piece_Examples_Questionnaire
 * @copyright  2006-2007 Piece Project
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    SVN: $Id$
 * @since      File available since Release 0.1.0
 */

require_once 'Piece/Unity/Service/FlowAction.php';
require_once 'Piece/Unity/Service/FlexyElement.php';

// {{{ QuestionnaireAction

/**
 * Questionnaire フローのアクションクラス
 *
 * @package    Piece_Examples_Questionnaire
 * @copyright  2006-2007 Piece Project
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    Release: @package_version@
 * @since      Class available since Release 0.1.0
 */
class QuestionnaireAction extends Piece_Unity_Service_FlowAction
{

    // {{{ properties

    /**#@+
     * @access public
     */

    /**#@-*/

    /**#@+
     * @access private
     */

    var $_questionnaireAnswer;

    /**#@-*/

    /**#@+
     * @access public
     */

    function QuestionnaireAction()
    {
        $this->_questionnaireAnswer = &new stdClass();
    }

    function setupForm1()
    {
        $this->_setupForm('Answer1');
    }

    function setupForm2()
    {
        $this->_setupForm('Answer2');
        $flexyElement = &new Piece_Unity_Service_FlexyElement();
        $flexyElement->setOptions('job',
                                  array('' => '選択してください',
                                        '会社員' => '会社員',
                                        '公務員' => '公務員',
                                        '自営業' => '自営業',
                                        '主婦・学生' => '主婦・学生',
                                        'その他' => 'その他')
                                  );
    }

    function setupForm3()
    {
        $this->_setupForm('Answer3');
    }

    function validateAnswer1()
    {
        if ($this->_validate('Answer1')) {
            return 'goDisplayForm2FromProcessAnswer1';
        } else {
            return 'goDisplayForm1FromProcessAnswer1';
        }
    }

    function validateAnswer2()
    {
        if ($this->_validate('Answer2')) {
            return 'goDisplayForm3FromProcessAnswer2';
        } else {
            return 'goDisplayForm2FromProcessAnswer2';
        }
    }

    function validateAnswer3()
    {
        if ($this->_validate('Answer3')) {
            return 'goDisplayConfirmationFromProcessAnswer3';
        } else {
            return 'goDisplayForm3FromProcessAnswer3';
        }
    }

    function setupConfirmation()
    {
        $flexyElement = &new Piece_Unity_Service_FlexyElement();
        $flexyElement->addForm($this->_flow->getView(), $this->_context->getScriptName());

        $viewElement = &$this->_context->getViewElement();
        $viewElement->setElementByRef('questionnaireAnswer', $this->_questionnaireAnswer);
    }

    function registerAnswer()
    {
        return 'goDisplayFinishFromProcessRegister';
    }

    /**#@-*/

    /**#@+
     * @access private
     */

    function _setupForm($validationSetName)
    {
        $flexyElement = &new Piece_Unity_Service_FlexyElement();
        $flexyElement->addForm($this->_flow->getView(), $this->_context->getScriptName());
        $validation = &$this->_context->getValidation();
        if ($validation->hasResults($validationSetName)) {
            $results = &$validation->getResults($validationSetName);
            foreach ($results->getFieldNames() as $field) {
                $flexyElement->setValue($field, $this->_questionnaireAnswer->$field);
            }
        }
    }

    function _validate($validationSetName)
    {
        $validation = $this->_context->getValidation();
        return $validation->validate($validationSetName, $this->_questionnaireAnswer);
    }

    /**#@-*/

    // }}}

}

// }}}

/*
 * Local Variables:
 * mode: php
 * coding: utf-8
 * tab-width: 4
 * c-basic-offset: 4
 * c-hanging-comment-ender-p: nil
 * indent-tabs-mode: nil
 * End:
 */
?>
