<?php

declare(strict_types=1);
/*
 * @addtogroup dynamicvisucontrol
 * @{
 *
 * @package       DynamicVisuControl
 * @file          AllBaseControl.php
 * @author        Michael Tröger <micha@nall-chan.net>
 * @copyright     2024 Michael Tröger
 * @license       https://creativecommons.org/licenses/by-nc-sa/4.0/ CC BY-NC-SA 4.0
 * @version       3.70
 *
 */
require_once __DIR__ . '/AllBaseControl.php';  // HideDeaktivLinkBaseControl Klasse

/**
 * HideOrDisableBaseControl ist die Basisklasse für alle nicht Link-Module der Library
 * Erweitert HideDeaktivLinkBaseControl.
 *
 * @author        Michael Tröger <micha@nall-chan.net>
 * @copyright     2024 Michael Tröger
 * @license       https://creativecommons.org/licenses/by-nc-sa/4.0/ CC BY-NC-SA 4.0
 *
 * @version       3.70
 *
 * @example <b>Ohne</b>
 * @abstract
 *
 * @property int $TargetID Die IPS-ID des Ziel-Objektes welches versteckt bzw. deaktiviert werden soll.
 */
abstract class HideOrDisableBaseControl extends HideDeaktivLinkBaseControl
{
    /**
     * Interne Funktion des SDK.
     */
    public function Create(): void
    {
        parent::Create();

        $this->RegisterPropertyInteger('Target', 1);
        $this->RegisterPropertyInteger('TargetType', 1);
        $this->TargetID = 1;
    }

    /**
     * Interne Funktion des SDK.
     */
    public function MessageSink(int $TimeStamp, int $SenderID, int $Message, array $Data): void
    {
        parent::MessageSink($TimeStamp, $SenderID, $Message, $Data);
        switch ($Message) {
            case OM_UNREGISTER:
                if ($SenderID == $this->TargetID) {
                    $this->RegisterTarget(1);
                }
                break;
        }
    }

    /**
     * Interne Funktion des SDK.
     */
    public function ApplyChanges(): void
    {
        parent::ApplyChanges();
        if (IPS_GetKernelRunlevel() != KR_READY) {
            return;
        }
        $this->RegisterTarget($this->ReadPropertyInteger('Target'));
    }

    /**
     * Wird aufgerufen wenn der IPS Betriebsbereit wird.
     */
    protected function KernelReady(): void
    {
        parent::KernelReady();
        $this->RegisterTarget($this->ReadPropertyInteger('Target'));
    }

    /**
     * Registriert die neue TriggerVariable.
     */
    protected function RegisterTarget(int $NewTargetID): void
    {
        $OldTargetID = $this->TargetID;
        if ($NewTargetID != $OldTargetID) {
            if ($OldTargetID > 9999) {
                $this->UnregisterMessage($OldTargetID, OM_UNREGISTER);
                $this->UnregisterReference($OldTargetID);
            }
            if ($NewTargetID > 9999) {
                if (IPS_ObjectExists($NewTargetID)) {
                    $this->RegisterMessage($NewTargetID, OM_UNREGISTER);
                    $this->RegisterReference($OldTargetID);
                }
            }
            $this->TargetID = $NewTargetID;
        }
    }

    /**
     * Steuert das verstecken oder deaktivieren.
     *
     * @param bool $hidden True wenn Ziel(e) versteckt oder deaktiviert werden, false zum anzeigen bzw. aktivieren.
     */
    protected function HideOrDeaktiv(bool $hidden): void
    {
        $Target = $this->ReadPropertyInteger('Target');

        if ($Target < 10000) {
            return;
        }

        if (!IPS_ObjectExists($Target)) {
            trigger_error($this->Translate('Target invalid.'), E_USER_NOTICE);
            return;
        }

        if ($this->ReadPropertyBoolean('Invert')) {
            $hidden = !$hidden;
        }

        if ($this->ReadPropertyInteger('TargetType') == 0) {
            $this->SetHiddenOrDisabled($Target, $hidden);
        } elseif ($this->ReadPropertyInteger('TargetType') == 1) {
            $Source = $this->ReadPropertyInteger('Source');
            $Children = IPS_GetChildrenIDs($Target);
            foreach ($Children as $Child) {
                if ($Child == $Source) {
                    continue;
                }
                if (IPS_GetObject($Child)['ObjectType'] == OBJECTTYPE_LINK) {
                    if (IPS_GetLink($Child)['TargetID'] == $Source) {
                        continue;
                    }
                }
                $this->SetHiddenOrDisabled($Child, $hidden);
            }
        } else {
            trigger_error($this->Translate('Type of target is invalid.'), E_USER_NOTICE);
            return;
        }
    }

    /**
     * Steuert das verstecken oder deaktivieren.
     *
     * @abstract
     *
     * @param int  $ObjectID Das Objekt welches manipuliert werden soll.
     * @param bool $Value    True wenn $ObjectID versteckt oder deaktiviert werden, false zum anzeigen bzw. aktivieren.
     */
    abstract protected function SetHiddenOrDisabled(int $ObjectID, bool $Value): void;
}

/* @} */
