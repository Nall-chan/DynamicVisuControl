<?php

declare(strict_types=1);
/*
 * @addtogroup dynamicvisucontrol
 * @{
 *
 * @package       DynamicVisuControl
 * @file          module.php
 * @author        Michael Tröger <micha@nall-chan.net>
 * @copyright     2024 Michael Tröger
 * @license       https://creativecommons.org/licenses/by-nc-sa/4.0/ CC BY-NC-SA 4.0
 * @version       3.55:
 *
 */

require_once __DIR__ . '/../libs/HideOrDisableBaseControl.php';  // HideDeaktivLinkBaseControl Klasse

/**
 * HideControl ist die Klasse für das IPS-Modul 'Hide Control'.
 * Erweitert HideOrDisableBaseControl.
 *
 * @author        Michael Tröger <micha@nall-chan.net>
 * @copyright     2024 Michael Tröger
 * @license       https://creativecommons.org/licenses/by-nc-sa/4.0/ CC BY-NC-SA 4.0
 *
 * @version       3.55:
 *
 * @example <b>Ohne</b>
 */
class HideControl extends HideOrDisableBaseControl
{
    protected static $Form = __DIR__ . '/form.json';
    /**
     * Steuert das Verstecken.
     *
     * @param int  $ObjectID Das Objekt welches manipuliert werden soll.
     * @param bool $Value    True wenn $ObjectID Versteckt werden soll, false zum anzeigen.
     */
    protected function SetHiddenOrDisabled(int $ObjectID, bool $Value): void
    {
        if (IPS_GetObject($ObjectID)['ObjectIsHidden'] != $Value) {
            IPS_SetHidden($ObjectID, $Value);
        }
    }
}
