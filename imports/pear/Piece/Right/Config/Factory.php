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
 * @package    Piece_Right
 * @author     KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @copyright  2006-2007 KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    SVN: $Id: Factory.php 331 2007-02-18 14:59:45Z iteman $
 * @link       http://piece-framework.com/piece-right/
 * @since      File available since Release 0.1.0
 */

require_once 'Piece/Right/Config.php';
require_once 'Piece/Right/Error.php';
require_once 'Cache/Lite/File.php';
require_once 'PEAR.php';

if (version_compare(phpversion(), '5.0.0', '<')) {
    require_once 'spyc.php';
} else {
    require_once 'spyc.php5';
}

// {{{ Piece_Right_Config_Factory

/**
 * A factory class for creating Piece_Right_Config objects.
 *
 * @package    Piece_Right
 * @author     KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @copyright  2006-2007 KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    Release: 1.5.0
 * @link       http://piece-framework.com/piece-right/
 * @since      Class available since Release 0.1.0
 */
class Piece_Right_Config_Factory
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

    // }}}
    // {{{ factory()

    /**
     * Creates a Piece_Right_Config object from a configuration file or a
     * cache.
     *
     * @param string $validationSet
     * @param string $configDirectory
     * @param string $cacheDirectory
     * @return Piece_Right_Config
     * @throws PIECE_RIGHT_ERROR_INVALID_CONFIGURATION
     * @throws PIECE_RIGHT_ERROR_NOT_FOUND
     * @static
     */
    function &factory($validationSet = null, $configDirectory = null, $cacheDirectory = null)
    {
        if (is_null($validationSet) || is_null($configDirectory)) {
            $config = &new Piece_Right_Config();
            return $config;
        }

        if (!file_exists($configDirectory)) {
            Piece_Right_Error::pushCallback(create_function('$error', 'return ' . PEAR_ERRORSTACK_PUSHANDLOG . ';'));
            Piece_Right_Error::push(PIECE_RIGHT_ERROR_NOT_FOUND,
                                    "The configuration directory [ $configDirectory ] not found.",
                                    'warning'
                                    );
            Piece_Right_Error::popCallback();

            $config = &new Piece_Right_Config();
            return $config;
        }

        $configFile = "$configDirectory/$validationSet.yaml";

        if (!file_exists($configFile)) {
            Piece_Right_Error::push(PIECE_RIGHT_ERROR_NOT_FOUND,
                                    "The configuration file [ $configFile ] not found."
                                    );
            $return = null;
            return $return;
        }

        if (!is_readable($configFile)) {
            Piece_Right_Error::push(PIECE_RIGHT_ERROR_NOT_READABLE,
                                    "The configuration file [ $configFile ] was not readable."
                                    );
            $return = null;
            return $return;
        }

        if (is_null($cacheDirectory)) {
            $cacheDirectory = './cache';
        }

        if (!file_exists($cacheDirectory)) {
            Piece_Right_Error::pushCallback(create_function('$error', 'return ' . PEAR_ERRORSTACK_PUSHANDLOG . ';'));
            Piece_Right_Error::push(PIECE_RIGHT_ERROR_NOT_FOUND,
                                    "The cache directory [ $cacheDirectory ] not found.",
                                    'warning'
                                    );
            Piece_Right_Error::popCallback();

            $config = &Piece_Right_Config_Factory::_getConfigurationFromFile($configFile);
            if (Piece_Right_Error::hasErrors('exception')) {
                $return = null;
                return $return;
            }

            return $config;
        }

        if (!is_readable($cacheDirectory)
            || !is_writable($cacheDirectory)
            ) {
            Piece_Right_Error::pushCallback(create_function('$error', 'return ' . PEAR_ERRORSTACK_PUSHANDLOG . ';'));
            Piece_Right_Error::push(PIECE_RIGHT_ERROR_NOT_READABLE,
                                    "The cache directory [ $cacheDirectory ] was not readable or writable.",
                                    'warning'
                                    );
            Piece_Right_Error::popCallback();

            $config = &Piece_Right_Config_Factory::_getConfigurationFromFile($configFile);
            if (Piece_Right_Error::hasErrors('exception')) {
                $return = null;
                return $return;
            }

            return $config;
        }

        $config = &Piece_Right_Config_Factory::_getConfiguration($configFile, $cacheDirectory);
        return $config;
    }

    /**#@-*/

    /**#@+
     * @access private
     */

    // }}}
    // {{{ _getConfiguration()

    /**
     * Gets a Piece_Right_Config object from a configuration file or a cache.
     *
     * @param string $masterFile
     * @param string $cacheDirectory
     * @return Piece_Right_Config
     * @throws PIECE_RIGHT_ERROR_INVALID_CONFIGURATION
     * @static
     */
    function &_getConfiguration($masterFile, $cacheDirectory)
    {
        $cache = &new Cache_Lite_File(array('cacheDir' => "$cacheDirectory/",
                                            'masterFile' => $masterFile,
                                            'automaticSerialization' => true,
                                            'errorHandlingAPIBreak' => true)
                                      );

        /*
         * The Cache_Lite class always specifies PEAR_ERROR_RETURN when
         * calling PEAR::raiseError in default.
         */
        $config = $cache->get($masterFile);
        if (PEAR::isError($config)) {
            Piece_Right_Error::pushCallback(create_function('$error', 'return ' . PEAR_ERRORSTACK_PUSHANDLOG . ';'));
            Piece_Right_Error::push(PIECE_RIGHT_ERROR_CANNOT_READ,
                                    "Cannot read the cache file in the directory [ $cacheDirectory ].",
                                    'warning'
                                    );
            Piece_Right_Error::popCallback();

            $config = &Piece_Right_Config_Factory::_getConfigurationFromFile($masterFile);
            if (Piece_Right_Error::hasErrors('exception')) {
                $return = null;
                return $return;
            }

            return $config;
        }

        if (!$config) {
            $config = &Piece_Right_Config_Factory::_getConfigurationFromFile($masterFile);
            if (Piece_Right_Error::hasErrors('exception')) {
                $return = null;
                return $return;
            }

            $result = $cache->save($config);
            if (PEAR::isError($result)) {
                Piece_Right_Error::pushCallback(create_function('$error', 'return ' . PEAR_ERRORSTACK_PUSHANDLOG . ';'));
                Piece_Right_Error::push(PIECE_RIGHT_ERROR_CANNOT_WRITE,
                                        "Cannot write the Piece_Right_Config object to the cache file in the directory [ $cacheDirectory ].",
                                        'warning'
                                        );
                Piece_Right_Error::popCallback();
            }
        }

        return $config;
    }

    // }}}
    // {{{ _getConfigurationFromFile()

    /**
     * Parses the given file and returns a Piece_Right_Config object.
     *
     * @param string $file
     * @return Piece_Right_Config
     * @throws PIECE_RIGHT_ERROR_INVALID_CONFIGURATION
     * @static
     */
    function &_getConfigurationFromFile($file)
    {
        $config = &new Piece_Right_Config();
        $yaml = Spyc::YAMLLoad($file);
        foreach ($yaml as $validation) {
            if (!array_key_exists('name', $validation)) {
                Piece_Right_Error::push(PIECE_RIGHT_ERROR_INVALID_CONFIGURATION,
                                        "A configuration in the configuration file [ $file ] has no 'name' element."
                                        );
                $return = null;
                return $return;
            }

            $config->addField($validation['name']);

            if (array_key_exists('required', $validation)) {
                $config->setRequired($validation['name'],
                                     (array)$validation['required']
                                     );
            }

            if (array_key_exists('filter', $validation)
                && is_array($validation['filter'])
                ) {
                foreach ($validation['filter'] as $filter) {
                    $config->addFilter($validation['name'], $filter);
                }
            }

            if (array_key_exists('validator', $validation)
                && is_array($validation['validator'])
                ) {
                foreach ($validation['validator'] as $validator) {
                    $config->addValidation($validation['name'],
                                           $validator['name'],
                                           (array)@$validator['rule'],
                                           @$validator['message']
                                           );
                }
            }

            if (array_key_exists('watcher', $validation)
                && is_array($validation['watcher'])
                ) {
                $config->setWatcher($validation['name'], $validation['watcher']);
            }

            if (array_key_exists('pseudo', $validation)
                && is_array($validation['pseudo'])
                ) {
                $config->setPseudo($validation['name'], $validation['pseudo']);
            }

            if (array_key_exists('description', $validation)) {
                $config->setDescription($validation['name'],
                                        $validation['description']
                                        );
            }

            if (array_key_exists('forceValidation', $validation)) {
                $config->setForceValidation($validation['name'],
                                            $validation['forceValidation']
                                            );
            }
        }

        return $config;
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