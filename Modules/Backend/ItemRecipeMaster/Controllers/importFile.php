<?php

namespace Modules\Backend\ItemRecipeMaster\Controllers;

use App\Libraries\UserPermissionLib;
use App\Controllers\ApiBaseController;
use Modules\Backend\ItemRecipeMaster\Models\ItemRecipeMasterModel;
use App\Libraries\RecipeParser;
use CodeIgniter\API\ResponseTrait;

class importFile extends ApiBaseController
{
    use ResponseTrait;


    protected $ItemRecipeMasterModel;

    public function __construct()
    {
        $this->ItemRecipeMasterModel = new ItemRecipeMasterModel();
    }

    public function save($itemRecipeId = 0)
    {

        if (!UserPermissionLib::userCanDo("ItemRecipeMaster", 'add')) {
            return $this->failForbidden('Insufficient permissions');
        }


        $input = $this->getInputData();
        $jsonInput = $input['jsonInput'];
        $files = $input['uploadedFiles'];

        $file = $files['importFile'] ?? null;

        $total = 1;
        $importedCount = 0;
        $failedCount = 0;

        if (is_array($file)) {
            $total = count($file);
            foreach ($file as $f) {
                if ($this->importMyFile($f)) {
                    $importedCount++;
                } else {
                    $failedCount++;
                }
            }
        } else {
            if ($this->importMyFile($file)) {
                $importedCount++;
            } else {
                $failedCount++;
            }
        }

        $message = "Total: $total, Imported: $importedCount, Failed: $failedCount";

        if ($importedCount > 0) {
            return $this->respondCreated(['status' => true, 'message' => $message]);
        }

        return $this->respondCreated(['status' => false, 'message' => $message]);
    }

    private function importMyFile($file)
    {
        // Implement your file import logic here
        try {
            // Read content straight from PHP temp path (no move/save)
            // Option A (fast): 
            $content = file_get_contents($file->getTempName());
            // Option B (stream-safe):
            // $content = $file->getStream()->getContents();

            // Optional extra: normalize to UTF-8 if needed
            // $content = mb_convert_encoding($content, 'UTF-8', 'UTF-8, ISO-8859-1, ASCII');

            $originalName = $file->getClientName(); // e.g. "mydocument.pdf"
            $nameWithoutExt = pathinfo($originalName, PATHINFO_FILENAME);
            $itemCode = $nameWithoutExt;


            $parser = new \App\Libraries\RecipeParser();
            $parsed = $parser->parseDatContent($content, $itemCode);

            // debug($parsed);
            // die();

            $meta = $parsed['meta'] ?? [];
            //varify parsing 
            if (!isset($meta['TA_TB_equal']) || !$meta['TA_TB_equal']) {
                return false;
            }

            if (isset($meta['unparsed_lines']) && count($meta['unparsed_lines']) > 0) {
                return false;
            }


            $itemRecipe = $parsed['itemRecipe'];
            $itemRecipe['isActive'] = 1;
            $itemRecipe['createdBy'] = $this->user->userId;
            $itemRecipe['createdAt'] = date('Y-m-d H:i:s');

            $itemRecipeSteps = $parsed['steps'];

            $itemRecipeId = $this->ItemRecipeMasterModel->insert($itemRecipe);
            assignSerialNumber($this->user->tenantId, "itemRecipeMaster", "itemRecipeId", $itemRecipeId);
            $this->syncChildTable("itemRecipeSteps", "itemRecipeStepId", "itemRecipeId", $itemRecipeId, $itemRecipeSteps);

            return true;
        } catch (\Throwable $e) {
            return $this->response->setStatusCode(400)->setJSON(['ok' => false, 'error' => $e->getMessage()]);
        }
    }
}
