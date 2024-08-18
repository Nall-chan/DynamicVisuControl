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
 * DisableControl ist die Klasse für das IPS-Modul 'Disable Control'.
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
class DisableControl extends HideOrDisableBaseControl
{
    protected static $Form = __DIR__ . '/form.json';

    /**
     * Steuert das Deaktivieren.
     *
     * @param int  $ObjectID Das Objekt welches manipuliert werden soll.
     * @param bool $Value    True wenn $ObjectID Deaktiviert werden soll, false aktivieren.
     */
    protected function SetHiddenOrDisabled(int $ObjectID, bool $Value): void
    {
        if (IPS_GetObject($ObjectID)['ObjectIsDisabled'] != $Value) {
            IPS_SetDisabled($ObjectID, $Value);
        }
    }
}

/* @} */
