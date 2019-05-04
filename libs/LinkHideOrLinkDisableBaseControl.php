<?php

declare(strict_types=1);
/*
 * @addtogroup dynamicvisucontrol
 * @{
 *
 * @package       DynamicVisuControl
 * @file          AllBaseControl.php
 * @author        Michael Tröger <micha@nall-chan.net>
 * @copyright     2019 Michael Tröger
 * @license       https://creativecommons.org/licenses/by-nc-sa/4.0/ CC BY-NC-SA 4.0
 * @version       3.0
 *
 */
require_once(__DIR__ . '/AllBaseControl.php');  // HideDeaktivLinkBaseControl Klasse

/**
 * LinkHideOrLinkDisableBaseControl ist die Basisklasse für alle Link-Module der Library
 * Erweitert HideDeaktivLinkBaseControl
 *
 * @package       DynamicVisuControl
 * @author        Michael Tröger <micha@nall-chan.net>
 * @copyright     2019 Michael Tröger
 * @license       https://creativecommons.org/licenses/by-nc-sa/4.0/ CC BY-NC-SA 4.0
 * @version       3.0
 * @example <b>Ohne</b>
 * @abstract
 */
abstract class LinkHideOrLinkDisableBaseControl extends HideDeaktivLinkBaseControl
{
    /**
     * Interne Funktion des SDK.
     *
     * @access public
     */
    public function Create()
    {
        parent::Create();
        $this->RegisterPropertyInteger('LinkSource', 0);
    }

    /**
     * Interne Funktion des SDK.
     *
     * @access public
     */
    public function ApplyChanges()
    {
        $this->RefreshLinks();
        parent::ApplyChanges();
    }

    /**
     * Ergänzt fehlenden Links unterhalb der eigenen Instanz zu allen Childs der Quelle.
     * @access private
     */
    private function RefreshLinks()
    {
        if ($this->ReadPropertyInteger('LinkSource') == 0) {
            foreach (IPS_GetChildrenIDs($this->InstanceID) as $Child) {
                if (IPS_GetObject($Child)['ObjectType'] == OBJECTTYPE_LINK) {
                    IPS_DeleteLink($Child);
                }
            }
            return;
        }
        $this->RegisterReference($this->ReadPropertyInteger('LinkSource'));
        $present = [];
        foreach (IPS_GetChildrenIDs($this->InstanceID) as $Child) {
            if (IPS_GetObject($Child)['ObjectType'] == OBJECTTYPE_LINK) {
                $present[] = IPS_GetLink($Child)['TargetID'];
            }
        }

        $create = array_diff(IPS_GetChildrenIDs($this->ReadPropertyInteger('LinkSource')), $present);
        foreach ($create as $Target) {
            if (IPS_GetObject($Target)['ObjectIsHidden']) {
                continue;
            }
            $Link = IPS_CreateLink();
            IPS_SetParent($Link, $this->InstanceID);
            IPS_SetName($Link, IPS_GetName($Target));
            IPS_SetLinkTargetID($Link, $Target);
        }
    }

    /**
     * Steuert das verstecken oder deaktivieren
     *
     * @access protected
     * @param bool $hidden True wenn Ziel(e) versteckt oder deaktiviert werden, false zum anzeigen bzw. aktivieren.
     */
    protected function HideOrDeaktiv(bool $hidden)
    {
        if ($this->ReadPropertyBoolean('Invert')) {
            $hidden = !$hidden;
        }

        // Links erzeugen / prüfen wird nur bei ApplyChanges gemacht
        $Source = $this->ReadPropertyInteger('Source');
        $Childs = IPS_GetChildrenIDs($this->InstanceID);

        foreach ($Childs as $Child) {
            if (IPS_GetObject($Child)['ObjectType'] <> OBJECTTYPE_LINK) {
                continue;
            }
            if (IPS_GetLink($Child)['TargetID'] == $Source) {
                continue;
            }
            $this->SetHiddenOrDisabled($Child, $hidden);
        }
    }

    /**
     * Steuert das verstecken oder deaktivieren
     *
     * @abstract
     * @access protected
     * @param int $ObjectID Das Objekt welches manipuliert werden soll.
     * @param bool $Value True wenn $ObjectID versteckt oder deaktiviert werden, false zum anzeigen bzw. aktivieren.
     */
    abstract protected function SetHiddenOrDisabled(int $ObjectID, bool $Value);
}
