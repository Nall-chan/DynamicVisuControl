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
 * @copyright     2023 Michael Tröger
 * @license       https://creativecommons.org/licenses/by-nc-sa/4.0/ CC BY-NC-SA 4.0
 * @version       3.10
 *
 */

/**
 * HideDeaktivLinkBaseControl ist die Basisklasse für alle Module der Library
 * Erweitert IPSModule.
 *
 * @author        Michael Tröger <micha@nall-chan.net>
 * @copyright     2023 Michael Tröger
 * @license       https://creativecommons.org/licenses/by-nc-sa/4.0/ CC BY-NC-SA 4.0
 *
 * @version       3.10
 *
 * @example <b>Ohne</b>
 * @abstract
 *
 * @property int $SourceID Die IPS-ID der Variable welche als Event verwendet wird.
 */
abstract class HideDeaktivLinkBaseControl extends IPSModule
{
    use \dynamicvisucontrol\DebugHelper;
    use \dynamicvisucontrol\BufferHelper;

    protected static $Form;

    /**
     * Interne Funktion des SDK.
     */
    public function Create()
    {
        parent::Create();

        $this->RegisterPropertyInteger('Source', 1);
        $this->RegisterPropertyInteger('ConditionBoolean', 1);
        $this->RegisterPropertyString('ConditionValue', '');
        $this->RegisterPropertyBoolean('Invert', false);
        $this->SourceID = 1;
    }

    /**
     * Interne Funktion des SDK.
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
     */
    public function ApplyChanges()
    {
        parent::ApplyChanges();
        $this->RegisterMessage(0, IPS_KERNELSTARTED);
        if (IPS_GetKernelRunlevel() != KR_READY) {
            return;
        }
        $this->RegisterTrigger($this->ReadPropertyInteger('Source'));
        if ($this->SourceID > 1) {
            $this->Update(GetValue($this->SourceID));
        }
    }
    /**
     * Interne Funktion des SDK.
     */
    public function RequestAction($Ident, $Value)
    {
        switch ($Ident) {
                case 'SelectVariable':
                    $this->UpdateForm((int) $Value);
                return true;
            }
        return true;
    }
    /**
     * Interne Funktion des SDK.
     */
    public function GetConfigurationForm()
    {
        $Form = json_decode(file_get_contents(static::$Form), true);
        if ($this->SourceID > 1) {
            if (IPS_VariableExists($this->SourceID)) {
                $Source = IPS_GetVariable($this->SourceID);
                if ($Source['VariableType'] == VARIABLETYPE_BOOLEAN) {
                    $Form['elements'][3]['visible'] = false;
                    $Form['elements'][4]['visible'] = false;
                } else {
                    $Form['elements'][1]['visible'] = false;
                    $Form['elements'][2]['visible'] = false;
                }
            }
        }
        $this->SendDebug('FORM', json_encode($Form), 0);
        $this->SendDebug('FORM', json_last_error_msg(), 0);

        return json_encode($Form);
    }
    /**
     * Wird aufgerufen wenn der IPS Betriebsbereit wird.
     */
    protected function KernelReady()
    {
        $this->RegisterTrigger($this->ReadPropertyInteger('Source'));
        if ($this->SourceID > 1) {
            $this->Update(GetValue($this->SourceID));
        }
    }

    /**
     * Registriert die neue TriggerVariable.
     */
    protected function RegisterTrigger(int $NewSourceID)
    {
        $OldSourceID = $this->SourceID;
        if ($NewSourceID != $OldSourceID) {
            $this->UnregisterVariableWatch($OldSourceID);
            $this->RegisterVariableWatch($NewSourceID);
            $this->SourceID = $NewSourceID;
        }
    }
    /**
     * Steuert das verstecken oder deaktivieren.
     *
     * @abstract
     *
     * @param bool $hidden True wenn Ziel(e) versteckt oder deaktiviert werden, false zum anzeigen bzw. aktivieren.
     */
    abstract protected function HideOrDeaktiv(bool $hidden);

    /**
     * Wird durch ein VariablenUpdate aus MessageSink aufgerufen und steuert mit HideOrDeaktiv das Ziel / die Ziele.
     *
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
     * @param int $VarId IPS-ID der Variable.
     */
    protected function RegisterVariableWatch(int $VarId)
    {
        if ($VarId < 9999) {
            return;
        }
        if (IPS_VariableExists($VarId)) {
            $this->SendDebug('RegisterVariableWatch', $VarId, 0);
            $this->RegisterMessage($VarId, VM_DELETE);
            $this->RegisterMessage($VarId, VM_UPDATE);
            $this->RegisterReference($VarId);
        }
    }

    /**
     * Desregistriert eine Überwachung einer Variable.
     *
     * @param int $VarId IPS-ID der Variable.
     */
    protected function UnregisterVariableWatch(int $VarId)
    {
        if ($VarId < 9999) {
            return;
        }
        $this->SendDebug('UnregisterVariableWatch', $VarId, 0);
        $this->UnregisterMessage($VarId, VM_DELETE);
        $this->UnregisterMessage($VarId, VM_UPDATE);
        $this->UnregisterReference($VarId);
    }
    private function UpdateForm(int $Variable)
    {
        if (!IPS_VariableExists($Variable)) {
            $this->UpdateFormField('LabelBool', 'visible', true);
            $this->UpdateFormField('ConditionBoolean', 'visible', true);
            $this->UpdateFormField('LabelValue', 'visible', true);
            $this->UpdateFormField('ConditionValue', 'visible', true);
            return;
        }

        $Source = IPS_GetVariable($Variable);
        if ($Source['VariableType'] == VARIABLETYPE_BOOLEAN) {
            $this->UpdateFormField('LabelBool', 'visible', true);
            $this->UpdateFormField('ConditionBoolean', 'visible', true);
            $this->UpdateFormField('LabelValue', 'visible', false);
            $this->UpdateFormField('ConditionValue', 'visible', false);
        } else {
            $this->UpdateFormField('LabelBool', 'visible', false);
            $this->UpdateFormField('ConditionBoolean', 'visible', false);
            $this->UpdateFormField('LabelValue', 'visible', true);
            $this->UpdateFormField('ConditionValue', 'visible', true);
        }
    }
}

/* @} */
