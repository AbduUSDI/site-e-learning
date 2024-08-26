<?php

namespace App\Controllers;

use App\Models\Rule;

class RuleController
{
    private $rule;

    public function __construct($db)
    {
        $this->rule = new Rule($db);
    }

    public function createRule($title, $description)
    {
        return $this->rule->create($title, $description);
    }

    public function getAllRules()
    {
        return $this->rule->read();
    }

    public function updateRule($id, $title, $description)
    {
        return $this->rule->update($id, $title, $description);
    }

    public function deleteRule($id)
    {
        return $this->rule->delete($id);
    }
}