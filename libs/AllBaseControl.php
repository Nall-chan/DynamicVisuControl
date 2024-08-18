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
 * @copyright     2024 Michael Tröger
 * @license       https://creativecommons.org/licenses/by-nc-sa/4.0/ CC BY-NC-SA 4.0
 * @version       3.55:
 *
 */

/**
 * HideDeaktivLinkBaseControl ist die Basisklasse für alle Module der Library
 * Erweitert IPSModule.
 *
 * @author        Michael Tröger <micha@nall-chan.net>
 * @copyright     2024 Michael Tröger
 * @license       https://creativecommons.org/licenses/by-nc-sa/4.0/ CC BY-NC-SA 4.0
 *
 * @version       3.55:
 *
 * @example <b>Ohne</b>
 * @abstract
 *
 * @property int $SourceID Die IPS-ID der Variable welche als Event verwendet wird.
 * @method bool SendDebug(string $Message, mixed $Data, int $Format)
 */
abstract class HideDeaktivLinkBaseControl extends IPSModuleStrict
{
    use \dynamicvisucontrol\DebugHelper;
    use \dynamicvisucontrol\BufferHelper;

    protected static $Form;

    /**
     * Interne Funktion des SDK.
     */
    public function Create(): void
    {
        parent::Create();
        $this->RegisterPropertyInteger('Source', 1);
        $this->RegisterPropertyString('Value', '[]');
        $this->RegisterPropertyBoolean('Invert', false);
        $this->SourceID = 1;
    }

    /**
     * Interne Funktion des SDK.
     */
    public function MessageSink(int $TimeStamp, int $SenderID, int $Message, array $Data): void
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
    public function ApplyChanges(): void
    {
        parent::ApplyChanges();
        $this->SendDebug('Source', $this->ReadPropertyInteger('Source'), 0);
        $this->SendDebug('Value', $this->ReadPropertyString('Value'), 0);
        $this->RegisterMessage(0, IPS_KERNELSTARTED);
        /*if (IPS_GetKernelRunlevel() == KR_READY) {
            if ($this->UpdateConfig()) {
                return;
            }
        }*/
        $this->RegisterTrigger($this->ReadPropertyInteger('Source'));
        if ($this->SourceID > 1) {
            $this->Update(GetValue($this->SourceID));
        }
    }

    public function Migrate(string $JSONData): string
    {
        $Data = json_decode($JSONData);
        if (property_exists($Data->configuration, 'ConditionBoolean')) {
            $VarId = $Data->configuration->Source;
            if (IPS_VariableExists($VarId)) {
                $Source = IPS_GetVariable($VarId);
                switch ($Source['VariableType']) {
                    case VARIABLETYPE_BOOLEAN:
                        $Value = (bool) $Data->configuration->ConditionBoolean;
                        break;
                    case VARIABLETYPE_INTEGER:
                        $Value = (int) $Data->configuration->ConditionValue;
                        break;
                    case VARIABLETYPE_FLOAT:
                        $Value = (float) $Data->configuration->ConditionValue;
                        break;
                    case VARIABLETYPE_STRING:
                        $Value = $Data->configuration->ConditionValue;
                        break;
                }
                $Data->configuration->Value = json_encode($Value);
            }
            $this->SendDebug('Migrate', json_encode($Data), 0);
            $this->LogMessage('Migrated settings:' . json_encode($Data), KL_MESSAGE);
        }
        return json_encode($Data);
    }

    /**
     * Interne Funktion des SDK.
     */
    public function RequestAction(string $Ident, mixed $Value): void
    {
        switch ($Ident) {
            case 'SelectVariable':
                $this->UpdateForm((int) $Value);
                return;
        }
        return;
    }
    /**
     * Interne Funktion des SDK.
     */
    public function GetConfigurationForm(): string
    {
        $Form = json_decode(file_get_contents(static::$Form), true);
        $SourceID = $this->ReadPropertyInteger('Source');
        if ($SourceID > 1) {
            if (IPS_VariableExists($SourceID)) {
                $Form['elements'][1]['variableID'] = $SourceID;
            }
        }
        $this->SendDebug('FORM', json_encode($Form), 0);
        $this->SendDebug('FORM', json_last_error_msg(), 0);

        return json_encode($Form);
    }
    /**
     * Wird aufgerufen wenn der IPS Betriebsbereit wird.
     */
    protected function KernelReady(): void
    {
        $this->RegisterTrigger($this->ReadPropertyInteger('Source'));
        if ($this->SourceID > 1) {
            $this->Update(GetValue($this->SourceID));
        }
    }

    /**
     * Registriert die neue TriggerVariable.
     */
    protected function RegisterTrigger(int $NewSourceID): void
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
    abstract protected function HideOrDeaktiv(bool $hidden): void;

    /**
     * Wird durch ein VariablenUpdate aus MessageSink aufgerufen und steuert mit HideOrDeaktiv das Ziel / die Ziele.
     *
     * @param mixed $Value Der neue Wert der Variable.
     */
    protected function Update(mixed $Value): void
    {
        $this->HideOrDeaktiv(json_decode($this->ReadPropertyString('Value'), true) == $Value);
    }

    /**
     * Registriert eine Überwachung einer Variable.
     *
     * @param int $VarId IPS-ID der Variable.
     */
    protected function RegisterVariableWatch(int $VarId): void
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
    protected function UnregisterVariableWatch(int $VarId): void
    {
        if ($VarId < 9999) {
            return;
        }
        $this->SendDebug('UnregisterVariableWatch', $VarId, 0);
        $this->UnregisterMessage($VarId, VM_DELETE);
        $this->UnregisterMessage($VarId, VM_UPDATE);
        $this->UnregisterReference($VarId);
    }

    private function UpdateConfig(): bool
    {
        $this->SendDebug('UpdateConfig', '1', 0);
        $Value = json_decode($this->ReadPropertyString('Value'), true);
        if (!is_array($Value)) { // new Property is [] !
            return false;
        }
        $this->SendDebug('UpdateConfig', '2', 0);
        $OldConfig = json_decode(IPS_GetConfiguration($this->InstanceID), true);
        if (!array_key_exists('ConditionBoolean', $OldConfig)) {
            return false;
        }
        $this->SendDebug('UpdateConfig', '3', 0);
        $VarId = $this->ReadPropertyInteger('Source');
        if (!IPS_VariableExists($VarId)) {
            return false;
        }
        $this->SendDebug('UpdateConfig', '4', 0);
        $Source = IPS_GetVariable($VarId);
        switch ($Source['VariableType']) {
            case VARIABLETYPE_BOOLEAN:
                $Value = (bool) $OldConfig['ConditionBoolean'];
                break;
            case VARIABLETYPE_INTEGER:
                $Value = (int) $OldConfig['ConditionValue'];
                break;
            case VARIABLETYPE_FLOAT:
                $Value = (float) $OldConfig['ConditionValue'];
                break;
            case VARIABLETYPE_STRING:
                $Value = $OldConfig['ConditionValue'];
                break;
        }
        $this->SendDebug('UpdateConfig', 'BANG', 0);
        IPS_SetProperty($this->InstanceID, 'Value', json_encode($Value));
        IPS_ApplyChanges($this->InstanceID);
        return true;
    }

    private function UpdateForm(int $Variable): void
    {
        if (!IPS_VariableExists($Variable)) {
            $Variable = 0;
        }

        $this->UpdateFormField('Value', 'variableID', $Variable);
    }
}

/* @} */
