<?

class HideControl extends IPSModule
{

    public function Create()
    {
        //Never delete this line!
        parent::Create();
        //These lines are parsed on Symcon Startup or Instance creation
        //You cannot use variables here. Just static values.
        $this->RegisterPropertyInteger("Source", 0);
        $this->RegisterPropertyInteger("ConditionBoolean", TRUE);
        $this->RegisterPropertyString("ConditionValue", "");
        $this->RegisterPropertyBoolean("Invert", FALSE);
        $this->RegisterPropertyInteger("Target", 0);
        $this->RegisterPropertyInteger("TargetType", 1);
    }

    public function ApplyChanges()
    {
        //Never delete this line!
        parent::ApplyChanges();

        // 15 Minuten Timer
        $this->RegisterEvent("UpdateHideControl", $this->ReadPropertyInteger("Source"), 'HIDE_Update($_IPS[\'TARGET\']);');
        // Nach übernahme der Einstellungen oder IPS-Neustart einmal Update durchführen.
        $this->Update();
        //$this->RegisterEventCyclic("UpdateTimer", "Automatische aktualisierung", 15);
    }

    private function Hide(boolean $hidden)
    {
        if ($this->ReadPropertyBoolean("Invert"))
            $hidden = !$hidden;

        //
        if ($this->ReadPropertyInteger("Target") == 0)
        {
            echo "Target invalid.";
            return;
        }
        $Target = $this->ReadPropertyInteger("Target");
        if (!IPS_ObjectExists($Target))
        {
            echo "Target invalid.";
            return;
        }

        if ($this->ReadPropertyInteger("TargetType") == 0)
        {
            $this->SetHidden($Target, $hidden);
        }
        elseif ($this->ReadPropertyInteger("TargetType") == 1)
        {
            $Source = $this->ReadPropertyInteger("Source");
            $Childs = IPS_GetChildrenIDs($Target);
            foreach ($Childs as $Child)
            {
                if ($Child == $Source)
                    continue;
                // Wenn Child Link ist 
                // und TargetID $Source ist
                // dann überspringen
                $this->SetHidden($Child, $hidden);
            }
        }
        else
        {
            echo "TargetType invalid.";
            return;
        }
    }

    public function Update()
    {
        // prüfen
        IPS_LogMessage("CondValue", print_r($this->ReadPropertyInteger("ConditionBoolean"), 1));
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
                    $this->Hide(true);
                else
                    $this->Hide(false);
                break;
            case 1: // int
                if ((int) $this->RegisterPropertyString("ConditionValue") == (int) $Value)
                    $this->Hide(true);
                else
                    $this->Hide(false);

                break;
            case 2: // float
                if ((float) $this->RegisterPropertyString("ConditionValue") == (float) $Value)
                    $this->Hide(true);
                else
                    $this->Hide(false);

                break;
            case 3: // string
                if ((string) $this->RegisterPropertyString("ConditionValue") == (string) $Value)
                    $this->Hide(true);
                else
                    $this->Hide(false);

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

    protected function SetEventSource($Name, $Source)
    {
        $id = @IPS_GetObjectIDByIdent($Name, $this->InstanceID);
        if ($id === false)
            throw new Exception('Event not present');

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

    protected function SetHidden($ObjectID, $Value)
    {
        if (IPS_GetObject($ObjectID)["ObjectIsHidden"] <> $Value)
            IPS_SetHidden($ObjectID, $Value);
    }

}

?>