<?

/*
 * @addtogroup dynamicvisucontrol
 * @{
 *
 * @package       DynamicVisuControl
 * @file          AllBaseControl.php
 * @author        Michael Tröger <micha@nall-chan.net>
 * @copyright     2016 Michael Tröger
 * @license       https://creativecommons.org/licenses/by-nc-sa/4.0/ CC BY-NC-SA 4.0
 * @version       2.0
 *
 */
require_once(__DIR__ . "/AllBaseControl.php");  // HideDeaktivLinkBaseControl Klasse

/**
 * HideOrDisableBaseControl ist die Basisklasse für alle nicht Link-Module der Library
 * Erweitert HideDeaktivLinkBaseControl
 * 
 * @package       DynamicVisuControl
 * @author        Michael Tröger <micha@nall-chan.net>
 * @copyright     2016 Michael Tröger
 * @license       https://creativecommons.org/licenses/by-nc-sa/4.0/ CC BY-NC-SA 4.0
 * @version       2.0
 * @example <b>Ohne</b>
 * @abstract
 * @property int $TargetID Die IPS-ID des Ziel-Objektes welches versteckt bzw. deaktiviert werden soll.
 */
abstract class HideOrDisableBaseControl extends HideDeaktivLinkBaseControl
{

    /**
     * Interne Funktion des SDK.
     *
     * @access public
     */
    public function Create()
    {
        parent::Create();

        $this->RegisterPropertyInteger("Target", 0);
        $this->RegisterPropertyInteger("TargetType", 1);
        $this->TargetID = 0;
    }

    /**
     * Interne Funktion des SDK.
     *
     * @access public
     */
    public function MessageSink($TimeStamp, $SenderID, $Message, $Data)
    {
        parent::MessageSink($TimeStamp, $SenderID, $Message, $Data);
        switch ($Message)
        {
            case OM_UNREGISTER:
                if ($SenderID != $this->ReadPropertyInteger('Target'))
                    break;
                IPS_SetProperty($this->InstanceID, 'Target', 0);
                IPS_ApplyChanges($this->InstanceID);
                break;
        }
    }

    /**
     * Interne Funktion des SDK.
     *
     * @access public
     */
    public function ApplyChanges()
    {
        parent::ApplyChanges();
        if (IPS_GetKernelRunlevel() <> KR_READY)
            return;
        $OldTargetID = $this->TargetID;
        $NewTargetID = $this->ReadPropertyInteger("Target");
        if ($NewTargetID <> $OldTargetID)
        {
            if ($OldTargetID > 0)
                $this->UnregisterMessage($OldTargetID, OM_UNREGISTER);
            if ($NewTargetID > 0)
                $this->RegisterMessage($NewTargetID, OM_UNREGISTER);
            $this->TargetID = $NewTargetID;
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
        if ($this->ReadPropertyBoolean("Invert"))
            $hidden = !$hidden;

        if ($this->ReadPropertyInteger("Target") == 0)
        {
            trigger_error($this->Translate("Target invalid."), E_USER_NOTICE);
            return;
        }

        $Target = $this->ReadPropertyInteger("Target");

        if (!IPS_ObjectExists($Target))
        {
            trigger_error($this->Translate("Target invalid."), E_USER_NOTICE);
            return;
        }

        if ($this->ReadPropertyInteger("TargetType") == 0)
        {
            $this->SetHiddenOrDisabled($Target, $hidden);
        }
        elseif ($this->ReadPropertyInteger("TargetType") == 1)
        {
            $Source = $this->ReadPropertyInteger("Source");
            $Childs = IPS_GetChildrenIDs($Target);
            foreach ($Childs as $Child)
            {
                if ($Child == $Source)
                    continue;
                if (IPS_GetObject($Child)['ObjectType'] == otLink)
                    if (IPS_GetLink($Child)['TargetID'] == $Source)
                        continue;
                $this->SetHiddenOrDisabled($Child, $hidden);
            }
        }
        else
        {
            trigger_error($this->Translate("Type of target is invalid."), E_USER_NOTICE);
            return;
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

/** @} */