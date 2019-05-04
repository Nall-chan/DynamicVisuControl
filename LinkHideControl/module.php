<?php

declare(strict_types=1);
/* SetHiddenOrDisabled
 * @addtogroup dynamicvisucontrol
 * @{
 *
 * @package       DynamicVisuControl
 * @file          module.php
 * @author        Michael Tröger <micha@nall-chan.net>
 * @copyright     2019 Michael Tröger
 * @license       https://creativecommons.org/licenses/by-nc-sa/4.0/ CC BY-NC-SA 4.0
 * @version       3.0
 *
 */

require_once(__DIR__ . '/../libs/LinkHideOrLinkDisableBaseControl.php');  // HideDeaktivLinkBaseControl Klasse

/**
 * LinkHideControl ist die Klasse für das IPS-Modul 'Link Hide Control'.
 * Erweitert LinkHideOrLinkDisableBaseControl
 *
 * @package       DynamicVisuControl
 * @author        Michael Tröger <micha@nall-chan.net>
 * @copyright     2019 Michael Tröger
 * @license       https://creativecommons.org/licenses/by-nc-sa/4.0/ CC BY-NC-SA 4.0
 * @version       3.0
 * @example <b>Ohne</b>
 */
class LinkHideControl extends LinkHideOrLinkDisableBaseControl
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
        if (IPS_GetObject($ObjectID)['ObjectIsHidden'] <> $Value) {
            IPS_SetHidden($ObjectID, $Value);
        }
    }

}

/** @} */
