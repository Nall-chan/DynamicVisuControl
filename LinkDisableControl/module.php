<?

/*
 * @addtogroup dynamicvisucontrol
 * @{
 *
 * @package       DynamicVisuControl
 * @file          module.php
 * @author        Michael Tröger <micha@nall-chan.net>
 * @copyright     2016 Michael Tröger
 * @license       https://creativecommons.org/licenses/by-nc-sa/4.0/ CC BY-NC-SA 4.0
 * @version       2.0
 *
 */

require_once(__DIR__ . "/../LinkHideOrLinkDisableBaseControl.php");  // HideDeaktivLinkBaseControl Klasse

/**
 * LinkDisableControl ist die Klasse für das IPS-Modul 'Link Disable Control'.
 * Erweitert LinkHideOrLinkDisableBaseControl
 * 
 * @package       DynamicVisuControl
 * @author        Michael Tröger <micha@nall-chan.net>
 * @copyright     2016 Michael Tröger
 * @license       https://creativecommons.org/licenses/by-nc-sa/4.0/ CC BY-NC-SA 4.0
 * @version       2.0
 * @example <b>Ohne</b>
 */
class LinkDisableControl extends LinkHideOrLinkDisableBaseControl
{

    /**
     * Steuert das Deaktivieren 
     * 
     * @access protected
     * @param int $ObjectID Das Objekt welches manipuliert werden soll.
     * @param bool $Value True wenn $ObjectID Deaktiviert werden soll, false zum aktivieren.
     */
    protected function SetHiddenOrDisabled(int $ObjectID, bool $Value)
    {
        if (IPS_GetObject($ObjectID)["ObjectIsDisabled"] <> $Value)
            IPS_SetDisabled($ObjectID, $Value);
    }

}

/** @} */