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

require_once(__DIR__ . "/../HideOrDisableBaseControl.php");  // HideDeaktivLinkBaseControl Klasse

/**
 * HideControl ist die Klasse für das IPS-Modul 'Hide Control'.
 * Erweitert HideOrDisableBaseControl
 * 
 * @package       DynamicVisuControl
 * @author        Michael Tröger <micha@nall-chan.net>
 * @copyright     2016 Michael Tröger
 * @license       https://creativecommons.org/licenses/by-nc-sa/4.0/ CC BY-NC-SA 4.0
 * @version       2.0
 * @example <b>Ohne</b>
 */
class HideControl extends HideOrDisableBaseControl
{

    /**
     * Steuert das Verstecken
     * 
     * @access protected
     * @param int $ObjectID Das Objekt welches manipuliert werden soll.
     * @param bool $Value True wenn $ObjectID Versteckt werden soll, false zum anzeigen.
     */
    protected function SetHiddenOrDisabled(int $ObjectID, bool $Value)
    {
        if (IPS_GetObject($ObjectID)["ObjectIsHidden"] <> $Value)
            IPS_SetHidden($ObjectID, $Value);
    }

}

?>