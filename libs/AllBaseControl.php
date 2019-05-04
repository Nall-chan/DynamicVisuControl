<?php

declare(strict_types=1);
eval('declare(strict_types=1);namespace dynamicvisucontrol {?>' . file_get_contents(__DIR__ . '/helper/DebugHelper.php') . '}');
eval('declare(strict_types=1);namespace dynamicvisucontrol {?>' . file_get_contents(__DIR__ . '/helper/BufferHelper.php') . '}');

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

/**
 * HideDeaktivLinkBaseControl ist die Basisklasse für alle Module der Library
 * Erweitert IPSModule
 *
 * @package       DynamicVisuControl
 * @author        Michael Tröger <micha@nall-chan.net>
 * @copyright     2019 Michael Tröger
 * @license       https://creativecommons.org/licenses/by-nc-sa/4.0/ CC BY-NC-SA 4.0
 * @version       3.0
 * @example <b>Ohne</b>
 * @abstract
 * @property int $SourceID Die IPS-ID der Variable welche als Event verwendet wird.
 */
abstract class HideDeaktivLinkBaseControl extends IPSModule
{

    use \dynamicvisucontrol\DebugHelper,
        \dynamicvisucontrol\BufferHelper;
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
            case IPS_KERNELSTARTED:
                $this->KernelReady();
                break;
            case VM_UPDATE:
                if ($SenderID == $this->SourceID) {
                    $this->Update($Data[0]);
                }
                break;
            case VM_DELETE:
                if ($SenderID == $this->SourceID) {
                    $this->RegisterTrigger(0);
                }
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
        $this->RegisterMessage(0, IPS_KERNELSTARTED);
        if (IPS_GetKernelRunlevel() <> KR_READY) {
            return;
        }
        $this->RegisterTrigger($this->ReadPropertyInteger('Source'));
        if ($this->SourceID > 0) {
            $this->Update(GetValue($this->SourceID));
        }
    }

    /**
     * Wird aufgerufen wenn der IPS Betriebsbereit wird.
     */
    protected function KernelReady()
    {
        $this->RegisterTrigger($this->ReadPropertyInteger('Source'));
        if ($this->SourceID > 0) {
            $this->Update(GetValue($this->SourceID));
        }
    }

    /**
     * Registriert die neue TriggerVariable
     */
    protected function RegisterTrigger(int $NewSourceID)
    {
        $OldSourceID = $this->SourceID;
        if ($NewSourceID <> $OldSourceID) {
            if ($OldSourceID > 0) {
                $this->UnregisterVariableWatch($OldSourceID);
            }
            if ($NewSourceID > 0) {
                if (IPS_VariableExists($NewSourceID)) {
                    $this->RegisterVariableWatch($NewSourceID);
                }
            }
            $this->SourceID = $NewSourceID;
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
        $Source = IPS_GetVariable($this->SourceID);
        switch ($Source['VariableType']) {
            case VARIABLETYPE_BOOLEAN:
                $this->HideOrDeaktiv($this->ReadPropertyInteger('ConditionBoolean') == (bool) $Value);
                break;
            case VARIABLETYPE_INTEGER:
                $this->HideOrDeaktiv((int) $this->ReadPropertyString('ConditionValue') == (int) $Value);
                break;
            case VARIABLETYPE_FLOAT:
                $this->HideOrDeaktiv((float) $this->ReadPropertyString('ConditionValue') == (float) $Value);
                break;
            case VARIABLETYPE_STRING:
                $this->HideOrDeaktiv((string) $this->ReadPropertyString('ConditionValue') == (string) $Value);
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
        $this->SendDebug('RegisterVariableWatch', $VarId, 0);
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

        $this->SendDebug('UnregisterVariableWatch', $VarId, 0);
        $this->UnregisterMessage($VarId, VM_DELETE);
        $this->UnregisterMessage($VarId, VM_UPDATE);
        $this->UnregisterReference($VarId);
    }

}

/** @} */
