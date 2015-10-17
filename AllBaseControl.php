<?

class HideDeaktivLinkBaseControl extends IPSModule
{

    public function Create()
    {
        parent::Create();

        $this->RegisterPropertyInteger("Source", 0);
        $this->RegisterPropertyInteger("ConditionBoolean", TRUE);
        $this->RegisterPropertyString("ConditionValue", "");
        $this->RegisterPropertyBoolean("Invert", FALSE);
    }
    
    public function Destroy()
    {
        parent::Destroy();
    }

    public function ApplyChanges()
    {
        parent::ApplyChanges();
    }
    
    protected function HideOrDeaktiv(boolean $hidden)
    {
        //must be overwritten
    }
    
    public function Update()
    {
        // prüfen
        IPS_LogMessage("CondBoolValue", print_r($this->ReadPropertyInteger("ConditionBoolean"), 1));
        IPS_LogMessage("CondValue", print_r($this->ReadPropertyInteger("ConditionValue"), 1));
        IPS_LogMessage("IPS", print_r($_IPS, 1));
        $SourceID = $this->ReadPropertyInteger("Source");
        if ($SourceID == 0)
            return;
        if ($_IPS["SENDER"] == "Variable")
        {
            if ($_IPS["VARIABLE"] <> $this->ReadPropertyInteger("Source"))
            {
                echo "Error processing Eventdata";
                return;
            }
            $Value = $_IPS["VALUE"];
        }
        else
        {
            $Value = GetValue($SourceID);
        }
        IPS_LogMessage("IPS", print_r($Value, 1));
        $Source = IPS_GetVariable($SourceID);
        switch ($Source["VariableType"])
        {
            case 0: // bool
                if ($this->ReadPropertyInteger("ConditionBoolean") == (bool) $Value)
                    $this->HideOrDeaktiv(true);
                else
                    $this->HideOrDeaktiv(false);
                break;
            case 1: // int
                if ((int) $this->ReadPropertyString("ConditionValue") == (int) $Value)
                    $this->HiHideOrDeaktiv(true);
                else
                    $this->HideOrDeaktiv(false);

                break;
            case 2: // float
                if ((float) $this->ReadPropertyString("ConditionValue") == (float) $Value)
                    $this->HideOrDeaktiv(true);
                else
                    $this->HideOrDeaktiv(false);

                break;
            case 3: // string
                if ((string) $this->ReadPropertyString("ConditionValue") == (string) $Value)
                    $this->HideOrDeaktiv(true);
                else
                    $this->HideOrDeaktiv(false);

                break;
        }
    }

    protected function UnRegisterEvent($Name)
    {
        $id = @IPS_GetObjectIDByIdent($Name, $this->InstanceID);
        if ($id > 0)
        {
            if (!IPS_EventExists($id))
                throw new Exception('Event not present');
            IPS_DeleteEvent($id);
        }
    }

    protected function RegisterEvent($Name, $Source, $Script)
    {
        $id = @IPS_GetObjectIDByIdent($Name, $this->InstanceID);
        if ($id === false)
            $id = 0;
        if ($id > 0)
        {
            if (!IPS_EventExists($id))
                throw new Exception("Ident with name " . $Name . " is used for wrong object type");

            if (IPS_GetEvent($id)['EventType'] <> 0)
            {
                IPS_DeleteEvent($id);
                $id = 0;
            }
        }
        if ($id == 0)
        {
            $id = IPS_CreateEvent(0);
            IPS_SetParent($id, $this->InstanceID);
            IPS_SetIdent($id, $Name);
        }
        IPS_SetName($id, $Name);
        IPS_SetHidden($id, true);
        IPS_SetEventScript($id, $Script);

        if ($Source > 0)
        {
            IPS_SetEventTrigger($id, 0, $Source);
            if (!IPS_GetEvent($id)['EventActive'])
                IPS_SetEventActive($id, true);
        } else
        {
            IPS_SetEventTrigger($id, 0, 0);

            if (IPS_GetEvent($id)['EventActive'])
                IPS_SetEventActive($id, false);
        }
    }

}

?>