<?php

declare(strict_types=1);
eval('declare(strict_types=1);namespace dynamicvisucontrol {?>' . file_get_contents(__DIR__ . '/helper/DebugHelper.php') . '}');

/*
 * @addtogroup dynamicvisucontrol
 * @{
 *
 * @package       DynamicVisuControl
 * @file          AllBaseControl.php
 * @author        Michael Tröger <micha@nall-chan.net>
 * @copyright     2018 Michael Tröger
 * @license       https://creativecommons.org/licenses/by-nc-sa/4.0/ CC BY-NC-SA 4.0
 * @version       2.02
 *
 */

/**
 * HideDeaktivLinkBaseControl ist die Basisklasse für alle Module der Library
 * Erweitert IPSModule
 *
 * @package       DynamicVisuControl
 * @author        Michael Tröger <micha@nall-chan.net>
 * @copyright     2016 Michael Tröger
 * @license       https://creativecommons.org/licenses/by-nc-sa/4.0/ CC BY-NC-SA 4.0
 * @version       2.0
 * @example <b>Ohne</b>
 * @abstract
 * @property int $SourceID Die IPS-ID der Variable welche als Event verwendet wird.
 */
abstract class HideDeaktivLinkBaseControl extends IPSModule
{

    use \dynamicvisucontrol\DebugHelper;
    /**
     * Wert einer Eigenschaft aus den InstanceBuffer lesen.
     *
     * @access public
     * @param string $name Propertyname
     * @return mixed Value of Name
     */
    public function __get($name)
    {
        return unserialize($this->GetBuffer($name));
    }

    /**
     * Wert einer Eigenschaft in den InstanceBuffer schreiben.
     *
     * @access public
     * @param string $name Propertyname
     * @param mixed Value of Name
     */
    public function __set($name, $value)
    {
        $this->SetBuffer($name, serialize($value));
    }

    /**
     * Interne Funktion des SDK.
     *
     * @access public
     */
    public function Create()
    {
        parent::Create();

        $this->RegisterPropertyInteger('Source', 0);
        $this->RegisterPropertyInteger('ConditionBoolean', 1);
        $this->RegisterPropertyString('ConditionValue', '');
        $this->RegisterPropertyBoolean('Invert', false);
        $this->SourceID = 0;
    }

    /**
     * Interne Funktion des SDK.
     *
     * @access public
     */
    public function MessageSink($TimeStamp, $SenderID, $Message, $Data)
    {
        switch ($Message) {
            case IPS_KERNELMESSAGE:
                switch ($Data[0]) {
                    case KR_READY:
                        $this->ApplyChanges();
                        break;
                }
                break;
            case VM_UPDATE:
                if ($SenderID != $this->ReadPropertyInteger('Source')) {
                    break;
                }
                $this->Update($Data[0]);
                break;
            case VM_DELETE:
                if ($SenderID != $this->ReadPropertyInteger('Source')) {
                    break;
                }
                IPS_SetProperty($this->InstanceID, 'Source', 0);
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
        $this->RegisterMessage(0, IPS_KERNELMESSAGE);
        if (IPS_GetKernelRunlevel() <> KR_READY) {
            return;
        }
        $OldSourceID = $this->SourceID;
        $NewSourceID = $this->ReadPropertyInteger('Source');
        if ($NewSourceID <> $OldSourceID) {
            $this->UnregisterVariableWatch($OldSourceID);
            $this->RegisterVariableWatch($NewSourceID);
            $this->SourceID = $NewSourceID;
        }
        if ($NewSourceID > 0) {
            $this->Update(GetValue($NewSourceID));
        }
    }

    /**
     * Steuert das verstecken oder deaktivieren
     *
     * @abstract
     * @access protected
     * @param bool $hidden True wenn Ziel(e) versteckt oder deaktiviert werden, false zum anzeigen bzw. aktivieren.
     */
    abstract protected function HideOrDeaktiv(bool $hidden);
    /**
     * Wird durch ein VariablenUpdate aus MessageSink aufgerufen und steuert mit HideOrDeaktiv das Ziel / die Ziele.
     *
     * @access protected
     * @param mixed $Value Der neue Wert der Variable.
     */
    protected function Update($Value)
    {
        $SourceID = $this->ReadPropertyInteger('Source');
        $Source = IPS_GetVariable($SourceID);
        switch ($Source['VariableType']) {
            case VARIABLETYPE_BOOLEAN:
                if ($this->ReadPropertyInteger('ConditionBoolean') == (bool) $Value) {
                    $this->HideOrDeaktiv(true);
                } else {
                    $this->HideOrDeaktiv(false);
                }
                break;
            case VARIABLETYPE_INTEGER:
                if ((int) $this->ReadPropertyString('ConditionValue') == (int) $Value) {
                    $this->HideOrDeaktiv(true);
                } else {
                    $this->HideOrDeaktiv(false);
                }

                break;
            case VARIABLETYPE_FLOAT:
                if ((float) $this->ReadPropertyString('ConditionValue') == (float) $Value) {
                    $this->HideOrDeaktiv(true);
                } else {
                    $this->HideOrDeaktiv(false);
                }

                break;
            case VARIABLETYPE_STRING:
                if ((string) $this->ReadPropertyString('ConditionValue') == (string) $Value) {
                    $this->HideOrDeaktiv(true);
                } else {
                    $this->HideOrDeaktiv(false);
                }

                break;
        }
    }

    /**
     * Registriert eine Überwachung einer Variable.
     *
     * @access protected
     * @param int $VarId IPS-ID der Variable.
     */
    protected function RegisterVariableWatch(int $VarId)
    {
        if ($VarId == 0) {
            return;
        }
        $this->SendDebug('RegisterVM', $VarId, 0);
        $this->RegisterMessage($VarId, VM_DELETE);
        $this->RegisterMessage($VarId, VM_UPDATE);
        $this->RegisterReference($VarId);
    }

    /**
     * Deregistriert eine Überwachung einer Variable.
     *
     * @access protected
     * @param int $VarId IPS-ID der Variable.
     */
    protected function UnregisterVariableWatch(int $VarId)
    {
        if ($VarId == 0) {
            return;
        }

        $this->SendDebug('UnregisterVM', $VarId, 0);
        $this->UnregisterMessage($VarId, VM_DELETE);
        $this->UnregisterMessage($VarId, VM_UPDATE);
        $this->UnregisterReference($VarId);
    }

}

/** @} */
