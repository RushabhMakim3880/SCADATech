<?php
/*
Used place holders so far, list maintained here for quick reference.
{{MODULE_NAME}}
{{ITEM_NAME}}
{{PRIMARY_FIELD}}
{{FOREIGN_DROPDOWNS}}
{{FORM_FIELDS}}
{{TABLE_NAME}}
{{VALIDATION_RULES}}
{{MODEL_ALLOWED_FIELDS}}
{{DATE_TIME_CONVERSION}}
{{DATATABLE_COLUMNS}}
{{DATATABLE_FILTERS}}
{{DELETED_FILETER}}
{{DB_TABLE}}
{{SCREEN_DATA_PROCESSING}}
{{GENERAL_DATA_PROCESSING}}
{{AUTO_SAVE_TENANT_ID}}

{{FIELD_NAME}}

{{CUSTOM_FUNCTIONS_FOR_TOGGLE}}
{{CUSTOM_FUNCTIONS_FOR_SWITCH}}
{{CUSTOM_FUNCTIONS_FOR_VIEW_DETAILS}}
{{CUSTOM_BACKEND_ROUTES}}

{{COLUMN_TOTALS}}

{{CRUD_TYPE}}
*/



namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Config\Database;

class GenerateCrudModule extends BaseCommand
{
    protected $group       = 'custom';
    protected $name        = 'make:crud';
    protected $description = 'Generates a CRUD module for a given database table in CI4.';

    private $itemName = '';
    private $moduleName = '';
    private $tableName = '';
    private $db;
    private $backendPath = '';
    private $frontendPath = '';
    private $primaryField = '';
    private $formFields = [];
    private $foreignKeys = [];
    private $formIgnoreList = ['tenantId', 'updatedBy', 'updatedAt', 'createdBy', 'createdAt', 'isDeleted', 'serialNo'];
    private $crudType = 'popup';


    public function run(array $params)
    {
        $this->showIntro(
            "Generate CRUD Module",
            "This command will help you generate a CRUD module for a given MySQL table in CodeIgniter 4. 
It will ask you to confirm various settings for each column in the table, 
and then generate the necessary files for the Frontend and Backend modules."
        );

        $customize = CLI::prompt("Do you want to customize settings?", ['No', 'Yes'], 'required');


        $this->tableName  = CLI::prompt('Enter MySQL Table Name');
        $this->moduleName = CLI::prompt('Enter Module Name');
        $this->itemName = CLI::prompt('Enter Item Name');

        $this->itemName = ucfirst(strtolower($this->itemName));


        // $this->tableName = "newSampleTable";
        // $this->moduleName = "testMaster";
        // $this->itemName = "Test";

        $this->frontendPath = ROOTPATH . "Modules/Frontend/{$this->moduleName}/";
        $this->backendPath = ROOTPATH . "Modules/Backend/{$this->moduleName}/";

        // if ($this->moduleExists($this->frontendPath) || $this->moduleExists($this->backendPath)) {
        //     CLI::error("Module {$this->moduleName} already exists! Aborting to prevent overwrite.");
        //     return;
        // }

        $this->db = db_connect();

        $this->formFields = $this->db->getFieldData($this->tableName);

        //prepare name for function names for first character as capital
        foreach ($this->formFields as $k => $f) {
            if ($f->name == "serialNo") {
                // unset 
                unset($this->formFields[$k]);
                continue;
            }

            $this->formFields[$k]->ucFirstName = ucfirst($f->name);
        }

        $this->prepareEnumFields();
        $this->primaryField = $this->formFields[0]->name;
        $this->getForeignKeys();

        $counter = 1;

        CLI::write("\n" . str_repeat('-', 80), 'green');
        CLI::write("$counter. Let's first confirm Foreign Key Relations.", 'cyan');
        CLI::write(str_repeat('-', 80) . "\n", 'green');
        $counter++;

        // add additional properties.
        foreach ($this->formFields as &$field) {
            $field->visible = "No";
            $field->visibleControl = "Yes";
            $field->searchable = "No";
            $field->columnTotal = "No";
            $field->filterType = "None";
            $field->toggle = "No";
            $field->switch = "No";
            if ($field->name == $this->primaryField) {
                $field->visible = "Yes";
                $field->visibleControl = "No";
                $field->label = "Sr.";
            } else {
                $field->label = printable($field->name);
            }
        }



        foreach ($this->formFields as &$field) {
            if (array_key_exists($field->name, $this->foreignKeys)) {
                $foreignTable = $this->foreignKeys[$field->name]['table'];
                $foreignLabel = $this->foreignKeys[$field->name]['nameField'];

                CLI::write("Foreign label for field {$field->name} found: $foreignLabel (Table: $foreignTable)", 'yellow');

                $newLabel = CLI::prompt(
                    "Press Enter to continue or enter another valid field from [$foreignTable] as label",
                    null
                );

                if ($newLabel) {
                    while (!$this->fieldExists($foreignTable, $newLabel)) {
                        CLI::error("Field [$newLabel] not found in [$foreignTable]. Please enter a valid field.");
                        $newLabel = CLI::prompt("Enter a valid field from [$foreignTable] to use as label");
                    }
                    $this->foreignKeys[$field->name]['nameField'] = $newLabel;
                }
            }
        }

        if ($customize == 'Yes') {
            CLI::write("\n" . str_repeat('-', 80), 'green');
            CLI::write("$counter. Confirm Column Filter Types", 'cyan');
            CLI::write(str_repeat('-', 80) . "\n", 'green');
            $counter++;
        }

        foreach ($this->formFields as &$field) {
            // Skip isDeleted field
            if ($field->name == 'isDeleted' or $field->name == 'tenantId') {
                continue;
            }

            // If field is a foreign key, always use ['Select', 'Checkbox', 'None']
            if (array_key_exists($field->name, $this->foreignKeys)) {
                $choices = ['Select', 'Checkbox', 'None'];
            } else {
                // Otherwise, determine choices based on field type
                switch ($field->type) {
                    case 'date':
                    case 'datetime':
                        $choices = ['Past', 'Future', 'All', 'None'];
                        break;
                    case 'enum':
                        $choices = ['Select', 'Checkbox', 'None'];
                        break;
                    case 'tinyint':
                        if ($field->max_length == 1) {
                            $choices = ['Yes-No', 'On-Off', 'Active-Inactive', 'Enabled-Disabled', 'None'];
                        } else {
                            $choices = ['Select', 'Checkbox', 'None'];
                        }
                        break;
                    default:
                        continue 2;
                }
            }

            if ($customize == 'No') {
                $field->filterType = $choices[0];
                continue;
            }
            $field->filterType = CLI::prompt("Choose filter type for {$field->name}:", $choices, 'required');
        }

        if ($customize == 'Yes') {
            CLI::write("\n" . str_repeat('-', 80), 'green');
            CLI::write("$counter. Confirm Column Default Visibility", 'cyan');
            CLI::write(str_repeat('-', 80) . "\n", 'green');
            $counter++;
        }

        foreach ($this->formFields as &$field) {

            //skip isDeleted field and primary key
            if ($field->name == 'isDeleted' || $field->name == $this->primaryField || $field->name == 'tenantId') {
                continue;
            }

            //if createdBy, createdAt, updatedBy, updatedAt, isDeleted
            if (in_array($field->name, ['createdBy', 'updatedBy', 'updatedAt'])) {
                if ($customize == 'No') {
                    $field->visible = 'No';
                } else {
                    $field->visible = CLI::prompt("Should {$field->name} be visible by default?", ['No', 'Yes'], 'required');
                }
            } else {
                if ($customize == 'No') {
                    $field->visible = 'Yes';
                } else {
                    $field->visible = CLI::prompt("Should {$field->name} be visible by default?", ['Yes', 'No'], 'required');
                }
            }
        }

        if ($customize == 'Yes') {
            CLI::write("\n" . str_repeat('-', 80), 'green');
            CLI::write("$counter. Confirm Column Searchable", 'cyan');
            CLI::write(str_repeat('-', 80) . "\n", 'green');
            $counter++;
        }

        foreach ($this->formFields as &$field) {
            if (in_array($field->type, ['varchar', 'text'])) {
                if ($customize == 'No') {
                    $field->searchable = 'Yes';
                } else {
                    $field->searchable = CLI::prompt("Should {$field->name} be searchable?", ['Yes', 'No'], 'required');
                }
            }
        }

        if ($customize == 'Yes') {
            CLI::write("\n" . str_repeat('-', 80), 'green');
            CLI::write("$counter. Confirm Column Controls", 'cyan');
            CLI::write(str_repeat('-', 80) . "\n", 'green');
            $counter++;
        }

        foreach ($this->formFields as &$field) {
            if ($this->primaryField == $field->name or $field->name == 'isDeleted' or $field->name == 'tenantId') {
                $field->visibleControl = false;
                continue;
            }
            if ($customize == 'No') {
                $field->visibleControl = 'Yes';
            } else {
                $field->visibleControl = CLI::prompt("Allow {$field->name} to be shown/hidden?", ['Yes', 'No'], 'required');
            }
        }

        if ($customize == 'Yes') {
            CLI::write("\n" . str_repeat('-', 80), 'green');
            CLI::write("$counter. Confirm Column Totals", 'cyan');
            CLI::write(str_repeat('-', 80) . "\n", 'green');
            $counter++;
        }

        foreach ($this->formFields as &$field) {
            if (in_array($field->type, ['int', 'smallint', 'bigint', 'decimal']) && $this->primaryField != $field->name && !array_key_exists($field->name, $this->foreignKeys)) {

                if ($customize == 'No') {
                    $field->columnTotal = 'No';
                } else {
                    $field->columnTotal = CLI::prompt("Require total for {$field->name}?", ['No', 'Yes'], 'required');
                }
            }
        }

        if ($customize == 'Yes') {
            CLI::write("\n" . str_repeat('-', 80), 'green');
            CLI::write("$counter. Confirm Additional Features", 'cyan');
            CLI::write(str_repeat('-', 80) . "\n", 'green');
            $counter++;
        }

        foreach ($this->formFields as &$field) {

            // skip isDeleted
            if ($field->name == 'isDeleted') {
                continue;
            }

            // for tinyint 
            if ($field->type == 'tinyint' and $field->max_length == 1) {
                if ($customize == 'No') {
                    $field->toggle = 'No';
                } else {
                    $field->toggle = CLI::prompt("Allow {$field->name} to toggle on click?", ['Yes', 'No'], 'required');
                }
            }

            // for enum fields
            if ($field->type == 'enum') {
                if ($customize == 'No') {
                    $field->switch = 'No';
                } else {
                    $field->switch = CLI::prompt("Allow {$field->name} dropdown quick switch?", ['Yes', 'No'], 'required');
                }
            }
        }


        CLI::newLine();
        CLI::newLine();

        do {
            CLI::write("\n============================================", 'yellow');
            CLI::write("⚠️  ATTENTION REQUIRED!", 'yellow',);
            CLI::write("============================================", 'yellow');
            CLI::write("Please carefully select the ADD/EDIT Form Type:", 'yellow');
            CLI::write(" - 'popup' → Opens form in a modal popup on manage screen.", 'cyan');
            CLI::write(" - 'normal' → Opens form as a separate screen.", 'cyan');
            CLI::write("============================================", 'yellow');

            // Read user input manually to prevent auto-selecting the first option
            $input = trim(strtolower(CLI::prompt("Select ADD/EDIT Form Type (Type explicitly: 'popup' or 'normal')", null)));

            // Validate input
            if (!in_array($input, ['popup', 'normal'])) {
                CLI::write("\n❌ Invalid input! Please type either 'popup' or 'normal'.", 'red');
            }
        } while (!in_array($input, ['popup', 'normal'])); // Force loop until valid input is entered

        $this->crudType = $input; // Assign the validated input

        $this->createFrontEndModuleStructure();
        $this->createBackEndModuleStructure();

        $this->showCompleted();
    }

    private function moduleExists($modulePath)
    {
        return is_dir($modulePath);
    }

    private function createFrontEndModuleStructure()
    {
        $paths = [
            "Config",
            "Controllers",
            "Views"
        ];

        foreach ($paths as $path) {
            if (!is_dir($this->frontendPath . $path)) {
                mkdir($this->frontendPath . $path, 0777, true);
            }
        }

        $this->createFileFromTemplate($this->frontendPath . "Config/Routes.php", 'Frontend/' . $this->crudType . '/Routes.tpl.php');
        $this->createFileFromTemplate($this->frontendPath . "Controllers/{$this->moduleName}.php", 'Frontend/' . $this->crudType . '/Controller.tpl.php');
        $this->createFileFromTemplate($this->frontendPath . "Views/add{$this->itemName}.php", 'Frontend/' . $this->crudType . '/addEdit.tpl.php');
        $this->createFileFromTemplate($this->frontendPath . "Views/manage{$this->itemName}.php", 'Frontend/' . $this->crudType . '/manage.tpl.php');
    }

    private function createBackEndModuleStructure()
    {
        $paths = [
            "Config",
            "Controllers",
            "Models",
            "Views"
        ];

        foreach ($paths as $path) {
            if (!is_dir($this->backendPath . $path)) {
                mkdir($this->backendPath . $path, 0777, true);
            }
        }

        $this->createFileFromTemplate($this->backendPath . "Config/Routes.php", 'Backend/' . $this->crudType . '/Routes.tpl.php');
        $this->createFileFromTemplate($this->backendPath . "Controllers/{$this->moduleName}.php", 'Backend/' . $this->crudType . '/Controller.tpl.php');
        $this->createFileFromTemplate($this->backendPath . "Models/{$this->moduleName}Model.php", 'Backend/' . $this->crudType . '/Model.tpl.php');
        $this->createFileFromTemplate($this->backendPath . "Config/Permissions.php", 'Backend/' . $this->crudType . '/Permissions.tpl.php');
    }

    private function createFileFromTemplate($filePath, $templateFile)
    {
        $templatePath = ROOTPATH . "autoCrudTemplates/{$templateFile}";
        if (!file_exists($templatePath)) {
            CLI::error("Template file {$templateFile} not found!");
            return;
        }

        $content = file_get_contents($templatePath);
        $content = $this->prepareContent($content);


        file_put_contents($filePath, $content);
    }

    private function getToggleContent()
    {
        $content = file_get_contents(ROOTPATH . "autoCrudTemplates/Backend/{$this->crudType}/toggleBooleanFunction.tpl.php");
        $content = str_replace("<?php", "", $content);
        $content = str_replace('{{MODULE_NAME}}', $this->moduleName, $content);
        $content = str_replace('{{ITEM_NAME}}', $this->itemName, $content);
        $content = str_replace('{{PRIMARY_FIELD}}', $this->primaryField, $content);
        $content = str_replace('{{TABLE_NAME}}', $this->tableName, $content);

        return $content;
    }

    private function getSwitchContent()
    {
        $content = file_get_contents(ROOTPATH . "autoCrudTemplates/Backend/{$this->crudType}/switchEnumFunction.tpl.php");
        $content = str_replace("<?php", "", $content);
        $content = str_replace('{{MODULE_NAME}}', $this->moduleName, $content);
        $content = str_replace('{{ITEM_NAME}}', $this->itemName, $content);
        $content = str_replace('{{PRIMARY_FIELD}}', $this->primaryField, $content);
        $content = str_replace('{{TABLE_NAME}}', $this->tableName, $content);
        return $content;
    }

    private function prepareContent($content)
    {
        $content = str_replace('{{MODULE_NAME}}', $this->moduleName, $content);
        $content = str_replace('{{ITEM_NAME}}', $this->itemName, $content);
        $content = str_replace('{{PRIMARY_FIELD}}', $this->primaryField, $content);
        $content = str_replace('{{FOREIGN_DROPDOWNS}}', $this->getForeignDropdowns(), $content);
        $content = str_replace('{{FORM_FIELDS}}', $this->getFormFields(), $content);
        $content = str_replace('{{TABLE_NAME}}', $this->tableName, $content);
        $content = str_replace('{{CRUD_TYPE}}', $this->crudType, $content);

        $modelAllowedFields = '';
        $validationRules = [];
        $dateTimeConversion = '';
        $toggleFunctions = '';
        $switchFunctions = '';
        $customBackendRoutes = '';

        foreach ($this->formFields as $field) {

            $modelAllowedFields .= "'" . $field->name . "',\n        ";

            $fieldName = $field->name;
            $fieldType = $field->type;

            if (in_array($fieldName, [$this->primaryField, 'isDeleted', 'tenantId', 'serialNo'])) {
                continue;
            }

            //is required filed based on nullable.
            if ($field->nullable == 0) {
                $validationRules[$fieldName]['rules'][] = 'required';
            }

            // max length validation for varchar fields
            if ($fieldType == 'varchar') {
                $validationRules[$fieldName]['rules'][] = 'max_length[' . $field->max_length . ']';
            }

            // integer validation for int fields
            if ($fieldType == 'int' or $fieldType == 'bigint') {
                $validationRules[$fieldName]['rules'][] = 'integer';
                $validationRules[$fieldName]['rules'][] = 'is_natural';
            }

            // decimal validation for decimal fields
            if ($fieldType == 'decimal' or $fieldType == 'float') {
                $validationRules[$fieldName]['rules'][] = 'decimal';
            }

            if ($fieldType == 'date') {
                $dateTimeConversion .= "if (!empty(\$jsonInput['{$fieldName}'])) {
                            \$jsonInput['{$fieldName}'] = date('Y-m-d', strtotime(str_replace('/', '-', \$jsonInput['{$fieldName}'])));
                        }\n";
            } else if ($fieldType == 'datetime') {
                $dateTimeConversion .= "if (!empty(\$jsonInput['{$fieldName}'])) {
                            \$jsonInput['{$fieldName}'] = date('Y-m-d H:i:s', strtotime(str_replace('/', '-', \$jsonInput['{$fieldName}'])));
                        }\n";
            } else if ($fieldType == 'time') {
                $dateTimeConversion .= "if (!empty(\$jsonInput['{$fieldName}'])) {
                            \$jsonInput['{$fieldName}'] = date('H:i:s', strtotime(str_replace('/', '-', \$jsonInput['{$fieldName}'])));
                        }\n";
            }


            // prepare toggle functions
            if ($field->toggle == 'Yes') {
                $tempContent = $this->getToggleContent();
                $tempContent = str_replace('{{UC_FIELD_NAME}}', $field->ucFirstName, $tempContent);
                $tempContent = str_replace('{{FIELD_NAME}}', $field->name, $tempContent);

                $toggleFunctions .= $tempContent . "\n\n";

                // add backend custom routes
                $customBackendRoutes .= "\$routes->get('toggle{$field->ucFirstName}/(:any)', '{$this->moduleName}::toggle{$field->ucFirstName}/$1'); // Toggle {$fieldName}\n";
            }

            // prepare switch functions
            if ($field->switch == 'Yes') {

                $tempContent = $this->getSwitchContent();
                $tempContent = str_replace('{{UC_FIELD_NAME}}', $field->ucFirstName, $tempContent);
                $tempContent = str_replace('{{FIELD_NAME}}', $field->name, $tempContent);

                $switchFunctions .= $tempContent . "\n\n";

                // add backend custom routes
                $customBackendRoutes .= "\$routes->get('switch{$field->ucFirstName}/(:any)', '{$this->moduleName}::switch{$field->ucFirstName}/$1'); // Switch {$fieldName}\n";
            }
        }

        $rules = "";
        foreach ($validationRules as $field => $rule) {
            $rules .= "\$rules['{$field}'] = [
                        'label'  => '" . printable($field) . "',
                        'rules'  => '" . implode('|', $rule['rules']) . "'
                    ];\n";
        }

        $content = str_replace('{{VALIDATION_RULES}}', $rules, $content);

        $content = str_replace('{{MODEL_ALLOWED_FIELDS}}', $modelAllowedFields, $content);

        $content = str_replace('{{DATE_TIME_CONVERSION}}', $dateTimeConversion, $content);

        $content = str_replace('{{CUSTOM_FUNCTIONS_FOR_TOGGLE}}', $toggleFunctions, $content);
        $content = str_replace('{{CUSTOM_FUNCTIONS_FOR_SWITCH}}', $switchFunctions, $content);
        $content = str_replace('{{CUSTOM_BACKEND_ROUTES}}', $customBackendRoutes, $content);


        //prepare datatable data.
        $dataTableColumns = "";
        $dataTableColumnFileters = "";
        $isDeleted = false;
        $isTenantId = false;

        foreach ($this->formFields as $field) {

            if ($field->name == 'isDeleted') {
                $isDeleted = true;
                continue;
            }

            if ($field->name == 'tenantId') {
                $isTenantId = true;
                continue;
            }

            $visible = ($field->visible && $field->visible == 'Yes') ? 'true' : 'false';
            $searchable = ($field->searchable && $field->searchable == 'Yes') ? 'true' : 'false';
            $visibleControl = ($field->visibleControl && $field->visibleControl == 'Yes') ? 'true' : 'false';

            if (array_key_exists($field->name, $this->foreignKeys)) {
                $foreignTable = $this->foreignKeys[$field->name]['table'];
                $foreignColumn = $this->foreignKeys[$field->name]['column'];
                $foreignNameField = $this->foreignKeys[$field->name]['nameField'];

                $dataTableColumns .= "\$defaultColumns['{$foreignTable}_{$foreignNameField}'] = ['title' => '" . printable($foreignNameField) . "', 'visible' => $visible, 'orderable' => true, 'searchable' => $searchable, 'visibleControl' => $visibleControl];\n";
            } else {
                $dataTableColumns .= "\$defaultColumns['{$this->tableName}_{$field->name}'] = ['title' => '$field->label', 'visible' => $visible, 'orderable' => true, 'searchable' => $searchable, 'visibleControl' => $visibleControl];\n";
            }
            //filter for enum, foreign key, tinyint, boolean
            if ($field->type == 'enum' and $field->filterType and $field->filterType != 'None') {
                $query = $this->db->query("SHOW COLUMNS FROM {$this->tableName} WHERE Field = '{$field->name}'");
                $enumValues = [];
                $result = $query->getRow();
                if ($result) {
                    preg_match("/^enum\((.*)\)$/", $result->Type, $matches);
                    if (isset($matches[1])) {
                        $enumValues = str_getcsv($matches[1], ",", "'");
                    }
                }

                $filterData = [];
                foreach ($enumValues as $enum) {
                    $filterData[] = "'{$enum}' => '" . printable($enum) . "'";
                }

                $dataTableColumnFileters .= "\$filterData = [" . implode(",", $filterData) . "];\n";
                $dataTableColumnFileters .= "\$defaultColumns['{$this->tableName}_{$field->name}']['filterType'] = '" . strtolower($field->filterType) . "';\n";
                $dataTableColumnFileters .= "\$defaultColumns['{$this->tableName}_{$field->name}']['filterOptions'] = \$filterData;\n\n\n";
            }

            // if date, datetime
            if ($field->type == 'date' or $field->type == 'datetime') {
                if ($field->filterType and $field->filterType != 'None') {
                    $dataTableColumnFileters .= "\$defaultColumns['{$this->tableName}_{$field->name}']['filterType'] = 'date';\n";
                    $dataTableColumnFileters .= "\$defaultColumns['{$this->tableName}_{$field->name}']['filterOptions'] = dateFilterOptions('" . strtolower($field->filterType) . "');\n\n\n";
                }
            }

            // if tinyint, boolean
            if ($field->type == 'tinyint' and $field->max_length == 1) {
                if ($field->filterType and $field->filterType != 'None') {
                    if ($field->filterType == 'Yes-No') {
                        $filterData = "['1' => 'Yes', '0' => 'No']";
                    } else if ($field->filterType == 'On-Off') {
                        $filterData = "['1' => 'On', '0' => 'Off']";
                    } else if ($field->filterType == 'Active-Inactive') {
                        $filterData = "['1' => 'Active', '0' => 'Inactive']";
                    } else if ($field->filterType == 'Enabled-Disabled') {
                        $filterData = "['1' => 'Enabled', '0' => 'Disabled']";
                    }
                    $dataTableColumnFileters .= "\$filterData = $filterData;\n";
                    $dataTableColumnFileters .= "\$defaultColumns['{$this->tableName}_{$field->name}']['filterType'] = 'select';\n";
                    $dataTableColumnFileters .= "\$defaultColumns['{$this->tableName}_{$field->name}']['filterOptions'] = \$filterData;\n\n\n";
                }
            }

            // if foreign key
            if (array_key_exists($field->name, $this->foreignKeys)) {
                if ($field->filterType and $field->filterType != 'None') {
                    $foreignTable = $this->foreignKeys[$field->name]['table'];
                    $foreignColumn = $this->foreignKeys[$field->name]['column'];
                    $foreignNameField = $this->foreignKeys[$field->name]['nameField'];

                    $dataTableColumnFileters .= "\$temp = \$this->db->query(\"SELECT GROUP_CONCAT({$foreignNameField} SEPARATOR '__') as `filterData` FROM {$foreignTable}\")->getRow()->filterData;\n";
                    $dataTableColumnFileters .= "\$foreignFilters = explode('__', \$temp);\n";
                    $dataTableColumnFileters .= "\$defaultColumns['{$foreignTable}_{$foreignNameField}']['filterType'] = '" . strtolower($field->filterType) . "';\n";
                    $dataTableColumnFileters .= "\$defaultColumns['{$foreignTable}_{$foreignNameField}']['filterOptions'] = \$foreignFilters;\n\n\n";
                }
            }


            // $dataTableColumnFileters .= "{ data: '{$field->name}', title: '{$field->name}' },\n            ";
        }

        $content = str_replace('{{DATATABLE_COLUMNS}}', $dataTableColumns, $content);
        $content = str_replace('{{DATATABLE_FILTERS}}', $dataTableColumnFileters, $content);


        // DELETED_FILETER
        // if $this->fields contains isDeleted
        $deletedWhere = "\$where = [\"1\"];\n";
        $autoSaveTenantId = "";
        if ($isDeleted) {
            $deletedWhere .= "\$where[] = \"{$this->tableName}.isDeleted = '0'\";\n";
        }
        if ($isTenantId) {
            $deletedWhere .= "\$where[] = \"{$this->tableName}.tenantId = '\".\$this->user->tenantId.\"'\";\n";
            $autoSaveTenantId = "\$jsonInput['tenantId'] = \$this->user->tenantId;\n";
        }

        $content = str_replace('{{DELETED_FILETER}}', $deletedWhere, $content);
        $content = str_replace('{{AUTO_SAVE_TENANT_ID}}', $autoSaveTenantId, $content);

        // $dbTable
        $dbTable = "\$dbTable = '{$this->tableName} ";
        $alreadyTableDone = [];
        foreach ($this->foreignKeys as $field => $foreign) {
            if (in_array($foreign['table'], $alreadyTableDone)) {
                continue;
            }

            $dbTable .= "LEFT JOIN {$foreign['table']} {$foreign['table']} ON {$foreign['table']}.{$foreign['column']} = {$this->tableName}.{$field} \n";
            $alreadyTableDone[] = $foreign['table'];
        }

        $dbTable .= "';\n";

        $screenDataProcessing = "";
        $generalDataProcessing = "";
        $columnTotals = "";
        foreach ($this->formFields as $field) {

            // skip foreign keys
            if (array_key_exists($field->name, $this->foreignKeys)) {
                continue;
            }

            //ignore is deleted 
            if ($field->name == 'isDeleted') {
                continue;
            }

            //if primary field, add edit button.
            if ($field->name == $this->primaryField) {
                // nothing doing here, dropdown code added there in controller
                continue;
            }

            if ($field->type == 'date') {
                $generalDataProcessing .= "\$row->{$this->tableName}_{$field->name} = myDateFormat(\$row->{$this->tableName}_{$field->name});\n";
            }
            if ($field->type == 'datetime') {
                $generalDataProcessing .= "\$row->{$this->tableName}_{$field->name} = myDateTimeFormat(\$row->{$this->tableName}_{$field->name});\n";
            }

            if ($field->type == 'time') {
                $generalDataProcessing .= "\$row->{$this->tableName}_{$field->name} = myTimeFormat(\$row->{$this->tableName}_{$field->name});\n";
            }

            //prepare data for column totals
            if ($field->columnTotal == 'Yes') {
                $columnTotals .= "\$columnTotalsData['{$this->tableName}_{$field->name}'] = \$this->db->query(\"SELECT SUM({$this->tableName}.{$field->name}) as total FROM \$dbTable \$whereClause\", \$queryParameters)->getRow()->total;\n";
            }


            if ($field->type == 'tinyint' and $field->max_length == 1) {

                if ($field->toggle == 'Yes') {

                    $screenDataProcessing .= "\n\n/***********************************************************************/\n";
                    $screenDataProcessing .= "//$field->name: Toggle Code Starts \n";
                    $screenDataProcessing .= "//If not required, comment this code, uncomment last line of this code part, remove \"toggle{$field->ucFirstName}\" function code, remove route from route file for proper cleanup.\n";
                    $screenDataProcessing .= "/***********************************************************************/\n";

                    $enumList = "";
                    if ($field->filterType == "Yes-No")
                        $enumList = "\$enumList = ['1' => 'Yes', '0' => 'No'];\n";
                    else if ($field->filterType == "On-Off")
                        $enumList = "\$enumList = ['1' => 'On', '0' => 'Off'];\n";
                    else if ($field->filterType == "Active-Inactive")
                        $enumList = "\$enumList = ['1' => 'Active', '0' => 'Inactive'];\n";
                    else if ($field->filterType == "Enabled-Disabled")
                        $enumList = "\$enumList = ['1' => 'Enabled', '0' => 'Disabled'];\n";
                    else
                        $enumList = "\$enumList = ['1' => 'Yes', '0' => 'No'];\n";

                    $screenDataProcessing .= $enumList;

                    $screenDataProcessing .= "
                    \$className = 'bg-success';
                    if (\$row->{$this->tableName}_{$field->name} == 0) {
                        \$className = 'bg-danger';
                    } 
                    \$row->{$this->tableName}_{$field->name} = \"<span title='Click to toggle \".printable('{$field->name}').\"' class='badge \$className bg-success cursor-pointer apiAction' data-confirm='Are you sure?' data-endpoint='api/{$this->moduleName}/toggle{$field->ucFirstName}/\" . setKey(\$primaryKey, \"{$this->itemName}\") . \"'>\".\$enumList[\$row->{$this->tableName}_{$field->name}].\"</span>\";\n\n";

                    $screenDataProcessing .= "/***********************************************************************/\n";
                    $screenDataProcessing .= "//$field->name: Toggle Code Ends \n";
                    $screenDataProcessing .= "//if dont want, comment above part and uncomment below line and do cleanup as explained in above comment box\n";
                    $screenDataProcessing .= "/***********************************************************************/\n";
                    $screenDataProcessing .= "//" . $enumList;
                    $screenDataProcessing .= "//\$row->{$this->tableName}_{$field->name} = \$enumList[\$row->{$this->tableName}_{$field->name}];\n";
                } else {
                    if ($field->filterType == "Yes-No")
                        $generalDataProcessing .= "\$enumList = ['1' => 'Yes', '0' => 'No'];\n";
                    else if ($field->filterType == "On-Off")
                        $generalDataProcessing .= "\$enumList = ['1' => 'On', '0' => 'Off'];\n";
                    else if ($field->filterType == "Active-Inactive")
                        $generalDataProcessing .= "\$enumList = ['1' => 'Active', '0' => 'Inactive'];\n";
                    else if ($field->filterType == "Enabled-Disabled")
                        $generalDataProcessing .= "\$enumList = ['1' => 'Enabled', '0' => 'Disabled'];\n";
                    else
                        $generalDataProcessing .= "\$enumList = ['1' => 'Yes', '0' => 'No'];\n";
                    $generalDataProcessing .= "\$row->{$this->tableName}_{$field->name} = \$enumList[\$row->{$this->tableName}_{$field->name}];\n";
                }
            }

            if ($field->type == 'enum') {
                if ($field->switch == 'Yes') {
                    $screenDataProcessing .= "\n\n/*******************************************************/\n";
                    $screenDataProcessing .= "//$field->name: Switch Code Starts Here \n";
                    $screenDataProcessing .= "//If not required, comment this code, uncomment last line of this code part, remove \"switch{$field->ucFirstName}\" function code, remove route from route file for proper cleanup.\n";
                    $screenDataProcessing .= "/*******************************************************/\n";
                    $screenDataProcessing .= "\$dropdownItems = [];\n";
                    $screenDataProcessing .= "\$enumList = ['" . implode("','", $field->enumOptions) . "'];\n";
                    $screenDataProcessing .= "foreach(\$enumList as \$option) {\n";
                    $screenDataProcessing .= "if(\$option == \$row->{$this->tableName}_{$field->name}) {\n continue; \n}\n";
                    $screenDataProcessing .= "\$dropdownItems[] = ['label' => printable(\$option), 'href' => 'javascript:;', 'class' => 'text-dark apiAction', 'attributes' => \"data-confirm='Are you sure to change \".printable('{$field->name}').\"?' data-endpoint='\" . (\"api/{$this->moduleName}/switch{$field->ucFirstName}/\" . setKey(\$primaryKey, \"{$this->itemName}\")) . \"/\$option'\"];\n\n";
                    $screenDataProcessing .= "}\n";

                    $screenDataProcessing .= "
                                        \$dropdown = [
                                        'id' => 'actionDropdown_{$field->name}_' . \$primaryKey,
                                        'toggleClass' => 'd-inline-flex btn-secondary align-items-center gap-1',
                                        'toggleLabel' => printable(\$row->{$this->tableName}_{$field->name}),
                                        'toggleAttributes' => 'title=\"Click to Change\"',
                                        'menuClass' => 'dropdown-menu-start',
                                        'menuAttributes' => '',
                                        'items' => \$dropdownItems,
                                    ];

                                    \$dropdownHtml = view('templates/' . \$config->theme . '/components/dropdown', ['dropdown' => \$dropdown]);

                    \$row->{$this->tableName}_{$field->name} = \$dropdownHtml;\n\n";
                    $screenDataProcessing .= "/*******************************************************/\n";
                    $screenDataProcessing .= "//$field->name: Switch Code Starts Here \n";
                    $screenDataProcessing .= "//if dont want, comment above part and uncomment below line and do cleanup as explained in above comment box\n";
                    $screenDataProcessing .= "//\$row->{$this->tableName}_{$field->name} = printable(\$row->{$this->tableName}_{$field->name});\n";
                } else {
                    $generalDataProcessing .= "\$row->{$this->tableName}_{$field->name} = printable(\$row->{$this->tableName}_{$field->name});\n";
                }
            }
        }

        $content = str_replace('{{DB_TABLE}}', $dbTable, $content);
        $content = str_replace('{{SCREEN_DATA_PROCESSING}}', $screenDataProcessing, $content);
        $content = str_replace('{{GENERAL_DATA_PROCESSING}}', $generalDataProcessing, $content);
        $content = str_replace('{{COLUMN_TOTALS}}', $columnTotals, $content);


        return $content;
    }

    private function getForeignDropdowns()
    {
        return "[]";
    }

    private function getFormFields()
    {
        $fields = '';
        foreach ($this->formFields as $field) {
            $fields .= $this->getFieldHtml($field);
        }

        return $fields;
    }

    private function getFieldHtml($field)
    {
        $html = '';
        $fieldName = $field->name;
        $fieldLable = printable($fieldName);
        $fieldType = $field->type;
        $required = "";
        $requiredLabel = "";

        if ($field->nullable == 0) {
            $required = "required";
            $requiredLabel = "<span class='text-danger'> *</span>";
        }

        if (in_array($fieldName, $this->formIgnoreList)) {
            return '';
        }

        if ($fieldName == $this->primaryField) {
            return '';
        }

        // first prepare dropdown for foreign keys tables.
        if (array_key_exists($fieldName, $this->foreignKeys)) {
            $foreignTable = $this->foreignKeys[$fieldName]['table'];
            $foreignColumn = $this->foreignKeys[$fieldName]['column'];
            $foreignNameField = $this->foreignKeys[$fieldName]['nameField'];

            $data = $this->db->table($foreignTable)->select("{$foreignColumn} as id, {$foreignNameField} as text")->get()->getResult();

            $html = "<div class='col-md-3 form-group mt-1'>
                        <div class='form-group'>
                            <label for='{$fieldName}'>{$fieldLable}$requiredLabel</label>";
            $html .= "<select class='form-control' name='{$fieldName}' id='{$fieldName}' $required>";
            foreach ($data as $row) {
                $html .= "<option value='{$row->id}'>" . printable($row->text) . "</option>";
            }
            $html .= "</select>
                        </div>
                    </div>";
        } else if (in_array($fieldType, ['int', 'bigint', 'decimal', 'float', 'smallint'])) {
            $html = "<div class='col-md-3 form-group mt-1'>
                        <div class='form-group'>
                            <label for='{$fieldName}'>{$fieldLable}$requiredLabel</label>
                            <input type='text' class='form-control numberInput' maxlength='$field->max_length' name='{$fieldName}' id='{$fieldName}' $required>
                        </div>
                    </div>";
        } else if ($fieldType == 'varchar' and $field->max_length < 100) {
            $html = "<div class='col-md-3 form-group mt-1'>
                        <div class='form-group'>
                            <label for='{$fieldName}'>{$fieldLable}$requiredLabel</label>
                            <input type='text' class='form-control' name='{$fieldName}' id='{$fieldName}' maxlength='$field->max_length' $required>
                        </div>
                    </div>";
        } else if (($fieldType == 'varchar' and $field->max_length > 100) or $fieldType == 'text') {
            $html = "<div class='col-md-3 form-group mt-1'>
                        <div class='form-group'>
                            <label for='{$fieldName}'>{$fieldLable}$requiredLabel</label>
                            <textarea class='form-control' name='{$fieldName}' id='{$fieldName}' maxlength='$field->max_length' $required></textarea>
                        </div>
                    </div>";
        } else if ($fieldType == 'date') {
            $html = "<div class='col-md-3 form-group mt-1'>
                        <div class='form-group'>
                            <label for='{$fieldName}'>{$fieldLable}$requiredLabel</label>
                            <input type='text' class='form-control datePicker' name='{$fieldName}' id='{$fieldName}' $required>
                        </div>
                    </div>";
        } else if ($fieldType == 'datetime') {
            $html = "<div class='col-md-3 form-group mt-1'>
                        <div class='form-group'>
                            <label for='{$fieldName}'>{$fieldLable}$requiredLabel</label>
                            <input type='text' class='form-control dateTimePicker' name='{$fieldName}' id='{$fieldName}' $required>
                        </div>
                    </div>";
        } else if ($fieldType == 'time') {
            $html = "<div class='col-md-3 form-group mt-1'>
                        <div class='form-group'>
                            <label for='{$fieldName}'>{$fieldLable}$requiredLabel</label>
                            <input type='text' class='form-control timePicker' name='{$fieldName}' id='{$fieldName}' $required>
                        </div>
                    </div>";
        } else if ($fieldType == 'enum') {

            $enumValues = $field->enumOptions;

            $html = "<div class='col-md-3 form-group mt-1'>
                        <div class='form-group'>
                            <label for='{$fieldName}'>{$fieldLable}$requiredLabel</label>";
            $html .= "<select class='form-control' name='{$fieldName}' id='{$fieldName}' $required>";
            foreach ($enumValues as $value) {
                $html .= "<option value='{$value}'>" . printable($value) . "</option>";
            }
            $html .= "</select>
                        </div>
                    </div>";
        }

        return $html;
    }

    private function getForeignKeys()
    {
        $query = "SELECT COLUMN_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE TABLE_NAME = '{$this->tableName}' AND REFERENCED_TABLE_NAME IS NOT NULL";
        $r =  $this->db->query($query)->getResultArray();

        $this->foreignKeys = [];
        foreach ($r as $row) {

            $foreignFields = $this->db->getFieldData($row['REFERENCED_TABLE_NAME']);
            $foreignNameField = "";
            foreach ($foreignFields as $field) {
                // if field contains name, title, or label, use it as the display field
                if (stripos($field->name, 'name') !== false || stripos($field->name, 'title') !== false || stripos($field->name, 'label') !== false) {
                    $foreignNameField = $field->name;
                    break;
                }
            }

            // if no name field found, use the first field
            if (!$foreignNameField) {
                $foreignNameField = $foreignFields[0]->name;
            }

            $this->foreignKeys[$row['COLUMN_NAME']] = [
                'table' => $row['REFERENCED_TABLE_NAME'],
                'column' => $row['REFERENCED_COLUMN_NAME'],
                'nameField' => $foreignNameField
            ];
        }
    }

    private function fieldExists($table, $fieldName)
    {
        $fields = $this->db->getFieldData($table);
        foreach ($fields as $field) {
            if ($field->name == $fieldName) {
                return true;
            }
        }

        return false;
    }

    private function prepareEnumFields()
    {
        $query = $this->db->query("SELECT COLUMN_NAME, COLUMN_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = '{$this->tableName}' AND COLUMN_TYPE LIKE 'enum(%'");
        $r = $query->getResultArray();

        foreach ($r as $row) {
            foreach ($this->formFields as $key => $field) {
                if ($field->name == $row['COLUMN_NAME']) {
                    //list enum options as array
                    preg_match_all("/'([^']+)'/", $row['COLUMN_TYPE'], $matches);
                    $this->formFields[$key]->enumOptions = $matches[1];
                }
            }
        }
    }

    private function showCompleted()
    {
        CLI::write("\nProcessing, please wait...\n", 'yellow');

        // Dummy progress bar for 3 seconds
        $totalSteps = 100; // Adjust for duration
        for ($i = 1; $i <= $totalSteps; $i++) {
            CLI::showProgress($i, $totalSteps);
            usleep(20000); // 150ms delay per step (~3s total)
        }
        CLI::showProgress(false); // Hide progress bar

        CLI::newLine();
        CLI::write("
    ██████╗░░█████╗░███╗░░██╗███████╗██╗
    ██╔══██╗██╔══██╗████╗░██║██╔════╝██║
    ██║░░██║██║░░██║██╔██╗██║█████╗░░██║
    ██║░░██║██║░░██║██║╚████║██╔══╝░░╚═╝
    ██████╔╝╚█████╔╝██║░╚███║███████╗██╗
    ╚═════╝░░╚════╝░╚═╝░░╚══╝╚══════╝╚═╝", 'green');

        $addUrl = base_url($this->moduleName . "/add{$this->itemName}");
        $manageUrl = base_url($this->moduleName . "/manage{$this->itemName}");

        CLI::write("\n" . str_repeat('-', 80), 'green');
        CLI::write("Frontend and Backend modules for {$this->moduleName} created successfully!", 'green');
        CLI::write("Visit any of following url for testing!", 'green');
        CLI::write("Add URL: {$addUrl}", 'yellow');
        CLI::write("Manage URL: {$manageUrl}", 'yellow');
        CLI::write(str_repeat('-', 80) . "\n", 'green');
    }

    private function showIntro($title, $description)
    {
        CLI::write("
    ░██╗░░░░░░░██╗███████╗██╗░░░░░░█████╗░░█████╗░███╗░░░███╗███████╗
    ░██║░░██╗░░██║██╔════╝██║░░░░░██╔══██╗██╔══██╗████╗░████║██╔════╝
    ░╚██╗████╗██╔╝█████╗░░██║░░░░░██║░░╚═╝██║░░██║██╔████╔██║█████╗░░
    ░░████╔═████║░██╔══╝░░██║░░░░░██║░░██╗██║░░██║██║╚██╔╝██║██╔══╝░░
    ░░╚██╔╝░╚██╔╝░███████╗███████╗╚█████╔╝╚█████╔╝██║░╚═╝░██║███████╗
    ░░░╚═╝░░░╚═╝░░╚══════╝╚══════╝░╚════╝░░╚════╝░╚═╝░░░░░╚═╝╚══════╝", "cyan");
        CLI::newLine();
        CLI::write(str_repeat('=', 80), 'green');
        CLI::write("📌  " . strtoupper($title), 'cyan'); // Title in cyan
        CLI::write(str_repeat('-', 80), 'green');
        CLI::write($description, 'yellow'); // Description in yellow
        CLI::write(str_repeat('=', 80), 'green');
        CLI::newLine();

        // Pause execution until the user presses Enter
        // CLI::prompt("Press Enter to continue...", null);
    }
}
